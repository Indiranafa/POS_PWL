<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\StokModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Penjualan',
            'list'  => ['Home', 'Penjualan']
        ];

        $page = (object)[
            'title' => 'Daftar penjualan'
        ];

        $activeMenu = 'penjualan';

        return view('penjualan.index', compact('breadcrumb', 'page', 'activeMenu'));
    }

    public function list(Request $request)
    {
        $penjualan = PenjualanModel::with('user')->select('t_penjualan.*');

        return DataTables::of($penjualan)
            ->addIndexColumn()
            ->addColumn('user_name', function ($penjualan) {
                return $penjualan->user ? $penjualan->user->nama : '-';
            })
            ->addColumn('aksi', function ($penjualan) {
                $btn  = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show_ajax(string $id)
    {
        $penjualan = PenjualanModel::with(['detail.barang', 'user'])->find($id);

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan'
            ], 404);
        }

        return view('penjualan.show_ajax', compact('penjualan'));
    }


    public function create_ajax(Request $request)
    {
        $barang = BarangModel::all();
        return view('penjualan.create_ajax', compact('barang'));
    }

    public function store_ajax(Request $request)
    {
    $request->validate([
        'pembeli' => 'required|string|max:100',
        'penjualan_tanggal' => 'required|date',
        'barang_id' => 'required|array',
        'barang_id.*' => 'required|exists:m_barang,barang_id',
        'jumlah' => 'required|array',
        'jumlah.*' => 'required|integer|min:1'
    ]);

    $tanggalLengkap = Carbon::parse($request->penjualan_tanggal)
        ->setTimeFromTimeString(now()->format('H:i:s'));

    DB::beginTransaction();
    try {
        $penjualan = PenjualanModel::create([
            'pembeli' => $request->pembeli,
            'penjualan_kode' => 'PJ' . time(),
            'penjualan_tanggal' => $tanggalLengkap,
            'created_at' => now(),
            'user_id' => auth()->id(),
        ]);

        foreach ($request->barang_id as $i => $barang_id) {
            $barang = BarangModel::findOrFail($barang_id);

            // Ambil total stok dari tabel stok
            $stokTersedia = StokModel::where('barang_id', $barang_id)->sum('stok_jumlah');

            if ($stokTersedia < $request->jumlah[$i]) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => "Stok barang '{$barang->barang_nama}' tidak mencukupi! (Tersedia: $stokTersedia, Diminta: {$request->jumlah[$i]})"
                ], 422);
            }


            // Cari entri stok terakhir untuk barang ini
            $latestStok = StokModel::where('barang_id', $barang_id)
                ->latest('stok_tanggal')
                ->first();

            if ($latestStok && $latestStok->stok_jumlah > 0) {
                if ($request->jumlah[$i] <= $latestStok->stok_jumlah) {
                    $latestStok->update([
                        'stok_jumlah' => $latestStok->stok_jumlah - $request->jumlah[$i],
                        'stok_tanggal' => now()
                    ]);
                } else {
                    $latestStok->update([
                        'stok_jumlah' => 0,
                        'stok_tanggal' => now()
                    ]);

                    $sisaPengurangan = $request->jumlah[$i] - $latestStok->stok_jumlah;
                    $this->reduceRemainingStock($barang_id, $sisaPengurangan);
                }
            } else {
                StokModel::create([
                    'barang_id' => $barang_id,
                    'stok_jumlah' => -$request->jumlah[$i],
                    'stok_tanggal' => now(),
                    'user_id' => auth()->id()
                ]);
            }

            // Simpan detail penjualan dengan harga satuan
            PenjualanDetailModel::create([
                'penjualan_id' => $penjualan->penjualan_id,
                'barang_id' => $barang_id,
                'harga' => $barang->harga_jual,
                'jumlah' => $request->jumlah[$i],
                'created_at'  => now(),
            ]);
        }

        DB::commit();
        return response()->json([
            'status' => true,
            'message' => 'Penjualan berhasil disimpan.'
        ]);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Gagal menyimpan penjualan: ' . $e->getMessage()
        ], 500);
    }
}

    private function reduceRemainingStock($barang_id, $amount)
    {
        // Dapatkan stok positif yang tersisa, urutkan dari yang terbaru
        $remainingStocks = StokModel::where('barang_id', $barang_id)
            ->where('stok_jumlah', '>', 0)
            ->orderBy('stok_tanggal', 'desc')
            ->get();

        foreach ($remainingStocks as $stock) {
            if ($amount <= 0) break;

            if ($stock->stok_jumlah <= $amount) {
                // Kurangi seluruh stok ini
                $pengurangan = $stock->stok_jumlah;
                $stock->update([
                    'stok_jumlah' => 0,
                    'stok_tanggal' => now()
                ]);
                $amount -= $pengurangan;
            } else {
                // Kurangi sebagian
                $stock->update([
                    'stok_jumlah' => $stock->stok_jumlah - $amount,
                    'stok_tanggal' => now()
                ]);
                $amount = 0;
            }
        }

        // Jika masih ada sisa pengurangan, buat catatan negatif
        if ($amount > 0) {
            StokModel::create([
                'barang_id' => $barang_id,
                'stok_jumlah' => -$amount,
                'stok_tanggal' => now(),
                'user_id' => auth()->id()
            ]);
        }
    }

    public function confirm_ajax($id)
    {
        $penjualan = PenjualanModel::find($id);
        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $penjualan = PenjualanModel::find($id);

            if ($penjualan) {
                try {
                    $penjualan->delete();
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data penjualan berhasil dihapus'
                    ]);
                } catch (\Throwable $th) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data gagal dihapus'
                    ]);
                }
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function destroy($id)
    {
        $penjualan = PenjualanModel::find($id);

        if (!$penjualan) {
            return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
        }

        try {
            $penjualan->delete();
            return redirect('/penjualan')->with('success', 'Data penjualan berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/penjualan')->with('error', 'Data penjualan gagal dihapus karena masih terkait dengan data lain');
        }
    }

    public function import()
    {
        return view('penjualan.import');
    }

    //import ajax
    public function import_ajax(Request $request) 
{ 
    if($request->ajax() || $request->wantsJson()){ 
        // Validasi file yang di-upload harus xlsx dan ukuran maksimal 1MB
        $rules = [ 
            'file_penjualan' => ['required', 'mimes:xlsx', 'max:10485760'] 
        ]; 

        $validator = Validator::make($request->all(), $rules); 

        if($validator->fails()){ 
            return response()->json([ 
                'status' => false, 
                'message' => 'Validasi Gagal', 
                'msgField' => $validator->errors() 
            ]); 
        } 

        // Ambil file yang di-upload
        $file = $request->file('file_penjualan');  

        // Memastikan file Excel dibaca dengan benar
        $reader = IOFactory::createReader('Xlsx');  
        $reader->setReadDataOnly(true);  // Hanya membaca data tanpa format
        $spreadsheet = $reader->load($file->getRealPath()); // Load file Excel
        $sheet = $spreadsheet->getActiveSheet();    // Ambil sheet aktif

        // Mengambil data dari sheet
        $data = $sheet->toArray(null, false, true, true);   // Konversi sheet ke array

        // Menyiapkan array untuk insert
        $insert = []; 
        if(count($data) > 1){ // Jika data lebih dari 1 baris
            foreach ($data as $baris => $value) { 
                if($baris > 1){ // Baris pertama adalah header, jadi lewati
                    $insert[] = [
                        'barang_id' => $value['A'],  // Kolom A = barang_id
                        'user_id' => $value['B'],    // Kolom B = user_id
                        'penjualan_tanggal' => $value['C'],  // Kolom C = tanggal penjualan
                        'penjualan_jumlah'  => $value['D'],  // Kolom D = jumlah penjualan
                        'created_at'  => now(),  // Waktu pembuatan data
                    ]; 
                } 
            } 

            if(count($insert) > 0){ 
                // Insert data ke database, jika data sudah ada, maka diabaikan
                PenjualanModel::insertOrIgnore($insert);    
            }

            return response()->json([ 
                'status' => true, 
                'message' => 'Data berhasil diimport' 
            ]); 
        }else{ 
            return response()->json([ 
                'status' => false, 
                'message' => 'Tidak ada data yang diimport' 
            ]); 
        } 
    } 
    return redirect('/'); 
}


public function export_excel()
{
    // Ambil semua data penjualan beserta relasinya
    $penjualan = PenjualanModel::with(['detail.barang', 'user'])
        ->orderBy('penjualan_tanggal', 'desc')
        ->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header kolom
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Nama Barang');
    $sheet->setCellValue('C1', 'Kode Penjualan');
    $sheet->setCellValue('D1', 'Tanggal Penjualan');
    $sheet->setCellValue('E1', 'Jumlah');
    $sheet->setCellValue('F1', 'Harga');
    $sheet->setCellValue('G1', 'Yang Mencatat');

    $sheet->getStyle('A1:G1')->getFont()->setBold(true);

    // Isi data
    $no = 1;
    $row = 2;

    foreach ($penjualan as $item) {
        if ($item->detail && $item->detail->count() > 0) {
            foreach ($item->detail as $detail) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $detail->barang->barang_nama ?? '-');
                $sheet->setCellValue('C' . $row, $item->penjualan_kode);
                $sheet->setCellValue('D' . $row, Carbon::parse($item->penjualan_tanggal)->format('d-m-Y'));
                $sheet->setCellValue('E' . $row, $detail->jumlah ?? 0);
                $sheet->setCellValue('F' . $row, $detail->harga ?? 0);
                $sheet->setCellValue('G' . $row, $item->user->nama ?? '-');
                $row++;
            }
        }
    }

    // Auto resize kolom
    foreach (range('A', 'G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Judul sheet
    $sheet->setTitle('Data Penjualan');

    // Buat writer
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

    // Nama file
    $filename = 'Data_Penjualan_' . now()->format('Ymd_His') . '.xlsx';

    // Output file ke browser untuk diunduh
    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Cache-Control' => 'max-age=0',
    ]);
}
    public function export_pdf()
    {
        // Ambil data stok
        $penjualan = PenjualanModel::with('detail.barang', 'user')
            ->orderBy('penjualan_tanggal', 'desc')
            ->get();

        // Muat view export PDF (sesuaikan nama file view jika diperlukan)
        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualan' => $penjualan]);

        $pdf->setPaper('a4', 'portrait');       // Set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // Aktifkan remote jika ada gambar dari URL

        return $pdf->stream('Data Penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }
}

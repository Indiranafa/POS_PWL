<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\SupplierModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Stok',
            'list'  => ['Home', 'Stok']
        ];

        $page = (object)[
            'title' => 'Daftar stok dalam sistem'
        ];

        $activeMenu = 'stok';

        $barang = BarangModel::all();

        return view('stok.index', compact('breadcrumb', 'page', 'activeMenu', 'barang'));
    }

    public function list(Request $request)
    {
        $stoks = StokModel::with(['supplier', 'user', 'barang'])->select('t_stok.*');

        if ($request->barang_id) {
            $stoks->where('barang_id', $request->barang_id);
        }

        return DataTables::of($stoks)
            ->addIndexColumn()
            ->editColumn('stok_tanggal', function ($stok) {
                return \Carbon\Carbon::parse($stok->stok_tanggal)->format('Y-m-d');
            })
            ->addColumn('supplier_nama', function ($stok) {
                return $stok->supplier ? $stok->supplier->supplier_nama : '-';
            })
            ->addColumn('user_nama', function ($stok) {
                return $stok->user ? $stok->user->nama : '-';
            })
            ->addColumn('barang_nama', function ($stok) {
                return $stok->barang ? $stok->barang->barang_nama : '-';
            })
            ->addColumn('aksi', function ($stok) {
                $btn  = '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                // $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stok->stok_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function create_ajax()
    {
        $suppliers = SupplierModel::all();
        $users = UserModel::all();
        $barangs = BarangModel::all();
        return view('stok.create_ajax', compact('suppliers', 'users', 'barangs'));
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_id'  => 'required|exists:m_supplier,supplier_id',
                'barang_id'    => 'required|exists:m_barang,barang_id',
                'stok_tanggal' => 'required|date',
                'stok_jumlah'  => 'required|integer'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            StokModel::create([
                'supplier_id'  => $request->supplier_id,
                          'user_id' => auth()->id(),
                'barang_id'    => $request->barang_id,
                'stok_tanggal' => $request->stok_tanggal,
                'stok_jumlah'  => $request->stok_jumlah
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Data stok berhasil disimpan.'
            ]);
        }
        return redirect('/');
    }

    public function edit_ajax($id)
    {
        $stok = StokModel::find($id);
        if (!$stok) {
            return view('stok.edit_ajax')->with('error', 'Data stok tidak ditemukan');
        }
        $suppliers = SupplierModel::all();
        $users     = UserModel::all();
        $barangs   = BarangModel::all();

        return view('stok.edit_ajax', compact('stok', 'suppliers', 'users', 'barangs'));
    }

    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_id'  => 'required|exists:m_supplier,supplier_id',
                'user_id'      => 'required|exists:m_user,user_id',
                'barang_id'    => 'required|exists:m_barang,barang_id',
                'stok_tanggal' => 'required|date',
                'stok_jumlah'  => 'required|integer'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $stok = StokModel::find($id);
            if ($stok) {
                $stok->update([
                    'supplier_id'  => $request->supplier_id,
                    'user_id'      => $request->user_id,
                    'barang_id'    => $request->barang_id,
                    'stok_tanggal' => $request->stok_tanggal,
                    'stok_jumlah'  => $request->stok_jumlah
                ]);

                return response()->json([
                    'status'  => true,
                    'message' => 'Data stok berhasil diperbarui.'
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data stok tidak ditemukan.'
                ]);
            }
        }
        return redirect('/');
    }

    public function show_ajax(string $id)
    {
        $stok = StokModel::with('supplier', 'user', 'barang')->find($id);

        return view('stok.show_ajax', compact('stok'));
    }

    public function confirm_ajax($id)
    {
        $stok = StokModel::find($id);
        return view('stok.confirm_ajax', compact('stok'));
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $stok = StokModel::find($id);
            if ($stok) {
                try {
                    $stok->delete();
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data stok berhasil dihapus.'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Data stok gagal dihapus karena masih terkait dengan data lain.'
                    ]);
                }
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data stok tidak ditemukan.'
                ]);
            }
        }
        return redirect('/');
    }

    public function import()
    {
        return view('stok.import');
    }

    //import ajax
    public function import_ajax(Request $request) 
    { 
       if($request->ajax() || $request->wantsJson()){ 
           $rules = [ 
               // validasi file harus xls atau xlsx, max 1MB 
               'file_stok' => ['required', 'mimes:xlsx', 'max:1024'] 
           ]; 

           $validator = Validator::make($request->all(), $rules); 
           if($validator->fails()){ 
               return response()->json([ 
                   'status' => false, 
                   'message' => 'Validasi Gagal', 
                   'msgField' => $validator->errors() 
               ]); 
           } 
           
           $file = $request->file('file_stok');  // ambil file dari request 

           $reader = IOFactory::createReader('Xlsx');  // load reader file excel 
           $reader->setReadDataOnly(true);             // hanya membaca data 
           $spreadsheet = $reader->load($file->getRealPath()); // load file excel 
           $sheet = $spreadsheet->getActiveSheet();    // ambil sheet yang aktif 

           $data = $sheet->toArray(null, false, true, true);   // ambil data excel 

           $insert = []; 
           if(count($data) > 1){ // jika data lebih dari 1 baris 
               foreach ($data as $baris => $value) { 
                   if($baris > 1){ // baris ke 1 adalah header, maka lewati 
                       $insert[] = [
                            'barang_id' => $value['A'],
                            'user_id' => $value['B'],
                            'stok_tanggal' => $value['C'],
                            'stok_jumlah'  => $value['D'],
                            'created_at'  => now(), 
                       ]; 
                   } 
               } 

               if(count($insert) > 0){ 
                   // insert data ke database, jika data sudah ada, maka diabaikan 
                   StokModel::insertOrIgnore($insert);    
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
        // Ambil data stok yang akan diexport
        $stok = StokModel::with(['barang', 'user'])
            ->orderBy('stok_tanggal', 'desc')
            ->get();

        // Buat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header kolom (baris pertama)
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Tanggal Stok');
        $sheet->setCellValue('D1', 'Jumlah');
        $sheet->setCellValue('E1', 'Yang Mencatat');

        // Buat header bold
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        // Isi data stok
        $no = 1;
        $row = 2;
        foreach ($stok as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->barang->barang_nama ?? '-');
            $sheet->setCellValue('C' . $row, $item->stok_tanggal);
            $sheet->setCellValue('D' . $row, $item->stok_jumlah);
            $sheet->setCellValue('E' . $row, $item->user->nama ?? '-');

            $no++;
            $row++;
        }

        // Set auto-size untuk setiap kolom
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Beri judul sheet
        $sheet->setTitle('Data stok');

        // Buat writer untuk file Excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data stok ' . date('Y-m-d H:i:s') . '.xlsx';

        // Atur header untuk file download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        // Output ke browser
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        // Ambil data stok
        $stok = StokModel::select('barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->orderBy('stok_tanggal', 'desc')
            ->with(['barang.kategori', 'user'])
            ->get();

        // Muat view export PDF (sesuaikan nama file view jika diperlukan)
        $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);

        $pdf->setPaper('a4', 'portrait');       // Set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // Aktifkan remote jika ada gambar dari URL

        return $pdf->stream('Data stok ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
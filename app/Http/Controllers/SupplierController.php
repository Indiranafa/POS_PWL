<?php

namespace App\Http\Controllers;

use App\Models\SupplierModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Exception;

class SupplierController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Supplier',
            'list'  => ['Home', 'Supplier']
        ];

        $page = (object) [
            'title' => 'Daftar supplier yang terdaftar dalam sistem'
        ];

        $activeMenu = 'supplier';

        return view('supplier.index', [
            'breadcrumb' => $breadcrumb,
            'page'       => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $supplier = SupplierModel::select('supplier_id', 'supplier_kode', 'supplier_nama', 'supplier_alamat');
        return DataTables::of($supplier)
            ->addIndexColumn()
            ->addColumn('aksi', function ($supplier) {
                $btn = '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/supplier/' . $supplier->supplier_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Supplier',
            'list'  => ['Home', 'Supplier', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah supplier baru'
        ];

        $activeMenu = 'supplier';

        return view('supplier.create', [
            'breadcrumb' => $breadcrumb,
            'page'       => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_kode' => 'required|string|min:2|unique:m_supplier,supplier_kode',
            'supplier_nama' => 'required|string|max:100',
            'supplier_alamat' => 'required|string|max:255'
        ]);

        SupplierModel::create([
            'supplier_kode' => $request->supplier_kode,
            'supplier_nama' => $request->supplier_nama,
            'supplier_alamat' => $request->supplier_alamat
        ]);

        return redirect('/supplier')->with('success', 'Data supplier berhasil disimpan');
    }

    public function edit(string $id)
    {
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        $breadcrumb = (object) [
            'title' => 'Edit Supplier',
            'list' => ['Home', 'Supplier', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Supplier'
        ];

        $activeMenu = 'supplier';

        return view('supplier.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'supplier' => $supplier,
            'activeMenu' => $activeMenu
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'supplier_kode' => 'required|string|min:2|unique:m_supplier,supplier_kode,' . $id . ',supplier_id',
            'supplier_nama' => 'required|string|max:100',
            'supplier_alamat' => 'required|string|max:255'
        ]);

        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        $supplier->update([
            'supplier_kode' => $request->supplier_kode,
            'supplier_nama' => $request->supplier_nama,
            'supplier_alamat' => $request->supplier_alamat
        ]);

        return redirect('/supplier')->with('success', 'Data supplier berhasil diubah');
    }

    public function destroy(string $id)
    {
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        try {
            SupplierModel::destroy($id);
            return redirect('/supplier')->with('success', 'Data supplier berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/supplier')->with('error', 'Data supplier gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        return view('supplier.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        try {
            if ($request->ajax() || $request->wantsJson()) {
                $rules = [
                    'supplier_kode' => 'required|string|min:3|max:10|unique:m_supplier,supplier_kode',
                    'supplier_nama' => 'required|string|min:3|max:100',
                    'supplier_alamat' => 'required|string|max:255',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Validasi Gagal',
                        'msgField' => $validator->errors(),
                    ], 422);
                }

                // Simpan data ke database
                SupplierModel::create([
                    'supplier_kode' => $request->supplier_kode,
                    'supplier_nama' => $request->supplier_nama,
                    'supplier_alamat' => $request->supplier_alamat,
                ]);

                return response()->json([
                    'status'  => true,
                    'message' => 'Data supplier berhasil disimpan'
                ], 201);
            }

            return redirect('/supplier');
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function edit_ajax(string $id)
    {
        // Cari supplier berdasarkan ID
        $supplier = SupplierModel::find($id);

        if (!$supplier) {
            return response()->json([
                'status' => false,
                'message' => 'Supplier tidak ditemukan'
            ]);
        }

        return view('supplier.edit_ajax', compact('supplier'));
    }

    public function update_ajax(Request $request, $id)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return response()->json([
                'status' => false,
                'message' => 'Permintaan tidak valid.'
            ], 400);
        }

        $rules = [
            'supplier_kode' => 'required|max:10|unique:m_supplier,supplier_kode,' . $id . ',supplier_id',
            'supplier_nama' => 'required|max:100',
            'supplier_alamat' => 'required|max:255'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'msgField' => $validator->errors()
            ], 422);
        }

        try {
            $supplier = SupplierModel::find($id);

            if ($supplier) {
                $supplier->update($request->only(['supplier_kode', 'supplier_nama', 'supplier_alamat']));

                return response()->json([
                    'status' => true,
                    'message' => 'Data supplier berhasil diperbarui'
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Data supplier tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function confirm_ajax(String $id)
    {
        $supplier = SupplierModel::find($id);

        return view('supplier.confirm_ajax', ['supplier' => $supplier]);
    }

    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $supplier = SupplierModel::find($id);

            if ($supplier) {
                try {
                    $supplier->delete();
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data supplier berhasil dihapus'
                    ]);
                } catch (\Throwable $th) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Data supplier gagal dihapus (terdapat relasi dengan tabel lain)'
                    ]);
                }
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data supplier tidak ditemukan'
                ]);
            }
        }

        return redirect('/supplier');
    }

    public function import()
    {
        return view('supplier.import');
    }

    public function import_ajax(Request $request) 
    { 
       if($request->ajax() || $request->wantsJson()){ 
           $rules = [ 
               // validasi file harus xls atau xlsx, max 1MB 
               'file_supplier' => ['required', 'mimes:xlsx', 'max:1024'] 
           ]; 

           $validator = Validator::make($request->all(), $rules); 
           if($validator->fails()){ 
               return response()->json([ 
                   'status' => false, 
                   'message' => 'Validasi Gagal', 
                   'msgField' => $validator->errors() 
               ]); 
           } 
           
           $file = $request->file('file_supplier');  // ambil file dari request 

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
                            'supplier_kode' => $value['A'], 
                            'supplier_nama' => $value['B'],
                            'supplier_alamat' => $value['C'],
                            'created_at' => now(),
                            'updated_at' => now() 
                       ]; 
                   } 
               } 

               if(count($insert) > 0){ 
                   // insert data ke database, jika data sudah ada, maka diabaikan 
                   SupplierModel::insertOrIgnore($insert);    
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
        // Ambil data Supplier yang akan diexport
        $Supplier = SupplierModel::select('supplier_kode', 'supplier_nama', 'supplier_alamat')
            ->orderBy('supplier_kode', 'ASC')
            ->get();

        // Buat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Supplier');
        $sheet->setCellValue('C1', 'Nama Supplier');
        $sheet->setCellValue('D1', 'Alamat Supplier');

        // Buat header bold
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        // Isi data
        $no = 1;
        $row = 2;
        foreach ($Supplier as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->supplier_kode);
            $sheet->setCellValue('C' . $row, $item->supplier_nama);
            $sheet->setCellValue('D' . $row, $item->supplier_alamat);

            $no++;
            $row++;
        }

        // Set auto size untuk kolom A sampai C
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul sheet
        $sheet->setTitle('Data Supplier');

        // Buat writer untuk file Excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Supplier ' . date('Y-m-d H:i:s') . '.xlsx';

        // Set header untuk file download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        // Output file ke browser
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        // Ambil data Supplier yang akan diexport
        $supplier = SupplierModel::select('Supplier_kode', 'Supplier_nama')
            ->orderBy('supplier_kode', 'ASC')
            ->get();

        // Muat view export PDF (sesuaikan nama file view jika diperlukan)
        $pdf = Pdf::loadView('supplier.export_pdf', ['supplier' => $supplier]);

        $pdf->setPaper('a4', 'portrait');       // Set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // Aktifkan remote jika ada gambar dari URL

        return $pdf->stream('Data Supplier ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
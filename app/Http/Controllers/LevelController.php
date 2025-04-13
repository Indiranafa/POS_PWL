<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class LevelController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Daftar Level',
            'list' => ['Home', 'Level']
        ];

        $page = (object)[
            'title' => 'Daftar level dalam sistem'
        ];

        $activeMenu = 'level';

        $levels = LevelModel::all();

        return view('level.index', compact('breadcrumb', 'page', 'activeMenu', 'levels'));
    }

    public function list(Request $request)
    {
        $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');

        if ($request->level_id) {
            $levels->where('level_id', $request->level_id);
        }

        return DataTables::of($levels)
            ->addIndexColumn()
            ->addColumn('aksi', function ($level) {
                $btn = '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create_ajax()
    {
        return view('level.create_ajax');
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required|string|max:50|unique:m_level,level_kode',
                'level_nama' => 'required|string|max:100|unique:m_level,level_nama'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            LevelModel::create([
                'level_kode' => $request->level_kode,
                'level_nama' => $request->level_nama
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data level berhasil disimpan.'
            ]);
        }

        return redirect('/');
    }

    public function edit_ajax(string $id)
    {
        $level = LevelModel::find($id);
        return view('level.edit_ajax', compact('level'));
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required|string|max:50|unique:m_level,level_kode,' . $id . ',level_id',
                'level_nama' => 'required|string|max:100|unique:m_level,level_nama,' . $id . ',level_id'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $level = LevelModel::find($id);
            if ($level) {
                $level->update([
                    'level_kode' => $request->level_kode,
                    'level_nama' => $request->level_nama
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Data level berhasil diperbarui'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data level tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $level = LevelModel::find($id);
        return view('level.confirm_ajax', compact('level'));
    }

    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $level = LevelModel::find($id);
            if ($level) {
                try {
                    $level->delete();
                    return response()->json([
                        'status' => true,
                        'message' => 'Data level berhasil dihapus'
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data level gagal dihapus karena masih terkait dengan data lain'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data level tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function import()
    {
        return view('level.import');
    }

    public function import_ajax(Request $request) 
    { 
       if($request->ajax() || $request->wantsJson()){ 
           $rules = [ 
               // validasi file harus xls atau xlsx, max 1MB 
               'file_level' => ['required', 'mimes:xlsx', 'max:1024'] 
           ]; 

           $validator = Validator::make($request->all(), $rules); 
           if($validator->fails()){ 
               return response()->json([ 
                   'status' => false, 
                   'message' => 'Validasi Gagal', 
                   'msgField' => $validator->errors() 
               ]); 
           } 
           
           $file = $request->file('file_level');  // ambil file dari request 

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
                            'level_id' =>$value['A'],
                           'level_kode' => $value['B'], 
                           'level_nama' => $value['C'], 
                           'created_at' => now(),
                           'updated_at' => now() 
                       ]; 
                   } 
               } 

               if(count($insert) > 0){ 
                   // insert data ke database, jika data sudah ada, maka diabaikan 
                   LevelModel::insertOrIgnore($insert);    
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
        // Ambil data level yang akan diexport
        $level = LevelModel::select('level_kode', 'level_nama')
            ->orderBy('level_kode', 'ASC')
            ->get();

        // Buat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Level');
        $sheet->setCellValue('C1', 'Nama Level');

        // Buat header bold
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        // Isi data
        $no = 1;
        $row = 2;
        foreach ($level as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->level_kode);
            $sheet->setCellValue('C' . $row, $item->level_nama);

            $no++;
            $row++;
        }

        // Set auto size untuk kolom A sampai C
        foreach (range('A', 'C') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul sheet
        $sheet->setTitle('Data Level');

        // Buat writer untuk file Excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Level ' . date('Y-m-d H:i:s') . '.xlsx';

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
        // Ambil data level yang akan diexport
        $level = LevelModel::select('level_kode', 'level_nama')
            ->orderBy('level_kode', 'ASC')
            ->get();

        // Muat view export PDF (sesuaikan nama file view jika diperlukan)
        $pdf = Pdf::loadView('level.export_pdf', ['level' => $level]);

        $pdf->setPaper('a4', 'portrait');       // Set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); // Aktifkan remote jika ada gambar dari URL

        return $pdf->stream('Data Level ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
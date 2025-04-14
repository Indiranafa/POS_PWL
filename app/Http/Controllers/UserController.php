<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

use function Laravel\Prompts\password;

class UserController extends Controller
{
    // Menampilkan halaman awal user
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar User',
            'list' => ['Home', 'User']
        ];

        $page = (object) [
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];

        $activeMenu = 'user'; // set menu yang sedang aktif

        $level = LevelModel::all();

        return view('user.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    // Ambil data user dalam bentuk json untuk datatables
    public function list(Request $request)
    {
        $users = UserModel::select('user_id', 'username', 'nama', 'level_id')
        ->with('level');
        // Filter data user berdasarkan level_id
        if ($request->level_id){
            $users->where('level_id',$request->level_id);
        }

        return DataTables::of($users)
        ->addIndexColumn() // menambahkan kolom index / no urut (default nama kolom:DT_RowIndex)
        ->addColumn('aksi', function ($user) { // menambahkan kolom aksi

        /* $btn = '<a href="'.url('/user/' . $user->user_id).'" class="btn btn-info btnsm">Detail</a> ';
        $btn .= '<a href="'.url('/user/' . $user->user_id . '/edit').'" class="btn btnwarning btn-sm">Edit</a> ';
        $btn .= '<form class="d-inline-block" method="POST" action="'. url('/user/'.$user-
        >user_id).'">'
        . csrf_field() . method_field('DELETE') .
        '<button type="submit" class="btn btn-danger btn-sm" onclick="return
        confirm(\'Apakah Anda yakit menghapus data ini?\');">Hapus</button></form>';*/
        $btn = '<button onclick="modalAction(\''.url('/user/' . $user->user_id .
        '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> ';
        $btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id .
        '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
        $btn .= '<button onclick="modalAction(\''.url('/user/' . $user->user_id .
        '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button> ';
        return $btn;
        })
        ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
        ->make(true);
    }


    public function create(){
        $breadcrumb = (object)[
            'title' => 'Tambah User',
            'list'  => ['Home', 'User', 'Tambah']
        ];

        $page = (object)[
            'title' => 'Tambah user baru'
        ];

        $level = LevelModel::all();
        $activeMenu = 'user';

        return view('user.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function create_ajax(){
        $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('user.create_ajax')
                    ->with('level', $level);
    }

    public function store(Request $request){
        $request->validate([
            'username'  => 'required|string|min:3|unique:m_user,username',
            'nama'      => 'required|string|max:100',
            'password'  => 'required|min:5',
            'level_id'  => 'required|integer'
        ]);

        UserModel::create([
            'username' => $request->username,
            'nama'     => $request->nama,
            'password'  => bcrypt($request->password),
            'level_id'  => $request->level_id
        ]);
        return redirect('/user')->with('success', 'Data user berhasil disimpan');
    }

    public function store_ajax(Request $request){
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_id' => 'required|integer',
                'username' =>'required|string|min:3|unique:m_user,username',
                'nama'     => 'required|string|max:100',
                'password' => 'required|min:6'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            UserModel::create($request->all());
            return response()->json([
                'status'    => true,
                'message'   => 'Data user berhasil disimpan'
            ]);
        }
        redirect('/');
    }

    public function show(string $id){
        $user = UserModel::with('level')->find($id);

        $breadcrumb = (object)[
            'title' => 'Detail User',
            'list'  => ['Home', 'User', 'Detail']
        ];

        $page = (object)[
            'title' => 'Detail user'
        ];

        $activeMenu = 'user';
        return view('user.show', ['breadcrumb' => $breadcrumb,'page' => $page, 'user' => $user, 'activeMenu' => $activeMenu]);
    }

    public function show_ajax(string $id){
        $user = UserModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('user.show_ajax',['user' => $user, 'level' => $level]);
    }

    public function edit(string $id){
        $user = UserModel::find($id);
        $level = LevelModel::all();

        $breadcrumb = (object)[
            'title' => 'Edit User',
            'list'  => ['Home', 'User', 'Edit']
        ];

        $page = (object)[
            'title' => 'Edit user'
        ];

        $activeMenu = 'user';


        return view('user.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function edit_ajax(string $id){
        $user = UserModel::find($id);
        $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('user.edit_ajax',['user' => $user, 'level' => $level]);
    }

    public function update(Request $request, string $id){
        $request->validate([
            'username'  => 'required|string|min:3|unique:m_user,username,'.$id.',user_id',
            'nama'      => 'required|string|max:100',
            'password'  => 'nullable|min:5',
            'level_id'  => 'required|integer'
        ]);

        UserModel::find($id)->update([
            'username'  => $request->username,
            'nama'      => $request->nama,
            'password'  => $request->password ? bcrypt($request->password) : UserModel::find($id)->password,
            'level_id'  => $request->level_id
        ]);
        return redirect('/user')->with('success', 'Data user berhasil diubah');
    }

    public function update_ajax(Request $request, $id){
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
            'level_id' => 'required|integer',
            'username' => 'required|max:20|unique:m_user,username,'.$id.',user_id',
            'nama' => 'required|max:100',
            'password' => 'nullable|min:6|max:20'
            ];
            
            // use Illuminate\Support\Facades\Validator;
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false, // respon json, true: berhasil, false: gagal
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors() // menunjukkan field mana yang error
                ]);
            }

            $check = UserModel::find($id);
            if ($check) {
                if(!$request->filled('password') ){ // jika password tidak diisi, maka hapus dari request
                    $request->request->remove('password');
                }
                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else{
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/user');
    }

    public function destroy(string $id){
        $user = UserModel::find($id);
        if (!$user) {
            return redirect('/user')->with('error', 'Data user tidak ditemukan');
        }
        try {
            UserModel::destroy($id);
            return redirect('/user')->with('success', 'Data user berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/user')->with('error', 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');;
        }
    }

    public function confirm_ajax(string $id){
        $user = UserModel::find($id);

        return view('user.confirm_ajax', ['user' => $user]);
    }

    public function delete_ajax(Request $request, $id)
    {
        //cek apakah req dari ajax
        if($request->ajax() || $request->wantsJson()){
            $user = UserModel::find($id);
            if($user){
                try {
                    $user->delete();
                return response()->json([
                    'status'=> true,
                    'message'=> 'Data berhasil dihapus'
                ]);
                } catch (\Throwable $th) {
                return response()->json([
                    'status'=> false,
                    'message'=> 'Data tidak bisa dihapus'
                ]);
                }
                
            }else{
                return response()->json([
                    'status'=> false,
                    'message'=> 'Data tidak ditemuka'
                ]);
            }
        } 
        return redirect('/');   
    }

    public function import()
    {
        return view('user.import');
    }

    //import ajax
    public function import_ajax(Request $request) 
    { 
       if($request->ajax() || $request->wantsJson()){ 
           $rules = [ 
               // validasi file harus xls atau xlsx, max 1MB 
               'file_user' => ['required', 'mimes:xlsx', 'max:1024'] 
           ]; 

           $validator = Validator::make($request->all(), $rules); 
           if($validator->fails()){ 
               return response()->json([ 
                   'status' => false, 
                   'message' => 'Validasi Gagal', 
                   'msgField' => $validator->errors() 
               ]); 
           } 
           
           $file = $request->file('file_user'); // ambil file dari request 

           $reader = IOFactory::createReader('Xlsx'); // load reader file excel 
           $reader->setReadDataOnly(true); // hanya membaca data 
           $spreadsheet = $reader->load($file->getRealPath()); // load file excel 
           $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif 

           $data = $sheet->toArray(null, false, true, true); // ambil data excel 

           $insert = []; 
           if(count($data) > 1){ // jika data lebih dari 1 baris 
               foreach ($data as $baris => $value) { 
                   if($baris > 1){ // baris ke 1 adalah header, maka lewati 
                       $insert[] = [ 
                            'level_id' =>$value['A'],
                            'username' => $value['B'], 
                            'nama' => $value['C'], 
                            'created_at' => now(),
                            'updated_at' => now() 
                       ]; 
                   } 
               } 

               if(count($insert) > 0){ 
                   // insert data ke database, jika data sudah ada, maka diabaikan 
                   UserModel::insertOrIgnore($insert);    
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
        // Ambil data user dengan relasi level
        $users = UserModel::with('level')->orderBy('username', 'asc')->get();

        // Buat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Level');
        $sheet->setCellValue('C1', 'Username');
        $sheet->setCellValue('D1', 'Nama');

        // Buat header bold
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        // Isi data user
        $no = 1;
        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $no);
            // Jika relasi level tidak ada, tampilkan kosong
            $sheet->setCellValue('B' . $row, $user->level ? $user->level->level_nama : '');
            $sheet->setCellValue('C' . $row, $user->username);
            $sheet->setCellValue('D' . $row, $user->nama);
            $row++;
            $no++;
        }

        // Set auto-size untuk kolom A sampai D
        foreach (range('A', 'D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set judul sheet
        $sheet->setTitle('Data User');

        // Buat writer untuk file Excel
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data User ' . date('Y-m-d H:i:s') . '.xlsx';

        // Set header HTTP untuk file download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        // Output file Excel ke browser
        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        // Ambil data user beserta relasi level
        $users = UserModel::with('level')->orderBy('username', 'asc')->get();

        // Muat view export PDF dengan data user
        $pdf = Pdf::loadView('user.export_pdf', ['users' => $users]);

        // Atur ukuran kertas dan orientasi
        $pdf->setPaper('a4', 'portrait');
        // Aktifkan opsi remote jika ada gambar dari URL
        $pdf->setOption("isRemoteEnabled", true);

        return $pdf->stream('Data User ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
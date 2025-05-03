<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function index(){
        return BarangModel::all();
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'kategori_id'   => 'required|exists:m_kategori,kategori_id',
            'supplier_id'   => 'required|exists:m_supplier,supplier_id',
            'barang_kode'   => 'required',
            'barang_nama'   => 'required',
            'harga_beli'    => 'required|numeric',
            'harga_jual'    => 'required|numeric',
            'image'         => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Simpan gambar
        $imagePath = $request->file('image')->store('public/barang');
        $imageName = basename($imagePath);

        // Simpan data barang ke database
        $barang = BarangModel::create([
            'kategori_id'   => $request->kategori_id,
            'supplier_id'   => $request->supplier_id,
            'barang_kode'   => $request->barang_kode,
            'barang_nama'   => $request->barang_nama,
            'harga_beli'    => $request->harga_beli,
            'harga_jual'    => $request->harga_jual,
            'image'         => $imageName,
        ]);

        if($barang){
            return response()->json([
            'success' => true,
            'barang' => $barang,
            ], 201);
            }
            //return JSON process insert failed
            return response()->json([
            'success' => false,
            ], 409);
            
    }


    public function show(BarangModel $barang){
        return BarangModel::find($barang);
    }

    public function update(Request $request, BarangModel $barang){
        $barang->update($request->all());
        return BarangModel::find($barang);
    }

    public function destroy(BarangModel $barang){
        $barang->delete();
        return response()->json([
            'success'   => true,
            'message'   => 'Data berhasil dihapus'
        ]);
    }
}

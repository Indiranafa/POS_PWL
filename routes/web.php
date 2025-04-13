<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PenjualanDetailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::pattern('id','[0-9]+');

Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister']);

Route::get('login',[AuthController::class,'login'])->name('login');
Route::post('login',[AuthController::class,'postlogin']);
Route::get('logout',[AuthController::class,'logout'])->middleware('auth');

Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
Route::post('/profile/update-foto', [ProfileController::class, 'updateFoto'])->name('profile.update-foto');

Route::middleware(['auth'])->group(function(){
    Route::get('/', [WelcomeController::class, 'index']);

    Route::middleware(['authorize:ADM'])->group(function () {
        Route::get('/level', [LevelController::class, 'index']);
        Route::post('/level/list', [LevelController::class, 'list']);
        Route::get('/create_ajax', [LevelController::class, 'create_ajax']);
        Route::post('/ajax', [LevelController::class, 'store_ajax']);
        Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']);
        Route::get('/level/import', [LevelController::class, 'import']);
        Route::post('/level/import_ajax', [LevelController::class, 'import_ajax']);
        Route::get('/level/export_excel', [LevelController::class, 'export_excel']);
        Route::get('/level/export_pdf', [LevelController::class, 'export_pdf']);
        Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);
    });

    Route::middleware('authorize:ADM,MNG,STF')->group(function () {
        Route::get('/barang', [BarangController::class, 'index']);
        Route::post('/barang/list', [BarangController::class, 'list']);
        Route::get('/barang/create_ajax', [BarangController::class, 'create_ajax']);
        Route::post('/barang_ajax', [BarangController::class, 'store_ajax']);
        Route::get('/barang/{id}/edit_ajax', [BarangController::class, 'edit_ajax']);
        Route::put('/barang/{id}/update_ajax', [BarangController::class, 'update_ajax']);
        Route::get('/barang/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);
        Route::delete('/barang{id}/delete_ajax', [BarangController::class, 'delete_ajax']);
        Route::get('/barang/import', [BarangController::class, 'import']);
        Route::post('/barang/import_ajax', [BarangController::class, 'import_ajax']);
        Route::get('/barang/export_excel', [BarangController::class, 'export_excel']);
        Route::get('/barang/export_pdf', [BarangController::class, 'export_pdf']);
    });

    Route::middleware('authorize:ADM,MNG,STF')->group(function () {
        Route::get('/kategori', [KategoriController::class, 'index']);
        Route::post('/kategori/list', [KategoriController::class, 'list']);
        Route::get('/kategori/create_ajax', [KategoriController::class, 'create_ajax']);
        Route::post('/kategori_ajax', [KategoriController::class, 'store_ajax']);
        Route::get('/kategori/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']);
        Route::put('/kategori/{id}/update_ajax', [KategoriController::class, 'update_ajax']);
        Route::get('/kategori/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']);
        Route::delete('/kategori/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']);
        Route::get('/kategori/import', [KategoriController::class, 'import']);
        Route::post('/kategori/import_ajax', [KategoriController::class, 'import_ajax']);
        Route::get('/kategori/export_excel', [KategoriController::class, 'export_excel']);
        Route::get('/kategori/export_pdf', [KategoriController::class, 'export_pdf']);
    });

    Route::middleware('authorize:ADM,MNG,STF')->group(function () {
        Route::get('/stok', [StokController::class, 'index']);
        Route::post('/stok/list', [StokController::class, 'list']);
        Route::get('/stok/create_ajax', [StokController::class, 'create_ajax']);
        Route::post('/stok_ajax', [StokController::class, 'store_ajax']);
        Route::get('/stok/{id}/edit_ajax', [StokController::class, 'edit_ajax']);
        Route::put('/stok/{id}/update_ajax', [StokController::class, 'update_ajax']);
        Route::get('/stok/{id}/delete_ajax', [StokController::class, 'confirm_ajax']);
        Route::delete('/stok/{id}/delete_ajax', [StokController::class, 'delete_ajax']);
    });

    Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
        Route::get('/supplier', [SupplierController::class, 'index']);
        Route::post('/supplier/list', [SupplierController::class, 'list']);
        Route::get('/supplier/create_ajax', [SupplierController::class, 'create_ajax']);
        Route::post('/supplier_ajax', [SupplierController::class, 'store_ajax']);
        Route::get('/supplier/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']);
        Route::put('/supplier/{id}/update_ajax', [SupplierController::class, 'update_ajax']);
        Route::get('/supplier/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']);
        Route::delete('/supplier/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']);
        Route::get('/supplier/import', [SupplierController::class, 'import']);
        Route::post('/supplier/import_ajax', [SupplierController::class, 'import_ajax']);
        Route::get('/supplier/export_excel', [SupplierController::class, 'export_excel']);
        Route::get('/supplier/export_pdf', [SupplierController::class, 'export_pdf']);
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/list', [UserController::class, 'list']);
        Route::get('/create', [UserController::class, 'create']);
        Route::post('/', [UserController::class, 'store'] );
        Route::get('/create_ajax', [UserController::class, 'create_ajax']);
        Route::post('/ajax', [UserController::class, 'store_ajax']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::get('/{id}/show_ajax', [UserController::class, 'show_ajax']);
        Route::get('/{id}/edit', [UserController::class, 'edit']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']);
        // Import & Export disamakan dengan barang
        Route::get('/import', [UserController::class, 'import']);
        Route::post('/import_ajax', [UserController::class, 'import_ajax']);
        Route::get('/export_excel', [UserController::class, 'export_excel']);
        Route::get('/export_pdf', [UserController::class, 'export_pdf']);
    });

    // Route::prefix('level')->group(function () {
    //     Route::get('/', [LevelController::class, 'index']);
    //     Route::post('/list', [LevelController::class, 'list'])->name('level.list');
    //     Route::get('/create', [LevelController::class, 'create']);
    //     Route::post('/', [LevelController::class, 'store']);
    //     Route::get('/create_ajax', [LevelController::class, 'create_ajax']);
    //     Route::post('/ajax', [LevelController::class, 'store_ajax']);
    //     Route::get('/{id}', [LevelController::class, 'show']);
    //     Route::get('/{id}/show_ajax', [LevelController::class, 'show_ajax']);
    //     Route::get('/{id}/edit', [LevelController::class, 'edit']);
    //     Route::put('/{id}', [LevelController::class, 'update']);
    //     Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']);
    //     Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']);
    //     Route::delete('/{id}', [LevelController::class, 'destroy']);
    //     Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);
    //     Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);
    // });

    // Route::prefix('level')->group(function () {
    //     Route::get('/', [LevelController::class, 'index']);
    //     Route::post('/list', [LevelController::class, 'list'])->name('level.list');
    //     Route::get('/create_ajax', [LevelController::class, 'create_ajax']);
    //     Route::post('/ajax', [LevelController::class, 'store_ajax']);
    //     Route::get('/{id}/show_ajax', [LevelController::class, 'show_ajax']);
    //     Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']);
    //     Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']);
    //     Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);
    //     Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);
    // });


    // Route::prefix('kategori')->group(function () {
    //     Route::get('/', [KategoriController::class, 'index']);
    //     Route::post('/list', [KategoriController::class, 'list'])->name('kategori.list');
    //     Route::get('/create', [KategoriController::class, 'create']);
    //     Route::post('/', [KategoriController::class, 'store']);
    //     Route::get('/{id}/edit', [KategoriController::class, 'edit']);
    //     Route::put('/{id}', [KategoriController::class, 'update']);
    //     Route::delete('/{id}', [KategoriController::class, 'destroy']);
    // });

    // Route::prefix('barang')->group(function () {
    //     Route::get('/', [BarangController::class, 'index']);
    //     Route::post('/list', [BarangController::class, 'list'])->name('barang.list');
    //     Route::get('/create', [BarangController::class, 'create']);
    //     Route::post('/', [BarangController::class, 'store']);
    //     Route::get('/{id}', [BarangController::class, 'show']);
    //     Route::get('/{id}/edit', [BarangController::class, 'edit']);
    //     Route::put('/{id}', [BarangController::class, 'update']);
    //     Route::delete('/{id}', [BarangController::class, 'destroy']);
    // });

    // Route::prefix('barang')->group(function () {
    //     Route::get('/', [BarangController::class, 'index']);
    //     Route::post('/list', [BarangController::class, 'list'])->name('barang.list');
    //     Route::get('/create_ajax', [BarangController::class, 'create_ajax']);
    //     Route::post('/ajax', [BarangController::class, 'store_ajax']);
    //     Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax'])->name('barang.show_ajax');
    //     Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']);
    //     Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']);
    //     Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);
    //     Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']);
    // });

    // Route::prefix('stok')->group(function () {
    //     Route::get('/', [StokController::class, 'index']);
    //     Route::post('/list', [StokController::class, 'list'])->name('stok.list');
    //     Route::get('/create', [StokController::class, 'create']);
    //     Route::post('/', [StokController::class, 'store']);
    //     Route::get('/{id}', [StokController::class, 'show']);
    //     Route::get('/{id}/edit', [StokController::class, 'edit']);
    //     Route::put('/{id}', [StokController::class, 'update']);
    //     Route::delete('/{id}', [StokController::class, 'destroy']);
    // });

    // Route::prefix('stok')->group(function () {
    //     Route::get('/', [StokController::class, 'index']);
    //     Route::post('/list', [StokController::class, 'list'])->name('stok.list');
    //     Route::get('/create_ajax', [StokController::class, 'create_ajax']);
    //     Route::post('/ajax', [StokController::class, 'store_ajax']);
    //     Route::get('/{id}/show_ajax', [StokController::class, 'show_ajax'])->name('stok.show_ajax');
    //     Route::get('/{id}/edit_ajax', [StokController::class, 'edit_ajax']);
    //     Route::put('/{id}/update_ajax', [StokController::class, 'update_ajax']);
    //     Route::get('/{id}/delete_ajax', [StokController::class, 'confirm_ajax']);
    //     Route::delete('/{id}/delete_ajax', [StokController::class, 'delete_ajax']);
    // });

    // Route::prefix('penjualan')->group(function () {
    //     // Penjualan (header)
    //     Route::get('/', [PenjualanController::class, 'index']);
    //     Route::post('/list', [PenjualanController::class, 'list'])->name('penjualan.list');
    //     Route::get('/create', [PenjualanController::class, 'create']);
    //     Route::post('/', [PenjualanController::class, 'store']);
    //     Route::get('/{id}', [PenjualanController::class, 'show']);
    //     Route::get('/{id}/edit', [PenjualanController::class, 'edit']);
    //     Route::put('/{id}', [PenjualanController::class, 'update']);
    //     Route::delete('/{id}', [PenjualanController::class, 'destroy']);

    //     // Penjualan Detail (nested di dalam penjualan)
    //     Route::prefix('{penjualan_id}/detail')->group(function () {
    //         Route::get('/', [PenjualanDetailController::class, 'index']);
    //         Route::post('/list', [PenjualanDetailController::class, 'list'])->name('penjualan.detail.list');
    //         Route::get('/create', [PenjualanDetailController::class, 'create']);
    //         Route::post('/', [PenjualanDetailController::class, 'store']);
    //         Route::get('/{detail_id}', [PenjualanDetailController::class, 'show']);
    //         Route::get('/{detail_id}/edit', [PenjualanDetailController::class, 'edit']);
    //         Route::put('/{detail_id}', [PenjualanDetailController::class, 'update']);
    //         Route::delete('/{detail_id}', [PenjualanDetailController::class, 'destroy']);
    //     });
    // });
    Route::prefix('penjualan')->group(function () {
        Route::get('/', [PenjualanController::class, 'index']);
        Route::post('/list', [PenjualanController::class, 'list'])->name('penjualan.list');
        Route::get('/{id}/show_ajax', [PenjualanController::class, 'show_ajax']);
        Route::get('/{id}/delete_ajax', [PenjualanController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [PenjualanController::class, 'delete_ajax']);
    });
});
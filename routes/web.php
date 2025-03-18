<?php

use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PenjualanDetailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\StokController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index']);

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

Route::prefix('level')->group(function () {
    Route::get('/', [LevelController::class, 'index']);
    Route::post('/list', [LevelController::class, 'list'])->name('level.list');
    Route::get('/create_ajax', [LevelController::class, 'create_ajax']);
    Route::post('/ajax', [LevelController::class, 'store_ajax']);
    Route::get('/{id}/show_ajax', [LevelController::class, 'show_ajax']);
    Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);
});


Route::prefix('kategori')->group(function () {
    Route::get('/', [KategoriController::class, 'index']);
    Route::post('/list', [KategoriController::class, 'list'])->name('kategori.list');
    Route::get('/create', [KategoriController::class, 'create']);
    Route::post('/', [KategoriController::class, 'store']);
    Route::get('/{id}/edit', [KategoriController::class, 'edit']);
    Route::put('/{id}', [KategoriController::class, 'update']);
    Route::delete('/{id}', [KategoriController::class, 'destroy']);
});

use App\Http\Controllers\BarangController;

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

Route::prefix('barang')->group(function () {
    Route::get('/', [BarangController::class, 'index']);
    Route::post('/list', [BarangController::class, 'list'])->name('barang.list');
    Route::get('/create_ajax', [BarangController::class, 'create_ajax']);
    Route::post('/ajax', [BarangController::class, 'store_ajax']);
    Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax'])->name('barang.show_ajax');
    Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']);
});

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

Route::prefix('stok')->group(function () {
    Route::get('/', [StokController::class, 'index']);
    Route::post('/list', [StokController::class, 'list'])->name('stok.list');
    Route::get('/create_ajax', [StokController::class, 'create_ajax']);
    Route::post('/ajax', [StokController::class, 'store_ajax']);
    Route::get('/{id}/show_ajax', [StokController::class, 'show_ajax'])->name('stok.show_ajax');
    Route::get('/{id}/edit_ajax', [StokController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [StokController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax', [StokController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax', [StokController::class, 'delete_ajax']);
});

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
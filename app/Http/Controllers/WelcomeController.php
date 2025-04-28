<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\BarangModel;
use App\Models\StokModel;
use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class WelcomeController extends Controller
{
    public function index(){
        $breadcrumb = (object)[
            'title' => 'Selamat Datang',
            'list' => ['Home', 'Welcome']
        ];

        $activeMenu = 'dashboard';

        // Ambil data statistik
        $totalPengguna = UserModel::count();
        $totalBarang = BarangModel::count();
        $totalStok = StokModel::sum('stok_jumlah');

        $totalPenjualan = PenjualanModel::with('detail')
            ->get()
            ->flatMap->detail
            ->sum(function ($item) {
                return $item->jumlah * $item->harga; // ganti jika field-nya berbeda
            });

        // Penjualan per bulan
        $penjualanPerBulan = PenjualanDetailModel::select(
            DB::raw('MONTH(t_penjualan.penjualan_tanggal) as bulan'),
            DB::raw('SUM(harga * jumlah) as total')
        )
        ->join('t_penjualan', 't_penjualan.penjualan_id', '=', 't_penjualan_detail.penjualan_id')
        ->groupBy(DB::raw('MONTH(t_penjualan.penjualan_tanggal)'))
        ->orderBy('bulan')
        ->pluck('total', 'bulan')
        ->toArray();

        $labelsBulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $dataPenjualan = [];
        for ($i = 1; $i <= 12; $i++) {
        $dataPenjualan[] = $penjualanPerBulan[$i] ?? 0;
        }


        return view('welcome', compact(
            'breadcrumb',
            'activeMenu',
            'totalPengguna',
            'totalBarang',
            'totalStok',
            'totalPenjualan',
            'labelsBulan',
            'dataPenjualan'
        ));
        
    }
}

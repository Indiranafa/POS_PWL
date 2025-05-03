<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangModel extends Model
{
    use HasFactory;

    protected $table = 'm_barang';

    protected $primaryKey = 'barang_id';

    public $timestamps = true;

    protected $fillable = [
        'kategori_id',
        'supplier_id',
        'barang_kode',
        'barang_nama',
        'harga_beli',
        'harga_jual',
        'image' // tambahkan image
    ];
    

    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
    }

    public function supplier()
    {
        return $this->belongsTo(SupplierModel::class, 'supplier_id', 'supplier_id');
    }

    public function stok()
    {
        return $this->hasMany(StokModel::class, 'barang_id', 'barang_id');
    }

    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetailModel::class, 'barang_id', 'barang_id');
    }

    protected function image(): Attribute{
        return Attribute::make(
            get: fn ($image) => url('/storage/barang/' . $image),
        );
    }
    
}
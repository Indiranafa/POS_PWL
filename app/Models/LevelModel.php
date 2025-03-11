<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LevelModel extends Model
{
    protected $table = 'm_level'; // Nama tabel sesuai dengan database
    protected $primaryKey = 'level_id'; // Primary Key sesuai migration
    protected $fillable = ['level_kode', 'level_nama']; // Kolom yang bisa diisi
}

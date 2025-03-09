<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LevelModel extends Model
{
    public function level(): HasOne{
        return $this->hasOne(UserModel::class, 'level_id', 'level_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembelian extends Model
{
    use SoftDeletes;

    protected $table = 'pembelian';

    public function siswa()
    {
        return $this->belongsTo(\App\Models\Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(\App\Models\Kelas::class);
    }

    public function barangjasa()
    {
        return $this->belongsTo(\App\Models\BarangJasa::class, 'bj_id');
    }
}

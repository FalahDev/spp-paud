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
        return $this->hasOne(\App\Models\Siswa::class);
    }

    public function kelas()
    {
        return $this->hasOne(\App\Models\Kelas::class);
    }

    public function barangjasa()
    {
        return $this->hasOne(\App\Models\BarangJasa::class, 'id', 'bj_id');
    }
}

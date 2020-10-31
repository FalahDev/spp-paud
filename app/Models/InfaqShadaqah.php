<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InfaqShadaqah extends Model
{
    use SoftDeletes;

    protected $table = 'infaq_sadaqah';

    public function siswa()
    {
        return $this->belongsTo(\App\Models\Siswa::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(\App\Models\Transaksi::class);
    }
}

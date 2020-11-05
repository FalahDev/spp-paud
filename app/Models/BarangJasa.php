<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangJasa extends Model
{
    use SoftDeletes;

    protected $table = 'barang_jasa';

    protected $fillable = [
        'nama',
        'harga_beli',
        'harga_jual',
        'stok',
        'tipe',
        'tagihan_id',
    ];
    protected $visible = [
        'nama',
        'harga_beli',
        'harga_jual',
        'stok',
        'tipe',
        'tagihan_id',
    ];

    public function beli()
    {
        return $this->hasMany(\App\Models\Pembelian::class, 'bj_id', 'id');
    }

    public function siswa()
    {
        return $this->belongsToMany(\App\Models\Siswa::class, 'pembelian', 'bj_id');
    }

    public function kelas()
    {
        return $this->belongsToMany(\App\Models\Kelas::class, 'pembelian', 'bj_id');
    }

    public function tagihan()
    {
        return $this->belongsTo(\App\Models\Tagihan::class);
    }
}

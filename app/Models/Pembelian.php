<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembelian extends Model
{
    use SoftDeletes;

    protected $table = 'pembelian';

    public function getHargaAttribute($value)
    {
        return format_idr($value);
    }

    public function getTotalHargaAttribute()
    {
        $total = $this->attributes['qty'] * $this->attributes['harga'];
        // return 'Rp' . number_format($total, 0, ',', '.');
        return format_idr($total);
    }

    public function getLunasAttribute()
    {
        $lunas = false;
        $tagihan =  $this->barangjasa->tagihan;
        if (!empty($tagihan)) {
            $transaksi = $tagihan->transaksi->where('siswa_id', $this->siswa->id)->first();
            if (!empty($transaksi)) {
                $lunas = $transaksi->is_lunas;
            }
        }
        return $lunas;
    }

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

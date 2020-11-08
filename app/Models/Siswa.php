<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    protected $table = 'siswa';

    protected $fillable = [
        'kelas_id',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'is_yatim',
        'is_lulus',
        'nis',
        'nisn',
        'wali_id',
    ];

    protected $visible = [
        'kelas_id',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'nama_wali',
        'telp_wali',
        'pekerjaan_wali',
        'is_yatim',
        'is_lulus',
        'nis',
        'nisn',
        'wali_id',
    ];

    protected static function boot()
    {
        parent::boot();

        // static::deleted(function ($siswa) {
        //     $siswa->wali()->dissociate();
        // });
    }

    public function getNamaAttribute($nama)
    {
        return strtoupper($nama);
    }

    public function totalKurang()
    {
        return $this->kekurangan()->where('dibayar', 0)->sum('jumlah');
    }

    public function totalTitipan()
    {
        $tabungan = $this->tabungan()->latest()->first();
        if ($tabungan != null) {
            $tabungan = $tabungan->saldo;
        }
        return $tabungan;
    }

    public function beli()
    {
        return $this->hasMany(\App\Models\Pembelian::class);
    }

    public function infaq()
    {
        return $this->hasMany(\App\Models\InfaqShadaqah::class);
    }

    public function kelas()
    {
        return $this->hasOne(\App\Models\Kelas::class, 'id', 'kelas_id');
    }

    public function transaksi()
    {
        return $this->hasMany(\App\Models\Transaksi::class, 'siswa_id', 'id');
    }

    public function bayar()
    {
        return $this->hasMany(\App\Models\Pembayaran::class, 'siswa_id', 'id');
    }

    public function tabungan()
    {
        return $this->hasMany(\App\Models\Tabungan::class, 'siswa_id', 'id');
    }

    public function kekurangan()
    {
        return $this->hasMany(\App\Models\Kekurangan::class, 'siswa_id', 'id');
    }

    public function wali()
    {
        return $this->belongsTo(\App\Models\WaliSiswa::class, 'wali_id');
    }
}

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
        'nisn'
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
        'nisn'
    ];

    protected static function boot() {
        parent::boot();
    
        static::deleted(function ($siswa) {
          $siswa->wali()->delete();
        });
    }

    public function kelas(){
        return $this->hasOne('App\Models\Kelas','id','kelas_id');
    }

    public function transaksi(){
        return $this->hasMany('App\Models\Transaksi','siswa_id','id');
    }

    public function role(){
        return $this->hasMany('App\Models\Role','siswa_id','id');
    }

    public function tabungan(){
        return $this->hasMany('App\Models\Tabungan','siswa_id','id');
    }

    public function kekurangan(){
        return $this->hasMany('App\Models\Kekurangan','siswa_id','id');
    }

    public function wali()
    {
        return $this->hasOne(\App\Models\WaliSiswa::class, 'siswa_id', 'id');
    }
}

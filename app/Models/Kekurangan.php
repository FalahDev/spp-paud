<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kekurangan extends Model
{
    use SoftDeletes;

    protected $table = 'kekurangan';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'siswa_id',
        'tagihan_id',
        'transaksi_id',
        'jumlah',
        'dibayar',
        'keterangan'
    ];

    public function tagihan(){
        return $this->hasOne('App\Models\Tagihan','id','tagihan_id');
    }

    public function siswa(){
        return $this->hasOne('App\Models\Siswa','id','siswa_id');
    }

    public function transaksi(){
        return $this->hasOne('App\Models\Transaksi','id','transaksi_id');
    }
}

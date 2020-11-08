<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembayaran extends Model
{
    use SoftDeletes;

    protected $table = 'role';

    protected $fillable = [
        'tagihan_id',
        'siswa_id',
    ];

    public function siswa()
    {
        return $this->hasOne(\App\Models\Siswa::class, 'id', 'siswa_id');
    }

    public function tagihan()
    {
        return $this->hasOne(\App\Models\Tagihan::class, 'id', 'tagihan_id');
    }
}

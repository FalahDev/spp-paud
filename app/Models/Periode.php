<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Periode extends Model
{
    use SoftDeletes;

    protected $table = 'periode';

    protected $fillable = [
        'nama',
        'tgl_mulai',
        'tgl_selesai',
        'is_active',
    ];

    protected $visible = [
        'nama',
        'tgl_mulai',
        'tgl_selesai',
        'is_active',
    ];

    public function kelas()
    {
        return $this->hasMany(\App\Models\Kelas::class, 'periode_id', 'id');
    }

    public function tagihan()
    {
        return $this->hasMany(\App\Models\Tagihan::class, 'periode_id', 'id');
    }
}

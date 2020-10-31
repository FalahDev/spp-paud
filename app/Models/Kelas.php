<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    protected $table = 'kelas';

    protected $fillable = [
        'periode_id',
        'nama',
    ];

    protected $visible = [
        'periode_id',
        'nama',
    ];

    public function siswa()
    {
        return $this->hasMany(\App\Models\Siswa::class, 'kelas_id', 'id');
    }

    public function periode()
    {
        return $this->hasOne(\App\Models\Periode::class, 'id', 'periode_id');
    }

    public function beli()
    {
        return $this->hasMany(\App\Models\Pembelian::class);
    }
}

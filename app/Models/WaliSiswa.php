<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaliSiswa extends Model
{
    use SoftDeletes;

    protected $table = 'wali_siswa';

    protected $fillable = ['nama', 'ponsel', 'pekerjaan'];
    protected $visible = ['nama', 'ponsel', 'pekerjaan'];

    public function siswa()
    {
        return $this->belongsTo(\App\Models\Siswa::class);
    }
}

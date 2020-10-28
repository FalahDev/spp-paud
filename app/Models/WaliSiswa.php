<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaliSiswa extends Model
{
    use SoftDeletes;

    protected $table = 'wali_siswa';

    protected $fillable = ['nama', 'ponsel', 'pekerjaan', 'siswa_id'];
    protected $visible = ['nama', 'ponsel', 'pekerjaan', 'siswa_id'];

    public function siswa()
    {
        return $this->belongsTo(\App\Models\Siswa::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class WaliSiswa extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'wali_siswa';

    protected $fillable = ['nama', 'ponsel', 'pekerjaan', 'siswa_id'];
    protected $visible = ['nama', 'ponsel', 'pekerjaan', 'siswa_id'];

    public function getAuthPassword()
    {
        return $this->siswa->nis ?? $this->siswa->nisn;
    }

    public function siswa()
    {
        return $this->belongsTo(\App\Models\Siswa::class);
    }
}

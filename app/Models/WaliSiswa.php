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

    protected $fillable = ['nama', 'ponsel', 'pekerjaan', 'password'];
    protected $visible = ['nama', 'ponsel', 'pekerjaan'];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function getNamaAttribute($nama)
    {
        return strtoupper($nama);
    }

    public function siswa()
    {
        return $this->hasMany(\App\Models\Siswa::class, 'wali_id', 'id');
    }
}

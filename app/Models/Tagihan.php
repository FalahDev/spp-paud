<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tagihan extends Model
{
    use SoftDeletes;

    protected $table = 'tagihan';

    protected $fillable = [
        'nama',
        'jumlah',
        'wajib_semua',
        'kelas_id',
        'periode_id'
    ];

    protected $visible = [
        'id',
        'nama',
        'jumlah',
        'wajib_semua',
        'kelas_id',
        'periode_id'
    ];

    protected $casts = [
        'has_item' => 'boolean',
    ];

    protected $appends = ['tagihan_id'];

    public function getTagihanIdAttribute()
    {
        return $this->attributes['id'];
    }

    public function barangjasa()
    {
        return $this->hasMany(\App\Models\BarangJasa::class, 'tagihan_id');
    }

    public function transaksi(){
        return $this->hasMany('App\Models\Transaksi','tagihan_id','id');
    }

    public function transaksiToday(){
        return $this->transaksi()->whereDate('created_at', now()->today());
    }

    public function role(){
        return $this->hasMany('App\Models\Role','tagihan_id','id');
    }

    public function siswa(){
        return $this->belongsToMany('App\Models\Siswa','role');
    }

    public function kelas(){
        return $this->hasOne('App\Models\Kelas','id','kelas_id');
    }

    public function kekurangan(){
        return $this->hasMany('App\Models\Kekurangan','tagihan_id', 'id');
    }

    public function periode(){
        return $this->belongsTo('App\Models\Periode');
    }

    public function getJumlahIdrAttribute(){
        return "IDR. ".format_idr($this->jumlah);
    }
}

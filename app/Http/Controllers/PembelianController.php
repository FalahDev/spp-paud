<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;

class PembelianController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web');
    }

    public function index()
    {
        $pembeliansiswa = Pembelian::whereNull('kelas_id')->paginate(10);
        $pembeliankelas = Pembelian::whereNotNull('kelas_id')->paginate(10);
        return view('pembelian.index', ['siswa' => $pembeliansiswa, 'kelas' => $pembeliankelas]);
    }

    // public function show(BarangJasa $item)
    // {
    //     return view('pembelian.show', ['item' => $item]);
    // }

    // public function create()
    // {
    //     return view('pembelian.form');
    // }

    // public function edit(BarangJasa $item)
    // {
    //     // $barangjasa = BarangJasa::find($item->id)->get();
    //     return view('pembelian.form', [ 'item' => $item ]);
    // }
}

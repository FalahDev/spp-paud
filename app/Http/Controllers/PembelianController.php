<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;

class PembelianController extends Controller
{
    public function index()
    {
        $pembelian = Pembelian::paginate(10);
        return view('pembelian.index', ['transaksi' => $pembelian]);
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

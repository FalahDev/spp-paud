<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangJasa;

class TagihanItemController extends Controller
{
    public function index($tagihanId = null)
    {
        if($tagihanId) {
            $barangjasa = BarangJasa::where('tagihan_id', $tagihanId)->paginate(10);
        } else {
            $barangjasa = BarangJasa::paginate(10);
        }
        return view('barangjasa.index', ['barangjasa' => $barangjasa]);
    }

    public function show(BarangJasa $item)
    {
        return view('barangjasa.show', ['item' => $item]);
    }

    public function create()
    {
        return view('barangjasa.form');
    }

    public function edit(BarangJasa $item)
    {
        // $barangjasa = BarangJasa::find($item->id)->get();
        return view('barangjasa.form', [ 'item' => $item ]);
    }
}
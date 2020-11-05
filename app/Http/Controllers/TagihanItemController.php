<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangJasa;
use Illuminate\Support\Facades\Redirect;

class TagihanItemController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web');
    }

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $request->validate([
            'nama' => 'required|max:255',
            'harga_jual' => 'required|numeric',
        ]);
        $barangjasa = BarangJasa::make($request->except('_token'));
        if($barangjasa->save()){
            return Redirect::route('itemtagihan.index')->with(['type' => 'success', 'msg' => 'Berhasil ditambahkan: ' . $barangjasa->nama]);
        } else {
            return Redirect::route('itemtagihan.index')->with(['type' => 'danger', 'msg' => 'Ada kesalahan']);
            
        }
    }

    public function edit(BarangJasa $item)
    {
        // $barangjasa = BarangJasa::find($item->id)->get();
        return view('barangjasa.form', [ 'item' => $item ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BarangJasa $item)
    {
        $request->validate([
            'nama' => 'required|max:255',
            'harga_jual' => 'required|numeric',
        ]);
        $barangjasa = $item->fill($request->except('_token'));
        if($barangjasa->save()){
            return Redirect::route('itemtagihan.index')->with(['type' => 'success', 'msg' => 'Berhasil disimpan: ' . $barangjasa->nama]);
        } else {
            return Redirect::route('itemtagihan.index')->with(['type' => 'danger', 'msg' => 'Ada kesalahan']);

        }
    }
}

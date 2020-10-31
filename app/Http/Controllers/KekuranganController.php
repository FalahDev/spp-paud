<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kekurangan;

class KekuranganController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web');
    }

    public function index()
    {
        $kekurangan = Kekurangan::where('dibayar', 0)->orderBy('created_at','desc')->paginate(10);
        return view('kekurangan.index', [
            'kekurangan' => $kekurangan,
            
        ]);
    }

    public function export()
    {
        return response()->json(['msg'=>'ok']);
    }
}

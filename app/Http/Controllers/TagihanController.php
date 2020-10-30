<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Periode;
use App\Models\BarangJasa;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TagihanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tagihan = Tagihan::whereHas('periode', function($q){
            $q->where('is_active', 1);
        })->orWhereHas('periode', function($q){ $q;}, '<', 1)
        ->orderBy('created_at','desc')->paginate(10);
        $tagihanLama = Tagihan::whereHas('periode', function($q){
            $q->where('is_active', 0);
        })
        ->orderBy('created_at','desc')->paginate(10);
        return view('tagihan.index', ['tagihan' => $tagihan, 'tagihan_lama' => $tagihanLama]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kelas = Kelas::all();
        $siswa = Siswa::where('is_lulus','!=','1')->get();
        $periode = Periode::where('is_active', '1')->get();
        $barangjasa = BarangJasa::whereNull('tagihan_id')->get();
        return view('tagihan.form',[
            'kelas' => $kelas,
            'siswa' => $siswa,
            'periode' => $periode,
            'items' => $barangjasa,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|max:255',
            'jumlah' => 'required|numeric|min:0',
            'peserta' => 'required|numeric',
            'siswa_id' => 'array|required_if:peserta,3',
            'items' => 'array|required_with:has_item'
        ]);
        if($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }
        $tagihan = Tagihan::make($request->except(['kelas_id','periode','periode_id']));
        $saveSiswaOnly = $saveItems = false;

        switch($request->peserta){
            case 1: // semua
                $tagihan->wajib_semua = 1;
                break;
            case 2: // hanya kelas
                $tagihan->kelas_id = $request->kelas_id;
                break;
            case 3: // siswa , make role
                $saveSiswaOnly = true;
                break;
            default:
                return Redirect::back()->withErrors(['msg'=>'Peserta Wajib diisi']);
        }

        if(isset($request->has_item) && $request->has_item == 'on' && !empty($request->items)){
            $saveItems = true;
            $tagihan->has_item = true;
        } else {
            $tagihan->has_item = false;
        }

        if (isset($request->periode) && $request->periode == 'on') {
            $tagihan->periode_id = $request->periode_id;
        } else {
            $tagihan->periode_id = null;
        }

        if($tagihan->save()){
            if ($saveSiswaOnly) {
                foreach($request->siswa_id as $siswa_id){
                    $tagihan->siswa()->save(Siswa::find($siswa_id));
                }
            }
            if ($saveItems) {
                foreach($request->items as $item){
                    $tagihan->barangjasa()->save(BarangJasa::find($item));
                }
            }
            return redirect()->route('tagihan.index')->with([
                'type' => 'success',
                'msg' => 'Item Tagihan ditambahkan'
            ]);
        }else{
            return redirect()->route('tagihan.index')->with([
                'type' => 'danger',
                'msg' => 'Err.., Terjadi Kesalahan'
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Tagihan $tagihan)
    {
        $kelas = Kelas::all();
        $periode = Periode::where('is_active', '1')->get();
        $siswa = Siswa::where('is_yatim','!=','1')->get();
        $barangjasa = BarangJasa::where('tagihan_id', $tagihan->id)->orWhereNull('tagihan_id')->get();
        return view('tagihan.form',[
            'kelas' => $kelas,
            'siswa' => $siswa,
            'periode' => $periode,
            'tagihan' => $tagihan,
            'items' => $barangjasa
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tagihan $tagihan)
    {
        // Log::debug($request);
        $validator = Validator::make($request->all(), [
            'nama' => 'required|max:255',
            'jumlah' => 'required|numeric|min:0',
            'peserta' => 'required|numeric',
            'siswa_id' => 'array|required_if:peserta,3',
            'items' => 'array|required_with:has_item'
        ]);
        // Log::debug($request);
        if($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }
        $tagihan->fill($request->except(['kelas_id','periode','periode_id', 'has_item']));
        
        //remove all related
        $tagihan->siswa()->detach();
        $tagihan->kelas_id = null;
        $tagihan->wajib_semua = null;

        switch($request->peserta){
            case 1: // semua
                $tagihan->wajib_semua = 1;
                break;
            case 2: // hanya kelas
                $tagihan->kelas_id = $request->kelas_id;
                break;
            case 3: // siswa , make role
                foreach($request->siswa_id as $siswa_id){
                    $tagihan->siswa()->save(Siswa::find($siswa_id));
                }
                break;
            default:
                return Redirect::back()->withErrors(['msg'=>'Peserta Wajib diisi']);
        }

        if(isset($request->has_item) && $request->has_item == 'on' && !empty($request->items)){
            foreach($request->items as $item){
                $tagihan->barangjasa()->save(BarangJasa::find($item));
            }
            $tagihan->has_item = true;
        } else {
            if (empty($request->items)) {
                $barangjasa = BarangJasa::where('tagihan_id', $tagihan->id)->get();
                foreach ($barangjasa as $item) {
                    $item->tagihan()->dissociate();
                    $item->save();
                }
            } else {
                foreach ($request->items as  $item) {
                    $barangjasa = BarangJasa::find($item)->tagihan()->dissociate();
                    $barangjasa->save();
                }
            }
            $tagihan->has_item = false;
        }

        if (isset($request->periode) && $request->periode == 'on') {
            $tagihan->periode_id = $request->periode_id;
        } else {
            $tagihan->periode_id = null;
        }

        if($tagihan->save()){
            return redirect()->route('tagihan.index')->with([
                'type' => 'success',
                'msg' => 'Item Tagihan diubah'
            ]);
        }else{
            return redirect()->route('tagihan.index')->with([
                'type' => 'danger',
                'msg' => 'Err.., Terjadi Kesalahan'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tagihan $tagihan)
    {
        if($tagihan->transaksi->count() != 0){
            return redirect()->route('tagihan.index')->with([
                'type' => 'danger',
                'msg' => 'tidak dapat menghapus tagihan yang masih memiliki transaksi'
            ]);
        }
        $tagihan->siswa()->detach();
        if($tagihan->delete()){
            return redirect()->route('tagihan.index')->with([
                'type' => 'success',
                'msg' => 'tagihan telah dihapus'
            ]);
        }
    }
}

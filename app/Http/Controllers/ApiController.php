<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kekurangan;
use App\Models\Siswa;
use App\Models\Tabungan;
use App\Models\Tagihan;
use App\Models\Transaksi;
use App\TransaksiOperator;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function __construct(TransaksiOperator $transaksi) {
        $this->middleware('auth:api');
        $this->transaksi = $transaksi;
    }

    //pay tagihan
    public function store(Request $request, Siswa $siswa)
    {
        // Log::debug($request);

        $request->validate([
            'tagihan_id' => 'required|integer|gt:0',
            'siswa_id' => 'required|numeric|gt:0',
            'keterangan' => 'sometimes|required_with:lebih|max:255',
            'jumlah' => 'required|numeric|min:0',
            'kurang' => 'sometimes|numeric|nullable',
            'lebih' => 'sometimes|numeric|gt:0',
            'via' => 'string|in:tunai,kredit,pelunasan',
        ]);
        // return response()->json($request);

        $transaksi = $this->transaksi->createTransaction($request, $siswa);

        if ($transaksi) {
            return response()->json(['msg' => 'transaksi berhasil dilakukan']);
        } else {
            return redirect()->route('spp.index')->with([
                'type' => 'danger',
                'msg'  => 'terjadi kesalahan',
            ]);
        }

    }

    public function getModifier(Siswa $siswa)
    {
        $data = ['kurang' => 0, 'lebih' => 0];
        if($siswa == null){
            $data['msg'] = 'siswa tidak ditemukan';
            return response()->json($data, 404);
        }
        if($siswa->kekurangan->count() == 0){
            return response()->json($data);
        }
        Log::debug(var_export($siswa->id, true));

        $kekurangan = Kekurangan::where('siswa_id', $siswa->id)->where('dibayar',0)->get();
        
        foreach ($kekurangan as $key => $kurang) {
            $data['kurang'] = [];
            $data['kurang'][$kurang->tagihan_id] = $kurang->jumlah;
        }

        $input = Tabungan::where('tipe','in')->where('siswa_id',$siswa->id)->sum('jumlah');
        $output = Tabungan::where('tipe','out')->where('siswa_id',$siswa->id)->sum('jumlah');
        $verify = Tabungan::where('siswa_id', $siswa->id)->latest()->first();
        if(!empty($verify) && ($input - $output) == $verify->saldo){
            $data['lebih'] = $input - $output;
        }

        // Log::debug($data);
        return response()->json($data);
    }

    //get list tagihan of siswa
    public function tagihan(Siswa $siswa)
    {
        $tagihan = $this->getTagihan($siswa);
        return response()->json($tagihan);
    }

    function print(Request $request, Siswa $siswa) {
        $beweendate = [];
        $dates      = explode('-', $request->dates);

        foreach ($dates as $index => $date) {
            if ($index == 0) {
                $date .= ' 00:00:00';
            } else {
                $date .= ' 23:59:59';
            }
            $beweendate[] = \Carbon\Carbon::create($date)->format('Y-m-d H:i:s');
        }
        $transaksi = Transaksi::where('siswa_id', $siswa->id)->whereBetween('created_at', [$beweendate[0], $beweendate[1]])->get();

        return view('transaksi.print', [
            'siswa'     => $siswa,
            'tanggal'   => $request->dates,
            'transaksi' => $transaksi,
        ]);
    }

    protected function getTagihan(Siswa $siswa)
    {
        // wajib semua
        $tagihan_wajib = Tagihan::where('wajib_semua', '1')
            ->whereHas('periode', function ($q) {$q->where('is_active', 1);
            })->orWhereHas('periode', function ($q) {$q;}, '<', 1)->get()->toArray();
        // Log::debug($tagihan_wajib);
        //kelas only
        $tagihan_kelas = Tagihan::where('kelas_id', $siswa->kelas->id)
            ->whereHas('periode', function ($q) {$q->where('is_active', 1);
            })->orWhereHas('periode', function ($q) {$q;}, '<', 1)->get()->toArray();

        //student only
        $tagihan_siswa     = [];
        $tagihan_rolesiswa = $siswa->role;
        foreach ($tagihan_rolesiswa as $tag_siswa) {
            $tagihan_siswa[] = $tag_siswa->tagihan->toArray();
        }

        $tagihan = array_merge($tagihan_wajib, $tagihan_kelas, $tagihan_siswa);

        return $tagihan;

        $tagihan_periode = Tagihan::where('wajib_semua', 1)
            ->where(function ($qu) {
                $qu->whereHas('periode', function ($q) {
                    $q->where('is_active', 1);
                })->orWhereHas('periode', function ($q) {$q;}, '<', 1);
                // })->orWhere( function($kls) use ($siswa) {
                //     $kls->where('kelas_id', $siswa->kelas->id);
            })->where(function ($tr) use ($siswa) {
            $tr->whereHas('transaksi', function ($q) use ($siswa) {
                $q->where('is_lunas', 0)->where('siswa_id', $siswa->id);
            })->orWhereHas('transaksi', function ($q) {$q;}, '<', 1);
        }); //->orWhere('kelas_id', $siswa->kelas->id)->orWhere('wajib_semua', '1')
        // ->get();
        //wajib semua
        // $tagihan_wajib = Tagihan::where('wajib_semua','1')
        // ->whereHas('periode', function($q) { $q->where('is_active',1); })->get()->toArray();

        //kelas only
        $tagihan_kelas = Tagihan::where('kelas_id', $siswa->kelas->id)
            ->where(function ($tr) {
                $tr->whereHas('transaksi', function ($q) {
                    $q->where('is_lunas', 0);
                })->orWhereHas('transaksi', function ($q) {$q;}, '<', 1);
            })->where(function ($qu) {
            $qu->whereHas('periode', function ($q) {
                $q->where('is_active', 1);
            })->orWhereHas('periode', function ($q) {$q;}, '<', 1);
        })->get()->toArray();

        // Log::debug($tagihan_periode->toSql());
        // return $tagihan_kelas;
        //student only
        // $tagihan_siswa = [];
        // $tagihan_rolesiswa = $siswa->role;
        // foreach($tagihan_rolesiswa as $tag_siswa){
        //     $tagihan_siswa[] = $tag_siswa->tagihan->toArray();
        // }

        // $tagihan = array_merge($tagihan_wajib, $tagihan_kelas, $tagihan_siswa);

        return $tagihan_periode->get()->toArray();
    }
}

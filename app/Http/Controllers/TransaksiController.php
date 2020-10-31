<?php

namespace App\Http\Controllers;

use App\Exports\SppExport;
use App\Exports\SppSiswaExport;
use App\Models\Keuangan;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Transaksi;
use App\TransaksiOperator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    public function __construct(TransaksiOperator $transaksi)
    {
        $this->transaksi = $transaksi;
        $this->middleware('auth:web');
    }

    public function index()
    {
        $siswa     = Siswa::where('is_lulus', '0')->orderBy('created_at', 'desc')->get();
        $transaksi = Transaksi::orderBy('created_at', 'desc')->paginate(10);
        return view('transaksi.index', [
            'siswa'     => $siswa,
            'transaksi' => $transaksi,
        ]);
    }

    // //pay tagihan
    // public function store(Request $request, Siswa $siswa)
    // {
    //     // Log::debug($request);

    //     $request->validate([
    //         'tagihan_id' => 'required|integer|gt:0',
    //         'siswa_id' => 'required|numeric|gt:0',
    //         'keterangan' => 'sometimes|required_with:lebih|max:255',
    //         'jumlah' => 'required|numeric|min:0',
    //         'kurang' => 'sometimes|numeric|nullable',
    //         'lebih' => 'sometimes|numeric|gt:0',
    //         'via' => 'string|in:tunai,kredit,pelunasan',
    //     ]);
    //     // return response()->json($request);

    //     $transaksi = $this->transaksi->createTransaction($request, $siswa);

    //     if ($transaksi) {
    //         return response()->json(['msg' => 'transaksi berhasil dilakukan']);
    //     } else {
    //         return redirect()->route('spp.index')->with([
    //             'type' => 'danger',
    //             'msg'  => 'terjadi kesalahan',
    //         ]);
    //     }

    // }

    public function transaksiExport()
    {
        return \Excel::download(new SppExport, 'histori_spp-' . now() . '.xlsx');
    }

    public function transaksiPrint(Request $request)
    {
        $ids       = explode(',', $request->ids);
        $total     = 0;
        $transaksi = Transaksi::whereIn('id', $ids)->get();
        foreach ($transaksi as $trans) {
            $total += $trans->keuangan->jumlah;
        }

        return view('transaksi.transaksiprint', [
            'items' => $transaksi,
            'total' => $total,
        ]);
    }

    // //get list tagihan of siswa
    // public function tagihan(Siswa $siswa)
    // {
    //     $tagihan = $this->getTagihan($siswa);
    //     return response()->json($tagihan);
    // }

    // function print(Request $request, Siswa $siswa) {
    //     $beweendate = [];
    //     $dates      = explode('-', $request->dates);

    //     foreach ($dates as $index => $date) {
    //         if ($index == 0) {
    //             $date .= ' 00:00:00';
    //         } else {
    //             $date .= ' 23:59:59';
    //         }
    //         $beweendate[] = \Carbon\Carbon::create($date)->format('Y-m-d H:i:s');
    //     }
    //     $transaksi = Transaksi::where('siswa_id', $siswa->id)->whereBetween('created_at', [$beweendate[0], $beweendate[1]])->get();

    //     return view('transaksi.print', [
    //         'siswa'     => $siswa,
    //         'tanggal'   => $request->dates,
    //         'transaksi' => $transaksi,
    //     ]);
    // }

    // protected function getTagihan(Siswa $siswa)
    // {
    //     // wajib semua
    //     $tagihan_wajib = Tagihan::where('wajib_semua', '1')
    //         ->whereHas('periode', function ($q) {$q->where('is_active', 1);
    //         })->orWhereHas('periode', function ($q) {$q;}, '<', 1)->get()->toArray();
    //     // Log::debug($tagihan_wajib);
    //     //kelas only
    //     $tagihan_kelas = Tagihan::where('kelas_id', $siswa->kelas->id)
    //         ->whereHas('periode', function ($q) {$q->where('is_active', 1);
    //         })->orWhereHas('periode', function ($q) {$q;}, '<', 1)->get()->toArray();

    //     //student only
    //     $tagihan_siswa     = [];
    //     $tagihan_rolesiswa = $siswa->role;
    //     foreach ($tagihan_rolesiswa as $tag_siswa) {
    //         $tagihan_siswa[] = $tag_siswa->tagihan->toArray();
    //     }

    //     $tagihan = array_merge($tagihan_wajib, $tagihan_kelas, $tagihan_siswa);

    //     return $tagihan;

    //     $tagihan_periode = Tagihan::where('wajib_semua', 1)
    //         ->where(function ($qu) {
    //             $qu->whereHas('periode', function ($q) {
    //                 $q->where('is_active', 1);
    //             })->orWhereHas('periode', function ($q) {$q;}, '<', 1);
    //             // })->orWhere( function($kls) use ($siswa) {
    //             //     $kls->where('kelas_id', $siswa->kelas->id);
    //         })->where(function ($tr) use ($siswa) {
    //         $tr->whereHas('transaksi', function ($q) use ($siswa) {
    //             $q->where('is_lunas', 0)->where('siswa_id', $siswa->id);
    //         })->orWhereHas('transaksi', function ($q) {$q;}, '<', 1);
    //     }); //->orWhere('kelas_id', $siswa->kelas->id)->orWhere('wajib_semua', '1')
    //     // ->get();
    //     //wajib semua
    //     // $tagihan_wajib = Tagihan::where('wajib_semua','1')
    //     // ->whereHas('periode', function($q) { $q->where('is_active',1); })->get()->toArray();

    //     //kelas only
    //     $tagihan_kelas = Tagihan::where('kelas_id', $siswa->kelas->id)
    //         ->where(function ($tr) {
    //             $tr->whereHas('transaksi', function ($q) {
    //                 $q->where('is_lunas', 0);
    //             })->orWhereHas('transaksi', function ($q) {$q;}, '<', 1);
    //         })->where(function ($qu) {
    //         $qu->whereHas('periode', function ($q) {
    //             $q->where('is_active', 1);
    //         })->orWhereHas('periode', function ($q) {$q;}, '<', 1);
    //     })->get()->toArray();

    //     // Log::debug($tagihan_periode->toSql());
    //     // return $tagihan_kelas;
    //     //student only
    //     // $tagihan_siswa = [];
    //     // $tagihan_rolesiswa = $siswa->role;
    //     // foreach($tagihan_rolesiswa as $tag_siswa){
    //     //     $tagihan_siswa[] = $tag_siswa->tagihan->toArray();
    //     // }

    //     // $tagihan = array_merge($tagihan_wajib, $tagihan_kelas, $tagihan_siswa);

    //     return $tagihan_periode->get()->toArray();
    // }

    public function export(Request $request, Siswa $siswa)
    {
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

        return \Excel::download(new SppSiswaExport($siswa, $transaksi, $request->dates), 'spp_siswa-' . now() . '.xlsx');
    }
}

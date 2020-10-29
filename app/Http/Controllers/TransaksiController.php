<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;
use App\Models\Keuangan;
use App\Models\Kekurangan;
use App\Models\Tabungan;
use App\Exports\SppSiswaExport;
use App\Exports\SppExport;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    public function index()
    {
        $siswa = Siswa::where('is_lulus','0')->orderBy('created_at','desc')->get();
        $transaksi = Transaksi::orderBy('created_at','desc')->paginate(10);
        return view('transaksi.index', [
            'siswa' => $siswa,
            'transaksi' => $transaksi,
        ]);
    }

    //pay tagihan
    public function store(Request $request, Siswa $siswa)
    {
        Log::debug($request);
        // return response()->json($request);
        DB::beginTransaction();
        //mulai transaksi, membersihkan request->jumlah dari titik dan koma
        $kurang = $request->kurang;
        $lebih  = $request->lebih;
        $titip  = $request->titipan;
        $jumlah = preg_replace("/[,.]/", "", $request->jumlah);
        $jumlah = $jumlah - $request->diskon;
        $message = 'dibayarkan secara tunai';
        //cek pembayaran via apa
        if (($request->via == 'kredit' && empty($lebih)) || $kurang > 0) {
            $message = 'dibayarkan secara angsur';
            $jumlah = $jumlah - $kurang;
        }

        //membuat transaksi baru
        $transaksi = Transaksi::make([
            'siswa_id' => $siswa->id,
            'tagihan_id' => $request->tagihan_id,
            'diskon' => $request->diskon,
            'is_lunas' => empty($kurang) ? 1 : 0,
            'keterangan' => $message.', '.$request->keterangan,
        ]);
        
        //menyimpan transaksi
        if($transaksi->save()){
            //tambahkan transaksi ke keuangan
            $keuangan = Keuangan::orderBy('created_at','desc')->first();
            $additional_message = empty($kurang) ? '' : ', kurang Rp' .format_idr($kurang);
            $additional_message .= empty($lebih) ? '' : ', menitipkan Rp' .format_idr($lebih);
            if($keuangan != null){
                $total_kas = $keuangan->total_kas + $jumlah;
            }else{
                $total_kas = $jumlah;
            }
            $cara_bayar = ($lebih > 0) ? 'tunai' : $request->via;
            $keuangan = Keuangan::create([
                'transaksi_id' => $transaksi->id,
                'tipe' => 'in',
                'jumlah' => $jumlah,
                'total_kas' => $total_kas,
                'keterangan' => $transaksi->siswa->nama.
                    ' ('.$transaksi->siswa->kelas->nama.') membayar '.$transaksi->tagihan->nama.
                    ' pada pukul '.$transaksi->created_at->format('H:i:s').
                    ', catatan : dibayarkan dengan '. $cara_bayar .
                    $additional_message . ', '.$request->keterangan
            ]);
        }

        // jika pembayaran dilakukan melalui tabungan
        if ($request->via == 'pelunasan') {
            $kekurangan = Kekurangan::where([
                ['siswa_id', '=', $siswa->id],
                ['tagihan_id', '=', $request->tagihan_id],
                ['dibayar', '=', '0']
            ]);
            $kekurangan->delete();
            $kekurangan->withTrashed()->update(['dibayar' => 1]);

            Transaksi::where([
                ['siswa_id', '=', $siswa->id],
                ['tagihan_id', '=', $request->tagihan_id],
                ['is_lunas', '=', '0']
            ])->update(['is_lunas' => 1]);
            
            // $jumlah = $jumlah - $kurang;

        } else if ($request->via == 'kredit') {
            $create = [
                'siswa_id' => $siswa->id,
                'tagihan_id' => $request->tagihan_id,
                'transaksi_id' => $transaksi->id,
                'jumlah' => $request->kurang,
                'keterangan' => $request->keterangan
            ];
            if ($lebih > 0) {
                $create['dibayar'] = 1;

                Transaksi::where([
                    ['siswa_id', '=', $siswa->id],
                    ['tagihan_id', '=', $request->tagihan_id],
                    ['is_lunas', '=', '0']
                ])->update(['is_lunas' => 1]);
            }
            $kekurangan = Kekurangan::create($create);
            // Log::debug($kekurangan);
            $query = [
                ['id', '!=', $kekurangan->id],
                ['siswa_id', '=', $siswa->id],
                ['tagihan_id', '=', $request->tagihan_id],
                ['dibayar', '=', '0']
            ];
            
            if($lebih > 0) {
                $query = [
                    ['siswa_id', '=', $siswa->id],
                    ['tagihan_id', '=', $request->tagihan_id],
                    ['dibayar', '=', '0']
                ];
            }

            $delKurang = Kekurangan::where($query);
            $delKurang->delete();
            $delKurang->withTrashed()->update(['dibayar' => 1]);

        }
        
        if($lebih > 0){
            $tabungan = Tabungan::where('siswa_id', $siswa->id)->orderBy('created_at','desc')->first();
            if(!empty($tabungan)) {
                $saldo = $tabungan->saldo;
            } else {
                $saldo = 0;
            }
            $menabung = Tabungan::create([
                'siswa_id' => $siswa->id,
                'tipe' => 'in',
                'jumlah' => $lebih,
                'saldo' => $saldo + $lebih,
                'keperluan' => 'Titipan pembayaran',
            ]);

            //tambahkan tabungan ke keuangan
            $keuangan = Keuangan::orderBy('created_at','desc')->first();
            if($keuangan != null){
                $jumlah = $keuangan->total_kas + $menabung->jumlah;
            }else{
                $jumlah = $menabung->jumlah;
            }
            $keuangan = Keuangan::create([
                'tabungan_id' => $menabung->id,
                'tipe' => $menabung->tipe,
                'jumlah' => $menabung->jumlah,
                'total_kas' => $jumlah,
                'keterangan' => $menabung->siswa->nama."(".$menabung->siswa->kelas->nama.")".
                                        ' menitipkan pembayaran sebesar '. $menabung->jumlah
                                        .' pada '.$menabung->created_at->format('H:i:s').', total titipan '.$menabung->saldo
            ]);
        }

        //ambil titipan
        if($titip > 0) {
            $tabungan = Tabungan::where('siswa_id', $siswa->id)->latest()->first();
            if(!empty($tabungan)) {
                $saldo = $tabungan->saldo;
            } else {
                $saldo = 0;
            }
            $saldo -= $titip;
            $menabung = Tabungan::create([
                'siswa_id' => $siswa->id,
                'tipe' => 'out',
                'jumlah' => $titip,
                'saldo' => $saldo,
                'keperluan' => 'Dibayarkan ke tagihan',
            ]);

            Tabungan::where('siswa_id', $siswa->id)->delete();

            if ($saldo > 0) {
                Tabungan::create([
                    'siswa_id' => $siswa->id,
                    'tipe' => 'in',
                    'jumlah' => $saldo,
                    'saldo' => $saldo,
                    'keperluan' => 'Titipan pembayaran',
                ]);
            }
            //tambahkan tabungan ke keuangan
            $keuangan = Keuangan::latest()->first();
            if($keuangan != null){
                $jumlah = $keuangan->total_kas + $menabung->jumlah;
            }else{
                $jumlah = 0;
            }
            $keuangan = Keuangan::create([
                'tabungan_id' => $menabung->id,
                'tipe' => $menabung->tipe,
                'jumlah' => $menabung->jumlah,
                'total_kas' => $jumlah,
                'keterangan' => $menabung->siswa->nama."(".$menabung->siswa->kelas->nama.")".
                                        ' mengambil titipan sebesar '. $menabung->jumlah
                                        .' untuk pembayaran pada '.$menabung->created_at->format('H:i:s').', total titipan '.$menabung->saldo
            ]);
        }

        if(isset($keuangan) || isset($kekurangan)){
            DB::commit();
            return response()->json(['msg' => 'transaksi berhasil dilakukan']);
        }else{
            DB::rollBack();
            return redirect()->route('spp.index')->with([
                'type' => 'danger',
                'msg' => 'terjadi kesalahan'
            ]);
        }
        
    }

    public function transaksiExport()
    {
        return \Excel::download(new SppExport, 'histori_spp-'.now().'.xlsx');
    }

    public function transaksiPrint(Request $request)
    {
        $ids = explode(',',$request->ids);
        $total = 0;
        $transaksi = Transaksi::whereIn('id', $ids)->get();
        foreach($transaksi as $trans){
            $total += $trans->keuangan->jumlah;
        }

        return view('transaksi.transaksiprint',[
            'items' => $transaksi,
            'total' => $total,
        ]);
    }

    //get list tagihan of siswa
    public function tagihan(Siswa $siswa)
    {
        $tagihan = $this->getTagihan($siswa);
        return response()->json($tagihan);
    }

    public function print(Request $request, Siswa $siswa)
    {
        $beweendate = [];
        $dates = explode('-',$request->dates);
        
        foreach($dates as $index => $date){
            if($index == 0){
                $date .= ' 00:00:00';
            }else{
                $date .= ' 23:59:59';
            }
            $beweendate[] = \Carbon\Carbon::create($date)->format('Y-m-d H:i:s');
        }
        $transaksi = Transaksi::where('siswa_id', $siswa->id)->whereBetween('created_at', [$beweendate[0], $beweendate[1]])->get();

        return view('transaksi.print',[
            'siswa' => $siswa,
            'tanggal' => $request->dates,
            'transaksi' => $transaksi
        ]);
    }

    protected function getTagihan(Siswa $siswa)
    {
        //wajib semua
        $tagihan_wajib = Tagihan::where('wajib_semua','1')
            ->whereHas('periode', function($q) { $q->where('is_active',1); })->get()->toArray();
        // Log::debug($tagihan_wajib);
        //kelas only
        $tagihan_kelas = Tagihan::where('kelas_id', $siswa->kelas->id)
            ->whereHas('periode', function($q) { $q->where('is_active',1); })->get()->toArray();

        //student only
        $tagihan_siswa = [];
        $tagihan_rolesiswa = $siswa->role;
        foreach($tagihan_rolesiswa as $tag_siswa){
            $tagihan_siswa[] = $tag_siswa->tagihan->toArray();
        }

        $tagihan = array_merge($tagihan_wajib, $tagihan_kelas, $tagihan_siswa);

        return $tagihan;
    }

    public function export(Request $request, Siswa $siswa)
    {
        $beweendate = [];
        $dates = explode('-',$request->dates);
        
        foreach($dates as $index => $date){
            if($index == 0){
                $date .= ' 00:00:00';
            }else{
                $date .= ' 23:59:59';
            }
            $beweendate[] = \Carbon\Carbon::create($date)->format('Y-m-d H:i:s');
        }

        $transaksi = Transaksi::where('siswa_id', $siswa->id)->whereBetween('created_at', [$beweendate[0], $beweendate[1]])->get();

        return \Excel::download(new SppSiswaExport($siswa, $transaksi, $request->dates), 'spp_siswa-'.now().'.xlsx');
    }
}

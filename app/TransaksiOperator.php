<?php
namespace App;

use App\Models\Keuangan;
use App\Models\Kekurangan;
use App\Models\Transaksi;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Tabungan;
use Illuminate\Support\Facades\DB;

class TransaksiOperator
{
    public function createTransaction(\Illuminate\Http\Request $request, Siswa $siswa)
    {
        DB::beginTransaction();
        //mulai transaksi, membersihkan request->jumlah dari titik dan koma
        $kurang       = $request->kurang;
        $lebih        = $request->lebih;
        $ambilTitipan = $request->titipan;
        $jumlah       = preg_replace("/[,.]/", "", $request->jumlah);
        $jumlah       = $jumlah - $request->diskon;
        $message      = 'dibayarkan secara tunai';
        //cek pembayaran via apa
        if (($request->via == 'kredit' && empty($lebih)) || $kurang > 0) {
            $message = 'dibayarkan secara angsur';
            $jumlah  = $jumlah - $kurang;
        }

        //membuat transaksi baru
        $transaksi = Transaksi::make([
            'siswa_id'   => $siswa->id,
            'tagihan_id' => $request->tagihan_id,
            'diskon'     => $request->diskon,
            'is_lunas'   => empty($kurang) ? 1 : 0,
            'keterangan' => $message . ', ' . $request->keterangan,
        ]);

        //menyimpan transaksi
        if ($transaksi->save()) {
            //tambahkan transaksi ke keuangan
            $keuangan           = Keuangan::orderBy('created_at', 'desc')->first();
            $additional_message = empty($kurang) ? '' : ', kurang Rp' . format_idr($kurang);
            $additional_message .= empty($lebih) ? '' : ', menitipkan Rp' . format_idr($lebih);
            if ($keuangan != null) {
                $total_kas = $keuangan->total_kas + $jumlah;
            } else {
                $total_kas = $jumlah;
            }
            $cara_bayar = ($lebih > 0) ? 'tunai' : $request->via;
            $keuangan   = Keuangan::create([
                'transaksi_id' => $transaksi->id,
                'tipe'         => 'in',
                'jumlah'       => $jumlah,
                'total_kas'    => $total_kas,
                'keterangan'   => $transaksi->siswa->nama .
                ' (' . $transaksi->siswa->kelas->nama . ') membayar ' . $transaksi->tagihan->nama .
                ' pada pukul ' . $transaksi->created_at->format('H:i:s') .
                ', catatan : dibayarkan dengan ' . $cara_bayar .
                $additional_message . ', ' . $request->keterangan,
            ]);
        }

        // jika pembayaran dilakukan melalui tabungan
        if ($request->via == 'pelunasan') {
            $kekurangan = Kekurangan::where([
                ['siswa_id', '=', $siswa->id],
                ['tagihan_id', '=', $request->tagihan_id],
                ['dibayar', '=', '0'],
            ]);
            $kekurangan->delete();
            $kekurangan->withTrashed()->update(['dibayar' => 1]);

            Transaksi::where([
                ['siswa_id', '=', $siswa->id],
                ['tagihan_id', '=', $request->tagihan_id],
                ['is_lunas', '=', '0'],
            ])->update(['is_lunas' => 1]);

            // $jumlah = $jumlah - $kurang;

        } else if ($request->via == 'kredit') {
            $create = [
                'siswa_id'     => $siswa->id,
                'tagihan_id'   => $request->tagihan_id,
                'transaksi_id' => $transaksi->id,
                'jumlah'       => $request->kurang,
                'keterangan'   => $request->keterangan,
            ];
            if ($lebih > 0) {
                $create['dibayar'] = 1;

                Transaksi::where([
                    ['siswa_id', '=', $siswa->id],
                    ['tagihan_id', '=', $request->tagihan_id],
                    ['is_lunas', '=', '0'],
                ])->update(['is_lunas' => 1]);
            }
            $kekurangan = Kekurangan::create($create);
            // Log::debug($kekurangan);
            $query = [
                ['id', '!=', $kekurangan->id],
                ['siswa_id', '=', $siswa->id],
                ['tagihan_id', '=', $request->tagihan_id],
                ['dibayar', '=', '0'],
            ];

            if ($lebih > 0) {
                $query = [
                    ['siswa_id', '=', $siswa->id],
                    ['tagihan_id', '=', $request->tagihan_id],
                    ['dibayar', '=', '0'],
                ];
            }

            $delKurang = Kekurangan::where($query);
            $delKurang->delete();
            $delKurang->withTrashed()->update(['dibayar' => 1]);

        }

        if ($lebih > 0) {
            $tabungan = Tabungan::where('siswa_id', $siswa->id)->orderBy('created_at', 'desc')->first();
            if (!empty($tabungan)) {
                $saldo = $tabungan->saldo;
            } else {
                $saldo = 0;
            }
            $menabung = Tabungan::create([
                'siswa_id'  => $siswa->id,
                'tipe'      => 'in',
                'jumlah'    => $lebih,
                'saldo'     => $saldo + $lebih,
                'keperluan' => $request->keterangan ?: 'Titipan pembayaran',
            ]);

            //tambahkan tabungan ke keuangan
            $keuangan = Keuangan::orderBy('created_at', 'desc')->first();
            if ($keuangan != null) {
                $jumlah = $keuangan->total_kas + $menabung->jumlah;
            } else {
                $jumlah = $menabung->jumlah;
            }
            $keuangan = Keuangan::create([
                'tabungan_id' => $menabung->id,
                'tipe'        => $menabung->tipe,
                'jumlah'      => $menabung->jumlah,
                'total_kas'   => $jumlah,
                'keterangan'  => $menabung->siswa->nama . "(" . $menabung->siswa->kelas->nama . ")" .
                ' menitipkan pembayaran sebesar ' . $menabung->jumlah
                . ' pada ' . $menabung->created_at->format('H:i:s') . ', total titipan ' . $menabung->saldo,
            ]);
        }

        //ambil titipan
        if ($ambilTitipan > 0) {
            $tabungan = Tabungan::where('siswa_id', $siswa->id)->latest()->first();
            if (!empty($tabungan)) {
                $saldo = $tabungan->saldo;
            } else {
                $saldo = 0;
            }
            $saldo -= $ambilTitipan;
            $menabung = Tabungan::create([
                'siswa_id'  => $siswa->id,
                'tipe'      => 'out',
                'jumlah'    => $ambilTitipan,
                'saldo'     => $saldo,
                'keperluan' => 'Dibayarkan ke tagihan',
            ]);

            Tabungan::where('siswa_id', $siswa->id)->delete();

            if ($saldo > 0) {
                Tabungan::create([
                    'siswa_id'  => $siswa->id,
                    'tipe'      => 'in',
                    'jumlah'    => $saldo,
                    'saldo'     => $saldo,
                    'keperluan' => 'Titipan pembayaran',
                ]);
            }
            //tambahkan tabungan ke keuangan
            $keuangan = Keuangan::latest()->first();
            if ($keuangan != null) {
                $jumlah = $keuangan->total_kas + $menabung->jumlah;
            } else {
                $jumlah = 0;
            }
            $keuangan = Keuangan::create([
                'tabungan_id' => $menabung->id,
                'tipe'        => $menabung->tipe,
                'jumlah'      => $menabung->jumlah,
                'total_kas'   => $jumlah,
                'keterangan'  => $menabung->siswa->nama . "(" . $menabung->siswa->kelas->nama . ")" .
                ' mengambil titipan sebesar ' . $menabung->jumlah
                . ' untuk pembayaran pada ' . $menabung->created_at->format('H:i:s') . ', total titipan ' . $menabung->saldo,
            ]);
        }

        if (isset($keuangan) || isset($kekurangan)) {
            try{ 
                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } else {
            
            return false;
        }
    }
}
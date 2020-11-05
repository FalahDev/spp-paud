<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Kelas;
use App\Models\Periode;
use App\Models\WaliSiswa;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class SiswaImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) 
        {
            if($index != 0 && ($row[0] != '') && ($row[1] != '')){
                $siswa = Siswa::make([
                    'kelas_id' => Kelas::firstOrCreate(['nama' => $row[0]])->id,
                    'nama' => $row[1],
                    'tempat_lahir' => $row[2],
                    'tanggal_lahir' => $row[3],
                    'jenis_kelamin' => $row[4],
                    'alamat' => $row[5],
                    'nis' => $row[6],
                    'nisn'=> $row[7],
                    // 'nama_wali' => $row[6],
                    // 'telp_wali'=> $row[7],
                    // 'pekerjaan_wali' => $row[8],
                    // 'is_yatim' => (($row[9] == 'Yatim') ? '1' : '0'),
                ]);

                $wali = WaliSiswa::firstOrNew(
                    ['ponsel' => $row[9]]
                );
                $wali->nama = $row[8];
                $wali->pekerjaan = $row[10];
                $wali->password = $row[7]; // password default adalah nisn
                $wali->save();

                $siswa->wali()->associate($wali)->save();
            }
        }
    }
}

<table>
    <thead>
    <tr>
        <th><b>Tanggal</b></th>
        <th><b>Siswa</b></th>
        <th><b>Kelas</b></th>
        <th><b>Tagihan</b></th>
        <th><b>Dibayarkan</b></th>
        <th><b>Kekurangan</b></th>
        <th><b>Keterangan</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($transaksi as $item)
        <tr>
            <td>{{ $item->created_at->format('Y-m-d') }}</td>
            <td>{{ $item->siswa->nama }}</td>
            <td>{{ $item->siswa->kelas->nama }}</td>
            <td>{{ $item->tagihan->nama }}</td>
            <td>{{ $item->keuangan->jumlah }}</td>
            <td>{{ isset($item->kekurangan) ? $item->kekurangan->jumlah : 0 }}</td>
            <td style="max-width:150px;">{{ $item->keterangan }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
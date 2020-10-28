<table>
    <thead>
    <tr>
        <th><b>Tanggal</b></th>
        <th><b>Pemasukan</b></th>
        <th><b>Pengeluaran</b></th>
        <th><b>Keterangan</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($keuangan as $item)
        <tr>
            <td>{{ $item->created_at->format('d-m-Y') }}</td>
            <td>{{ ($item->tipe == 'in') ? $item->jumlah : '' }}</td>
            <td>{{ ($item->tipe == 'out') ? $item->jumlah : '' }}</td>
            <td>{{ $item->keterangan }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
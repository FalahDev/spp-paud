@extends('layouts.app')

@section('page-name','Kekurangan')

@section('content')
    <div class="page-header">
        <h1 class="page-title">
            @yield('page-name')
        </h1>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Kekurangan Bayar</h3>
                    <div class="card-options">
                        {{-- <a href="{{ route('kekurangan.export') }}" class="btn btn-primary btn-sm ml-2" download="true">Export</a> --}}
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-hover table-vcenter text-wrap">
                        <thead>
                        <tr>
                            <th class="w-1">No.</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Tagihan</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($kekurangan as $index => $item)
                            <tr>
                                <td><span class="text-muted">{{ $index+1 }}</span></td>
                                <td>{{ $item->created_at->format('d-m-Y') }}</td>
                                <td><a href="{{ route('siswa.show', $item->siswa->id) }}">
                                    {{ $item->siswa->nama.'('.$item->siswa->kelas->nama.')' }}
                                </a></td>
                                <td>{{ $item->tagihan->nama }}</td>
                                <td>Rp{{ format_idr($item->jumlah) }}</td>
                                <td style="max-width:150px;">{{ $item->keterangan }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex">
                        <div class="ml-auto mb-0">
                            {{ $kekurangan->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('site-name','Sistem Informasi SPP')
@section('page-name','Item Tagihan')

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
                    <h3 class="card-title">@yield('page-name') Per Siswa</h3>
                    <a href="{{ route('itemtagihan.create') }}" class="btn btn-outline-primary btn-sm ml-5">Tambah Item Tagihan</a>
                </div>
                @if(session()->has('msg'))
                <div class="card-alert alert alert-{{ session()->get('type') }}" id="message" style="border-radius: 0px !important">
                    @if(session()->get('type') == 'success')
                        <i class="fe fe-check mr-2" aria-hidden="true"></i>
                    @else
                        <i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i> 
                    @endif
                        {{ session()->get('msg') }}
                </div>
                @endif
                <div class="table-responsive">
                    
                    <table class="table card-table table-hover table-vcenter text-wrap">
                        <thead>
                        <tr>
                            <th class="w-1">No.</th>
                            <th>Siswa</th>
                            <th>Nama Item</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Total Bayar</th>
                            <th>Keterangan</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($siswa as $index => $item)
                            <tr>
                                <td><span class="text-muted">{{ $index+1 }}</span></td>
                                <td>
                                    {{ $item->siswa->nama }}
                                </td>
                                <td>
                                    {{ $item->barangjasa->nama }}
                                </td>
                                <td>
                                    {{ $item->qty }}
                                </td>
                                <td>
                                    {{ $item->harga }}
                                </td>
                                <td>
                                    {{ $item->total_harga }}
                                </td>
                                <td>
                                    ({{ $item->lunas ? 'Lunas' : 'Belum dibayar'}}) {{ $item->keterangan }}
                                </td>
                                <td class="text-center">
                                    {{-- <a class="icon" href="{{ route('pembelian.show', $item->id) }}" title="detail item">
                                        <i class="fe fe-box"></i>
                                    </a>
                                    <a class="icon" href="{{ route('pembelian.edit', $item->id) }}" title="edit item">
                                        <i class="fe fe-edit"></i>
                                    </a>
                                    <a class="icon btn-delete" href="#!" data-id="{{ $item->id }}" title="delete item">
                                        <i class="fe fe-trash"></i>
                                    </a>
                                    <form action="{{ route('pembelian.destroy', $item->id) }}" method="POST" id="form-{{ $item->id }}">
                                        @csrf 
                                    </form> --}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex">
                        <div class="ml-auto mb-0">
                            {{ $siswa->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@yield('page-name') Per Kelas</h3>
                    {{-- <a href="{{ route('pembelian.create') }}" class="btn btn-outline-primary btn-sm ml-5">Tambah Item Tagihan</a> --}}
                </div>
                <div class="table-responsive">
                    
                    <table class="table card-table table-hover table-vcenter text-wrap">
                        <thead>
                        <tr>
                            <th class="w-1">No.</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Nama Item</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Total Bayar</th>
                            <th>Keterangan</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($kelas as $index => $item)
                            <tr>
                                <td><span class="text-muted">{{ $index+1 }}</span></td>
                                <td>
                                    {{ $item->siswa->nama }}
                                </td>
                                <td>
                                    {{ $item->kelas->nama }}
                                </td>
                                <td>
                                    {{ $item->barangjasa->nama }}
                                </td>
                                <td>
                                    {{ $item->qty }}
                                </td>
                                <td>
                                    {{ $item->harga }}
                                </td>
                                <td>
                                    {{ $item->total_harga }}
                                </td>
                                <td>
                                    ({{ $item->lunas ? 'Lunas' : 'Belum dibayar'}}) {{ $item->keterangan }}
                                </td>
                                <td class="text-center">
                                    {{-- <a class="icon" href="{{ route('pembelian.show', $item->id) }}" title="detail item">
                                        <i class="fe fe-box"></i>
                                    </a>
                                    <a class="icon" href="{{ route('pembelian.edit', $item->id) }}" title="edit item">
                                        <i class="fe fe-edit"></i>
                                    </a>
                                    <a class="icon btn-delete" href="#!" data-id="{{ $item->id }}" title="delete item">
                                        <i class="fe fe-trash"></i>
                                    </a>
                                    <form action="{{ route('pembelian.destroy', $item->id) }}" method="POST" id="form-{{ $item->id }}">
                                        @csrf 
                                    </form> --}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex">
                        <div class="ml-auto mb-0">
                            {{ $kelas->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('js')
<script>
    require(['jquery', 'sweetalert'], function ($, sweetalert) {
        $(document).ready(function () {

            $(document).on('click','.btn-delete', function(){
                formid = $(this).attr('data-id');
                swal({
                    title: 'Anda yakin ingin menghapus?',
                    text: 'tagihan yang dihapus tidak dapat dikembalikan',
                    dangerMode: true,
                    buttons: {
                        cancel: true,
                        confirm: true,
                    },
                }).then((result) => {
                    if (result) {
                        $('#form-' + formid).submit();
                    }
                })
            })

        });
    });
</script>
@endsection
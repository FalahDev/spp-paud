@extends('layouts.app')

@section('site-name','Sistem Informasi SPP')
@section('page-name', (isset($item) ? 'Ubah Item Tagihan' : 'Item Tagihan Baru'))

@section('content')
    <div class="row">
        <div class="col-8">
            <form action="{{ (isset($item) ? route('pembelian.update', $item->id) : route('pembelian.create')) }}" method="post" class="card">
                <div class="card-header">
                    <h3 class="card-title">@yield('page-name')</h3>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama" placeholder="Nama" value="{{ isset($item) ? $item->nama : old('nama') }}" required>
                            </div>
                            <div class="form-group" id="form-harga-beli">
                                <label class="form-label">Harga Beli</label>
                                <input type="number" class="form-control" name="harga_beli" value="{{ isset($item) ? $item->harga_beli : old('harga_beli') }}">
                            </div>
                            <div class="form-group" id="form-harga-jual">
                                <label class="form-label">Harga Jual</label>
                                <input type="number" class="form-control" name="harga_jual" value="{{ isset($item) ? $item->harga_jual : old('harga_jual') }}" required>
                            </div>
                            <div class="form-group" id="form-stok">
                                <label class="form-label">Stok</label>
                                <input type="number" class="form-control" name="stok" value="{{ isset($item) ? $item->stok : old('stok') }}">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer text-right">
                    <div class="d-flex">
                        <a href="{{ url()->previous() }}" class="btn btn-link">Batal</a>
                        <button type="submit" class="btn btn-primary ml-auto" id="simpan">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
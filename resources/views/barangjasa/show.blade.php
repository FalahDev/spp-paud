@extends('layouts.app')

@section('site-name','Sistem Informasi SPP')
@section('page-name', 'Detail Item Tagihan')

@section('content')
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card-group">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@yield('page-name')</h3>
                </div>
                <div class="card-body">
                    <p>
                        <b>Nama</b> : {{$item->nama}} 
                    </p>
                    <p><b>Harga Beli</b> : {{$item->harga_beli}} </p>
                    <p><b>Harga Jual</b> : {{$item->harga_jual}} </p>
                    <p><b>Stok</b> : {{$item->stok}} </p>
                    
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection
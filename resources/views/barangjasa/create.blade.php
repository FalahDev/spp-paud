@extends('layouts.app')

@section('site-name','Sistem Informasi SPP')
@section('page-name', (isset($item) ? 'Ubah item' : 'item Baru'))

@section('content')
    <div class="row">
        <div class="col-8">
            <form action="{{ (isset($item) ? route('itemtagihan.update', $item->id) : route('itemtagihan.create')) }}" method="post" class="card">
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

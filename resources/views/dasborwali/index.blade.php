@extends('layouts.public')

@section('content')
    <h1>Laporan Pembayaran Siswa</h1>

    <div class="content">
        <form id="logout-form" action="{{ route('dasborwali.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>	
        <a class="text-center" href="{{ route('dasborwali.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fe fe-log-out"></i> Keluar
        </a>
    </div>
    
@endsection
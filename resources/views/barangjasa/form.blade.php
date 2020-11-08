@extends('layouts.app')

@section('site-name','Sistem Informasi SPP')
@section('page-name', (isset($item) ? 'Ubah Item Tagihan' : 'Item Tagihan Baru'))

@php
$persiswa = false;
if (isset($item)){
    if($item->beli->count() > 0 || $item->harga_jual == 0) {
        $persiswa = true;
    }
}
@endphp

@section('content')
    <div class="row">
        <div class="col-8">
            <form action="{{ (isset($item) ? route('itemtagihan.update', $item->id) : route('itemtagihan.store')) }}" onsubmit="return validateForm()"  method="post" class="card">
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
                            <div class="form-group">
                                <label class="form-label">Jenis</label>
                                <select class="form-control" id="tipe" name="tipe" required>
                                    <option value="barang" {{ (isset($item->tipe) && $item->tipe == 'Barang') ? 'selected' : ''}}>Barang</option>
                                    <option value="jasa" {{ (isset($item->tipe) && $item->tipe == 'Jasa') ? 'selected' : ''}}>Jasa</option>
                                </select>
                            </div>
                            <div class="form-group" id="form-harga-jual">
                                <label class="form-label">Harga Jual</label>
                                <input type="number" class="form-control" name="harga_jual" value="{{ isset($item) ? $item->harga_jual : old('harga_jual') }}" required {{ (isset($item) && $item->harga_jual == 0) ? 'readonly' : ''}}>
                                <div class="form-check">
                                    <input type="checkbox" id="pembelian" class="form-check-input" name="pembelian" {{ ($persiswa) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pembelian">Harga per siswa</label>
                                </div>
                            </div>
                            <div class="form-group" id="form-harga-beli" {!! (isset($item) && $item->tipe == 'Jasa') ? 'style="display:none"' : '' !!}>
                                <label class="form-label">Harga Beli</label>
                                <input type="number" class="form-control" name="harga_beli" value="{{ isset($item) ? $item->harga_beli : old('harga_beli') }}">
                            </div>
                            <div class="form-group" id="form-stok" {!! (isset($item) && $item->tipe == 'Jasa') ? 'style="display:none"' : '' !!}>
                                <label class="form-label">Stok</label>
                                <input type="number" class="form-control" name="stok" value="{{ isset($item) ? $item->stok : old('stok') }}">
                            </div>

                            <div class="form-group subitem" id="subitem">
                                <label class="form-label">Input Per Siswa atau Kelas</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="appliedto" id="appliedtosis" value="siswa" checked>
                                    <label class="form-check-label" for="appliedtosis">Siswa</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="appliedto" id="appliedtokls" value="kelas">
                                    <label class="form-check-label" for="appliedtokls">Kelas</label>
                                </div>
                            </div>

                            <div id="form-subitem" class="form-group subitem">
                            @isset($item)
                            @if ($item->siswa->count() > 0 )
                                @foreach ($item->siswa as $sid => $si)
                                <div class="form-row">
                                    <div class="form-group col kelas_id" style="display: none;">
                                        <select name="pembelian[{{ $sid }}][kelas_id]" class="form-control" placeholder="Kelas">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelas as $item)
                                        <option value="{{ $item->id }}" {{ ($item->id == $si->pivot->kelas_id) ? 'selected' : ''}}>{{ $item->nama }} - {{ isset($item->periode) ? $item->periode->nama : '' }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col siswa_id">
                                        <select name="pembelian[{{ $sid }}][siswa_id]" class="form-control selectsiswa" placeholder="Nama siswa" required>
                                            <option value="">-- Pilih Siswa --</option>
                                            @foreach($siswa as $key => $item)
                                            <optgroup label="{{ $key }}">
                                                @foreach ($item as $id => $name)
                                                <option value="{{ $id }}" {{ ($id == $si->pivot->siswa_id) ? 'selected' : ''}}> {{ $name }} </option>
                                                @endforeach
                                            </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col">
                                        <input type="number" min="1" max="100" value="{{ $si->pivot->qty }}" name="pembelian[{{ $sid }}][qty]" class="form-control" placeholder="Jumlah item" required>
                                    </div>
                                    <div class="form-group col">
                                        <input type="text" value="{{ $si->pivot->harga }}" name="pembelian[{{ $sid }}][harga]" class="form-control" placeholder="Harga satuan" required>
                                    </div>
                                    <div class="form-group col">
                                        <input type="text" value="{{ $si->pivot->keterangan }}" name="pembelian[{{ $sid }}][keterangan]" class="form-control" placeholder="Keterangan">
                                    </div>
                                    <div class="form-group col-auto">
                                        {{-- <input type="button" class="btn btn-primary addrow" value="+"> --}}
                                        @if ($sid > 0)
                                        <input type="button" class="btn btn-danger delrow" value="-">
                                        @else
                                        <input type="button" class="btn btn-secondary" disabled value="-">
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                                @else
                                <div class="form-row">
                                    <div class="form-group col kelas_id" style="display: none;">
                                        <select name="pembelian[0][kelas_id]" class="form-control" placeholder="Kelas">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelas as $item)
                                        <option value="{{ $item->id }}" >{{ $item->nama }} - {{ isset($item->periode) ? $item->periode->nama : '' }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col siswa_id">
                                        <select name="pembelian[0][siswa_id]" class="form-control selectsiswa" placeholder="Nama siswa" required>
                                            <option value="">-- Pilih Siswa --</option>
                                            @foreach($siswa as $key => $item)
                                            <optgroup label="{{ $key }}">
                                                @foreach ($item as $id => $name)
                                                <option value="{{ $id }}" > {{ $name }} </option>
                                                @endforeach
                                            </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col">
                                        <input type="number" min="1" max="100" name="pembelian[0][qty]" class="form-control" placeholder="Jumlah item" required>
                                    </div>
                                    <div class="form-group col">
                                        <input type="text" name="pembelian[0][harga]" class="form-control" placeholder="Harga satuan" required>
                                    </div>
                                    <div class="form-group col">
                                        <input type="text" name="pembelian[0][keterangan]" class="form-control" placeholder="Keterangan">
                                    </div>
                                    <div class="form-group col-auto">
                                        {{-- <input type="button" class="btn btn-primary addrow" value="+"> --}}
                                        <input type="button" class="btn btn-secondary" disabled value="-">
                                    </div>
                                </div>
                            @endif
                            @endisset
                            </div>
                            <div class="form-group subitem">
                                <input type="button" id="addrow" class="btn btn-secondary btn-block" value="Tambah baris">

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
@section('css')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            color: black;
        }
        .select2{
            /* width: 100% !important; */
        }

        .subitem {
            display: {{ ($persiswa) ? 'block' : 'none' }};
        }
    </style>
@endsection
@section('js')
<script>
require(['jquery', 'selectize','select2', 'sweetalert'], 
    function ($, selectize, select2, swal) {

        $('.selectsiswa').select2({
            theme: 'bootstrap4',
            dropdownAutoWidth: false,
            // width: 'auto',
            placeholder: "-- Pilih siswa --",
        });

        // $('#simpan').click(function(event){
        //     if($('#item-tagihan').select2('data').length == 0 && $('#has-item').prop('checked')){
        //         swal({title:"Item belum diisi bu!", icon:'warning'})
        //         event.preventDefault()
        //     }
        // })
        var count = 1;

        $('#addrow').click(function(event){
            var newRow = $('<div class="form-row">');

            var cols = '';
            cols += '<div class="form-group col kelas_id" style="display: none;">\
                        <select name="pembelian[' + count + '][kelas_id]" class="form-control" placeholder="Kelas">\
                        <option value="">-- Pilih Kelas --</option>'+
                        @foreach($kelas as $item)
                        '<option value="{{ $item->id }}" >{{ $item->nama }} - {{ isset($item->periode) ? $item->periode->nama : '' }}</option>'+
                        @endforeach
                        '</select>\
                    </div>'
            cols += '<div class="form-group col siswa_id">\
                        <select name="pembelian[' + count + '][siswa_id]" class="form-control selectsiswa" placeholder="Nama siswa" required>' +
                            '<option value="">-- Pilih Siswa --</option>' +
                            @foreach($siswa as $key => $item)
                            '<optgroup label="{{ $key }}">' +
                                @foreach ($item as $id => $name)
                                '<option value="{{ $id }}"> {{ $name }} </option>' +
                                @endforeach
                            '</optgroup>' +
                            @endforeach
                        '</select>\
                    </div>'
            cols += '<div class="form-group col">\
                        <input type="number" min="1" max="100" name="pembelian[' + count + '][qty]" class="form-control" placeholder="Jumlah item" required>\
                    </div>'
            cols += '<div class="form-group col">\
                        <input type="text" name="pembelian[' + count + '][harga]" class="form-control" placeholder="Harga satuan" required>\
                    </div>'
            cols += '<div class="form-group col">\
                        <input type="text" name="pembelian[' + count + '][keterangan]" class="form-control" placeholder="Keterangan">\
                    </div>'
            cols += '<div class="form-group col-auto">\
                        <input type="button" class="btn btn-danger delrow" value="-">\
                    </div>'
            newRow.append(cols);
            $('#form-subitem').append(newRow);
            count++;
            // console.log(newRow)
            $('.selectsiswa').select2({
                theme: 'bootstrap4',
                dropdownAutoWidth: false,
                placeholder: "-- Pilih siswa --",
            });
        })

        $('#form-subitem').on('click', '.delrow', function(event){
            $(this).closest('.form-row').remove();       
        counter -= 1
        })
        

        $('#pembelian').change(function(event){
            var checked = $(this).prop('checked')
            $('#form-harga-jual > input').val(0);
            $('#form-harga-jual > input').prop('readonly', checked)
            // $('#form-item').toggle(checked)
            // $('#item-tagihan').prop('required', checked)
            $('.subitem').toggle(checked)
        })

        $('#tipe').change(function(event){
            var checked;
            if(this.value == 'jasa'){
                checked = false
            } else {
                checked = true
            }
            $('#form-harga-beli').toggle(checked)
            $('#form-harga-beli').prop('required', checked)
            $('#form-stok').toggle(checked)
            $('#form-stok').prop('required', checked)
        })

        $('input[name="appliedto"]').click(function(event){

            if($(this).val() == 'siswa') {
                $('.siswa_id').show()
                $('.kelas_id').hide()
            } else {
                $('.kelas_id').show()
                $('.siswa_id').hide()
            }
        })
    });
</script>
@endsection

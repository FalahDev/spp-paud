@extends('layouts.app')

@section('site-name','Sistem Informasi SPP')
@section('page-name', (isset($item) ? 'Ubah Item Tagihan' : 'Item Tagihan Baru'))

@section('content')
    <div class="row">
        <div class="col-8">
            <form action="{{ (isset($item) ? route('itemtagihan.update', $item->id) : route('itemtagihan.store')) }}" method="post" class="card">
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
                                    <option value="barang" {{ (isset($item->tipe) && $item->tipe == 'barang') ? 'selected' : ''}}>Barang</option>
                                    <option value="jasa" {{ (isset($item->tipe) && $item->tipe == 'jasa') ? 'selected' : ''}}>Jasa</option>
                                </select>
                            </div>
                            <div class="form-group" id="form-harga-jual">
                                <label class="form-label">Harga Jual</label>
                                <input type="number" class="form-control" name="harga_jual" value="{{ isset($item) ? $item->harga_jual : old('harga_jual') }}" required {{ (isset($item) && $item->harga_jual == 0) ? 'readonly' : ''}}>
                                <div class="form-check">
                                    <input type="checkbox" id="pembelian" class="form-check-input" name="pembelian" {{ (isset($item) && $item->beli->count() > 0 || $item->harga_jual == 0) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pembelian">Harga per siswa</label>
                                </div>
                            </div>
                            <div class="form-group" id="form-harga-beli" {!! (isset($item) && $item->tipe == 'jasa') ? 'style="display:none"' : '' !!}>
                                <label class="form-label">Harga Beli</label>
                                <input type="number" class="form-control" name="harga_beli" value="{{ isset($item) ? $item->harga_beli : old('harga_beli') }}">
                            </div>
                            <div class="form-group" id="form-stok" {!! (isset($item) && $item->tipe == 'jasa') ? 'style="display:none"' : '' !!}>
                                <label class="form-label">Stok</label>
                                <input type="number" class="form-control" name="stok" value="{{ isset($item) ? $item->stok : old('stok') }}">
                            </div>

                            <div class="form-group" id="form-subitem">
                                
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
@section('js')
<script>
require(['jquery', 'selectize','select2', 'sweetalert'], 
    function ($, selectize, select2, swal) {
        // $(document).ready(function () {
        //     $('#select-beast').selectize({});
        // });
        // $('#hanya-kelas').select2({
        //     placeholder: "Pilih Kelas",
        // });
        // $('#hanya-siswa').select2({
        //     placeholder: "Pilih Siswa",
        // });
        // $('#hanya-periode').select2({
        //     placeholder: "Pilih Periode",
        // });
        // $('#item-tagihan').select2({
        //     placeholder: "Pilih Item",
        // });
        // $('#simpan').click(function(event){
        //     if($('#item-tagihan').select2('data').length == 0 && $('#has-item').prop('checked')){
        //         swal({title:"Item belum diisi bu!", icon:'warning'})
        //         event.preventDefault()
        //     }
        // })
        

        $('#pembelian').change(function(event){
            var checked = $(this).prop('checked')
            $('#form-harga-jual > input').val(0);
            $('#form-harga-jual > input').prop('readonly', checked)
            // $('#form-item').toggle(checked)
            // $('#item-tagihan').prop('required', checked)
        })

        $('#tipe').change(function(event){
            var checked;
            if(this.value == 'jasa'){
                checked = false
            } else {
                checked = true
            }
            $('#form-harga-beli').toggle(checked)
            $('#form-stok').toggle(checked)
        })

        // $('#periode').change(function(event){

        //     if($(this).prop('checked')) {
        //         $('#form-periode').show()
        //     } else {
        //         $('#form-periode').hide()
        //     }
        // })
    });
</script>
@endsection

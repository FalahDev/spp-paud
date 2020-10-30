@extends('layouts.app')

@section('site-name','Sistem Informasi SPP')
@section('page-name', (isset($tagihan) ? 'Ubah Tagihan' : 'Tagihan Baru'))

@section('content')
    <div class="row">
        <div class="col-8">
            <form action="{{ (isset($tagihan) ? route('tagihan.update', $tagihan->id) : route('tagihan.create')) }}" method="post" class="card">
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
                                <input type="text" class="form-control" name="nama" placeholder="Nama" value="{{ isset($tagihan) ? $tagihan->nama : old('nama') }}" required>
                            </div>
                            <div class="form-group" style="display: {{ isset($tagihan) ? ($tagihan->has_item ? 'none' : 'block') : 'block' }}" id="form-jumlah">
                                <label class="form-label">Jumlah</label>
                                <input type="number" class="form-control" name="jumlah" value="{{ isset($tagihan) ? $tagihan->jumlah : old('jumlah') }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tidak ada jumlah tertentu</label>
                                <div class="form-check">
                                    <input type="checkbox" id="has-item" class="form-check-input" name="has_item" {{ isset($tagihan) ? ($tagihan->has_item ? 'checked' : '' ) : old('has_item') }}>
                                    <label for="has-item">Ada item tagihan</label>
                                </div>
                            </div>
                            <div class="form-group" style="display: {{ isset($tagihan) ? ($tagihan->has_item ? 'block' : 'none') : 'none' }}" id="form-item">
                                <label class="form-label">Item tagihan</label>
                                <select class="form-control" name="items[]" id="item-tagihan" multiple>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" {{ isset($tagihan) ? ($tagihan->has_item ? (in_array($item->id, $tagihan->barangjasa->pluck('id')->toArray()) ? 'selected' : '') : '') : '' }}>
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="form-label">Peserta</div>
                                <div class="custom-switches-stacked">
                                <label class="custom-switch">
                                <input type="radio" name="peserta" value="1" class="custom-switch-input" {{ isset($tagihan) ? ($tagihan->wajib_semua == 1 ? 'checked' : '') : 'checked' }}>
                                <span class="custom-switch-indicator"></span>
                                <span class="custom-switch-description">Wajib Semua Siswa</span>
                                </label>
                                <label class="custom-switch">
                                <input type="radio" name="peserta" value="2" class="custom-switch-input" {{ isset($tagihan) ? (($tagihan->kelas_id != null) ? 'checked' : '') : '' }}>
                                <span class="custom-switch-indicator"></span>
                                <span class="custom-switch-description">Hanya Kelas</span>
                                </label>
                                <label class="custom-switch">
                                <input type="radio" name="peserta" value="3" class="custom-switch-input" {{ isset($tagihan) ? (($tagihan->kelas_id == null && $tagihan->wajib_semua == null) ? 'checked' : '') : '' }}>
                                <span class="custom-switch-indicator"></span>
                                <span class="custom-switch-description">Hanya Siswa</span>
                                </label>
                                </div>
                            </div>
                            <div class="form-group" style="display: {{ isset($tagihan) ? (($tagihan->kelas_id != null) ? 'block' : 'none') : 'none' }}" id="form-kelas">
                                <label class="form-label">Kelas</label>
                                <select class="form-control" name="kelas_id" id="hanya-kelas">
                                    @foreach($kelas as $item)
                                        <option value="{{ $item->id }}" {{ isset($tagihan) ? (($tagihan->kelas_id == $item->id) ? 'selected' : '') : '' }}>
                                            {{ $item->nama }} - {{ isset($item->periode) ? $item->periode->nama : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="display: {{ isset($tagihan) ? (($tagihan->kelas_id == null && $tagihan->wajib_semua == null) ? 'block' : 'none') : 'none' }}" id="form-siswa">
                                <label class="form-label">Siswa</label>
                                <select class="form-control" name="siswa_id[]" id="hanya-siswa" multiple>
                                    @foreach($siswa as $item)
                                        <option value="{{ $item->id }}" {{ isset($tagihan) ? (($tagihan->wajib_semua == null && $tagihan->kelas_id == null) ? (in_array($item->id, $tagihan->siswa->pluck('id')->toArray()) ? 'selected' : '') : '') : '' }}>
                                            {{ $item->nama }} - {{ $item->kelas->nama }} {{ isset($item->kelas->periode) ? "(". $item->kelas->periode->nama .")" : ''}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Periode Khusus</label>
                                <div class="form-check">
                                    <input id="periode" name="periode" type="checkbox" class="form-check-input" {{ isset($tagihan) ? (($tagihan->periode_id != null) ? 'checked' : '') : '' }}>
                                    <label for="periode" class="form-check-label">Khusus periode tertentu</label>
                                </div>
                            </div>
                            <div class="form-group" style="display: {{ isset($tagihan) ? (($tagihan->periode_id != null) ? 'block' : 'none') : 'none' }}" id="form-periode">
                                <label class="form-label">Periode</label>
                                <select class="form-control" name="periode_id" id="hanya-periode">
                                    @foreach($periode as $item)
                                        <option value="{{ $item->id }}" {{ isset($tagihan) ? (($tagihan->periode_id == $item->id) ? 'selected' : '') : '' }}>
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <div class="d-flex">
                        <a href="{{ url()->previous() }}" class="btn btn-link">Batal</a>
                        <button type="submit" class="btn btn-primary ml-auto">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            color: black;
        }
        .select2{
            width: 100% !important;
        }
    </style>
@endsection
@section('js')
<script>
    require(['jquery', 'selectize','select2'], function ($, selectize) {
        $(document).ready(function () {
            $('#select-beast').selectize({});
        });
        $('#hanya-kelas').select2({
            placeholder: "Pilih Kelas",
        });
        $('#hanya-siswa').select2({
            placeholder: "Pilih Siswa",
        });
        $('#hanya-periode').select2({
            placeholder: "Pilih Periode",
        });
        $('#item-tagihan').select2({
            placeholder: "Pilih Item",
        });

        $('.custom-switch-input').change(function(){
            if(this.value == 2){
                $('#form-kelas').show()
                $('#form-siswa').hide()

                $('#hanya-kelas').prop('required', true)
                $('#hanya-siswa').prop('required', false)
            }else if(this.value == 3){
                $('#form-kelas').hide()
                $('#form-siswa').show()

                $('#hanya-kelas').prop('required', false)
                $('#hanya-siswa').prop('required', true)
            }else{
                $('#form-kelas').hide()
                $('#form-siswa').hide()

                $('#hanya-kelas').prop('required', false)
                $('#hanya-siswa').prop('required', false)
            }
        })

        $('#has-item').change(function(event){
            var checked = $(this).prop('checked')
            $('#form-jumlah > input').val(0);
            $('#form-jumlah').toggle(!checked)
            $('#form-item').toggle(checked)
        })

        $('#periode').change(function(event){

            if($(this).prop('checked')) {
                $('#form-periode').show()
            } else {
                $('#form-periode').hide()
            }
        })
    });
</script>
@endsection
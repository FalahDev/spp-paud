@extends('layouts.app')

@section('page-name','Pembayaran SPP')

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
                    <h3 class="card-title">Transaksi</h3>
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
                <div class="card-body">
                    {{-- <form action="{{ route('keuangan.store') }}" method="post"> --}}
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
                                    <label class="form-label">Siswa</label>
                                    <select id="siswa" class="form-control" name="siswa_id">
                                        <option value="#">[-- Pilih Siswa --]</option>
                                        @foreach($siswa as $item)
                                            <option value="{{ $item->id }}"> {{ $item->nama.' - '.$item->kelas->nama.' - ' }} </option>
                                        @endforeach
                                    </select><br>
                                    {{-- <p>Titipan: Rp<span id="saldo" class="saldotitipan">0</span></p> --}}
                                </div>
                                <div class="form-group" id="form-utama" style="display: none;">
                                    <div class="row gutters-xs">
                                        <div class="col-md-6">
                                            <div class="form-group" id="form-tagihan">
                                                <label class="form-label" >Tagihan</label>
                                                <select id="tagihan" class="form-control" name="tagihan_id">
                                                    
                                                </select>
                                            </div>
                                            <div class="form-group" id="form-tagihan-2">
                                                <label class="form-label">Total Tagihan</label>
                                                Rp<span id="harga">0</span>
                                                <span id="infokurang" style="display: none;"><strong>(Kekurangan bayar)</strong></span>
                                                <label class="custom-switch" id="checktitipan">
                                                    <input type="checkbox" class="custom-switch-input" id="ada-diskon">
                                                    <span class="custom-switch-indicator"></span>
                                                    <span class="custom-switch-description">Gunakan uang titipan: </span>
                                                    Rp<span class="saldotitipan">0</span>
                                                </label>
                                            </div>
                                            <div class="form-group" id="form-total">
                                                <label class="form-label">Total Pembayaran <span class="small infolebih" style="display:none; float: right;">Titipan pembayaran</span></label>
                                                <div class="row gutters-xs">
                                                    <div class="col">
                                                        <input type="text" name="pembayaran" class="form-control inputjumlah" id="total" readonly>
                                                        <input type="text" name="pelunasan" class="form-control inputjumlah" id="lunas" readonly>
                                                    </div>
                                                    <div class="col" style="display: none;" id="kelebihan">
                                                        <input type="text" name="titipan" class="form-control" id="lebih" readonly>
                                                        {{-- <span class="small">Titipan pembayaran</span> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col -md-6">
                                            <div class="form-group" id="form-keterangan">
                                                <label class="form-label">Keterangan</label>
                                                <textarea name="keterangan" id="keterangan" rows="3" class="form-control"></textarea>
                                            </div>
                                            <div class="form-group" style="margin-top: 25px " id="form-pembayaran">
                                                <label class="form-label">Pembayaran</label>
                                                <div class="selectgroup w-100">
                                                    <label class="selectgroup-item">
                                                        <input type="radio" name="via" value="tunai" class="selectgroup-input" checked="checked">
                                                        <span class="selectgroup-button">Tunai</span>
                                                    </label>
                                                    <label class="selectgroup-item" style="display: none" id="opsi-pelunasan">
                                                        <input type="radio" name="via" value="pelunasan" class="selectgroup-input">
                                                        <span class="selectgroup-button">Pelunasan</span>
                                                    </label>
                                                    <label class="selectgroup-item">
                                                        <input type="radio" name="via" value="kredit" class="selectgroup-input">
                                                        <span class="selectgroup-button">Titipan</span>
                                                    </label>
                                                    {{-- <label class="selectgroup-item" style="display: none" id="opsi-tabungan">
                                                        <input type="radio" name="via" value="tabungan" class="selectgroup-input">
                                                        <span class="selectgroup-button">Potong Tabungan</span>
                                                    </label> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <button class="btn btn-primary ml-auto" style="display: none" id="btn-simpan">Simpan</button>
                        </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Histori Transaksi</h3>
                    <div class="card-options">
                        <a href="{{ route('transaksi.export') }}" class="btn btn-primary btn-sm ml-2" download="true">Export</a>
                        <a href="#!cetak" class="btn btn-outline-primary btn-sm ml-2" id="mass-cetak">Cetak</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-hover table-vcenter text-wrap">
                        <thead>
                        <tr>
                            <th class="w-1">No.</th>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Tagihan</th>
                            <th>Dibayarkan</th>
                            <th>Kurang</th>
                            <th>Keterangan</th>
                            <th>Cetak</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($transaksi as $index => $item)
                            <tr>
                                <td><span class="text-muted">{{ $index+1 }}</span></td>
                                <td>{{ $item->created_at->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('siswa.show', $item->siswa->id) }}">
                                        {{ $item->siswa->nama.'('.$item->siswa->kelas->nama.')' }}
                                    </a>
                                </td>
                                <td>{{ $item->tagihan->nama }}</td>
                                <td>Rp{{ format_idr($item->keuangan->jumlah) }}</td>
                                <td>Rp{{ isset($item->kekurangan) ? format_idr($item->kekurangan->jumlah) : 0 }}</td>
                                <td style="max-width:150px;">{{ $item->keterangan }}</td>
                                <td>
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input tandai" name="example-checkbox2" value="{{ $item->id }}">
                                        <span class="custom-control-label">Tandai</span> 
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex">
                        <div class="ml-auto mb-0">
                            {{ $transaksi->links() }}
                        </div>
                    </div>
                </div>
            </div>
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
    require(['jquery','select2','sweetalert','axios'], function ($, select2, sweetalert, axios) {
        $(document).ready(function () {
            //format IDR
            function formatNumber(num) {
                return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
            }
            var tok = $('meta[name="csrf-token"]').prop('content');
            function toggleField(bool) {
                $('#form-utama').toggle(bool)
                // $('#form-tagihan').toggle(bool)
                // $('#form-tagihan-2').toggle(bool)
                // $('#form-total').toggle(bool)
                // $('#form-pembayaran').toggle(bool)
                // $('#opsi-pelunasan').toggle(bool)
                // $('#opsi-tabungan').toggle(bool)
                // $('#form-keterangan').toggle(bool)
                $('#btn-simpan').toggle(bool)
                $('#infokurang').toggle(bool)
                $('#kelebihan').toggle(false)
                $('#checktitipan').toggle(false)
                $('#ada-diskon').prop('checked',false)
                $('#lebih').val('')
            }
            $('#siswa').select2({
                placeholder: "Pilih Siswa",
            });
            $('#tagihan').select2({});
            
            var siswa_id;   //siswa_id
            var tagihan_id; //tagihan_id
            var saldo;      //saldo dari siswa
            var harga;      //harga dari tagihan
            var diskon = 0; //diskon
            var kurang = 0; //kekurangan
            var lebih = 0;
            var titipan = 0;
            var ambiltitipan;
            var kekurangan;
            var via = 'tunai';  //pembayaran via 
            // memilih siswa
            $('#siswa').on('change',function(){
                if(this.value == '#'){
                    toggleField(false)
                    return;
                }else{
                    siswa_id = this.value
                }
                
                axios.interceptors.request.use(function(config) {
                    // console.log(`${config.method} ${config.url}`);
                    if(config.method == 'get')
                        toggleField(false)
                    return config;
                }, function(error) {
                    console.log('Error');
                    swal({title: "Koneksi error atau ada masalah di server"});
                    return Promise.reject(error);
                });

                axios.all([
                        axios.get(`{{ route("api.getmodifier") }}/` + this.value, {headers: {'Authorization': 'Bearer '+ tok}}),
                        axios.get(`{{ route("api.gettagihan") }}/` + this.value, {headers: {'Authorization': 'Bearer '+ tok}})
                    ])
                    .then(axios.spread(function(modifier, tagihan) {
                        // console.log(modifier.data)
                        var result = modifier.data
                        titipan = result.lebih
                        
                        toggleField(true)

                        if (titipan) {
                            $('.saldotitipan').text(titipan)
                            $('#checktitipan').toggle(true)
                        }
                        $('.selectgroup-item:first').show()
                        $('.selectgroup-item:first > input')
                            .prop('checked',true).trigger('change');
                        kekurangan = result.kurang;
                        if ('msg' in result == false && result.kurang != 0) {
                            $('.selectgroup-item:first').hide();
                            $('#total').hide();
                            $('#lunas').show();
                            $('#opsi-pelunasan').show();
                            $('#opsi-pelunasan > input')
                                .prop('checked', true).trigger('change');
                            via =  $('#opsi-pelunasan > input').val();
                        } else {
                            $('#total').show();
                            $('#lunas').hide();
                            $('#opsi-pelunasan').hide();
                        }
                        // console.log(kekurangan)

                        $("#tagihan").empty()
                        result = tagihan.data
                        if(result.length == 0){
                            // alert('tidak ada item tagihan yang tersedia')
                            swal({title:'Tidak ada tagihan yang belum dibayar', icon: 'success'})
                            // $('#btn-simpan').prop('disabled', true);
                            toggleField(false)
                            return
                        }
                        for(i=0;i < result.length ;i++){
                            $("#tagihan").append('<option value="'+ result[i].id +'" data-harga="'+ result[i].jumlah +'">'+ result[i].nama +'</option>');
                        }
                        //set harga dari data pertama
                        tagihan_id = result[0].id
                        harga = result[0].jumlah
                        if(harga <= saldo){
                            $('#opsi-tabungan').show()
                        }else{
                            $('#opsi-tabungan').hide()
                        }
                        if (kekurangan != 0) {
                            if(tagihan_id in kekurangan) {
                                harga = kekurangan[tagihan_id];
                                $('#infokurang').show();
                            }
                        } else {
                            $('#infokurang').hide();
                        }

                        //menampilkan harga
                        $('#harga').text(formatNumber(harga));
                        $('#lunas').val(formatNumber(harga));
                        $('#total').val(formatNumber(harga - diskon));
                        if (!harga) {
                            $('#btn-simpan').prop('disabled', true);
                        } else {
                            $('#btn-simpan').prop('disabled', false);
                        }
                        // console.log(gettagihan.data)
                    }))
                    .catch(function(error) {
                        console.log(error.response)
                        swal({title:error.response.status, text:error.response.statusText})
                    });

            
            })

            $('#tagihan').on('change', function(){
                tagihan_id = this.value
                //set harga dari opsi yang dipilih
                harga = $('option:selected', this).data('harga');

                if(harga <= saldo){
                    $('#opsi-tabungan').show()
                } else if(harga <= 0) {
                    $('#btn-simpan').prop('disabled', true);
                }else{
                    $('#btn-simpan').prop('disabled', false);
                    $('#opsi-tabungan').hide()
                }

                if (kekurangan != 0) { 
                    if( tagihan_id in kekurangan) {
                        harga = kekurangan[tagihan_id];
                        $('#infokurang').show();
                        // console.log(kekurangan[tagihan_id])
                    } else {
                        $('#infokurang').hide();
                    }
                }

                //jika diganti diskon kembali ke nol
                // diskon = 0
                // $('#diskon').val('');
                //menampilkan harga
                $('#ada-diskon').prop('checked',false)
                $('#harga').text(formatNumber(harga));
                $('#lunas').val(formatNumber(harga)).trigger('change');
                $('#total').val(formatNumber(harga - diskon)).trigger('change');
            })

            $('#ada-diskon').on('change', function(){
                // $('#form-diskon').toggle();
                // console.log($(this).prop('checked'))
                if ($(this).prop('checked')) {
                    harga -= titipan
                    ambiltitipan = true;
                    lebih = 0
                    $('#kelebihan').val('').hide()
                    $('.infolebih').hide()
                } else {
                    harga += titipan
                    ambiltitipan = false;
                }
                // console.log(harga)
                $('#lunas').val(formatNumber(harga));
                $('#total').val(formatNumber(harga - diskon));
            })

            // $('#diskon').keyup(function(){
            //     if(this.value <= 0){
            //         this.value = ''
            //         diskon = 0
            //     }else{
            //         diskon = this.value
            //     }
            //     if((harga - diskon) < 0){
            //         diskon = 0
            //         alert('diskon invalid, silahkan tulis ulang')
            //         $('#diskon').val('')
            //     }
            //     $('#total').val(formatNumber(harga - diskon));
            //     $('#lunas').val(formatNumber(harga - diskon));
            //     if((harga - diskon) <= saldo){
            //         $('#opsi-tabungan').show()
            //     }else{
            //         $('#opsi-tabungan').hide()
            //     }
            // })

            $('.inputjumlah').on('keyup', function(event){

                // 1.
                var selection = window.getSelection().toString();
                if ( selection !== '' ) {
                    return;
                }
            
                // 2.
                if ( $.inArray( event.keyCode, [38,40,37,39] ) !== -1 ) {
                    return;
                }

                // 1
                var $this = $( this );
                var input = $this.val();
                
                // 2
                var input = input.replace(/[\D\s\._\-]+/g, "");
                
                // 3
                input = input ? parseInt( input, 10 ) : 0;
                
                // if (input > harga) {
                //     setTimeout(3000)
                //     swal({
                //         title: "Nominal yang dimasukkan kebanyakan bu!",
                //         icon: 'error'
                //     }).then(function(val){
                //         swal({
                //             title: 'Ataukah Anda ingin memasukkan uang titipan?',
                //             buttons: {
                //                 ya: {
                //                     text: 'Ya',
                //                     value: true
                //                 },
                //                 no: {
                //                     text: 'Tidak',
                //                     value: false
                //                 }
                //             }
                //         }).then(function(val){
                //             // console.log(val)
                //             if (val) {
                //                 lebih = input - harga;
                //                 $('#kelebihan').show();
                //                 $('.infolebih').show();
                //                 $('#lebih').val(formatNumber(lebih));
                //                 $('#total').val(formatNumber(harga));
                //                 $('#lunas').val(formatNumber(harga));
                //             } else {
                //                 $('.infolebih').hide();
                //                 $('#total').val(harga).trigger('keyup');
                //                 $('#lunas').val(harga).trigger('keyup');
                //             }
                //         })
                //     })
                //     // alert('nominal yang dimasukkan kebanyakan bu!')
                // } else {
                //     if (lebih > 0) {
                //         lebih = 0;
                //         $('#lebih').val(lebih);
                //         $('.infolebih').hide();
                //         $('#kelebihan').hide()
                //     }
                // }

                kurang = harga - input

                // 4
                $this.val( function() {
                    return ( input === 0 ) ? "" : input.toLocaleString( "id-ID" );
                } );

                // console.log(kurang)
            })

            $('.inputjumlah').on('blur change', function(event){
                // 1
                var $this = $( this );
                var input = $this.val();
                
                // 2
                var input = input.replace(/[\D\s\._\-]+/g, "");
                
                // 3
                input = input ? parseInt( input, 10 ) : 0;
                
                if (input > harga) {
                    setTimeout(3000)
                    swal({
                        title: "Nominal yang dimasukkan kebanyakan bu!",
                        icon: 'error'
                    }).then(function(val){
                        swal({
                            title: 'Ataukah Anda ingin memasukkan uang titipan?',
                            buttons: {
                                ya: {
                                    text: 'Ya',
                                    value: true
                                },
                                no: {
                                    text: 'Tidak',
                                    value: false
                                }
                            }
                        }).then(function(val){
                            // console.log(val)
                            if (val) {
                                lebih = input - harga;
                                $('#kelebihan').show();
                                $('.infolebih').show();
                                $('#lebih').val(formatNumber(lebih));
                                $('#total').val(formatNumber(harga));
                                $('#lunas').val(formatNumber(harga));
                            } else {
                                $('.infolebih').hide();
                                $('#total').val(harga).trigger('keyup');
                                $('#lunas').val(harga).trigger('keyup');
                            }
                        })
                    })
                    // alert('nominal yang dimasukkan kebanyakan bu!')
                } else {
                    if (lebih > 0) {
                        lebih = 0;
                        $('#lebih').val(lebih);
                        $('.infolebih').hide();
                        $('#kelebihan').hide()
                    }
                }

                $('#btn-simpan').prop('disabled', false);

                kurang = harga - input

                // console.log(kurang)
                // 4
                $this.val( function() {
                    return ( input === 0 ) ? "" : input.toLocaleString( "id-ID" );
                } );
            })

            //pembayaran via
            $('.selectgroup-input').change(function(){
                via = this.value
                if (via == 'kredit') {
                    $('#total').prop('readonly', false)
                    $('#total').select()
                    $('#lunas').prop('readonly', false)
                    $('#lunas').select()
                } else {
                    $('#total').val(harga).trigger('keyup')
                    $('#total').prop('readonly', true)
                    $('#lunas').val(harga).trigger('keyup')
                    $('#lunas').prop('readonly', true)
                    lebih = 0;
                    $('#lebih').val(lebih);
                    $('#kelebihan').hide();
                    if (kekurangan != 0 && kurang < 0) {
                        kurang = 0
                    }
                }
                // console.log(kurang)
            })

            $('#btn-simpan').on('click', function(){
                // console.log(harga)
                if((harga - diskon) == NaN){
                    alert('diskon invalid')
                } else if(kurang < 0 && lebih == 0) {
                    // alert('nominal pembayaran masih kebanyakan bu!')
                    swal({title: "Nominal pembayaran masih kebanyakan bu!", icon: 'error'})
                }else{
                    $('#btn-simpan').addClass("btn-loading")
                    var data = {
                        tagihan_id : tagihan_id,
                        siswa_id : siswa_id,
                        jumlah : harga,
                        diskon : diskon,
                        kurang : kurang,
                        keterangan : keterangan.value,
                        via : via
                    };
                    if (kurang < 0) {
                        data.lebih = Math.abs(kurang);
                        data.kurang = 0
                    }
                    if (titipan > 0 && ambiltitipan) {
                        data.titipan = titipan
                    }
                    // $.ajax({
                    //     type: "POST",
                    //     url: "{{ route('api.tagihan') }}/"+siswa_id,
                    //     data: data,
                    //     success: function(data){
                    //         // console.log(data);
                    //         swal({title: data.msg})
                    //         setTimeout(function(){
                    //             window.location.reload()
                    //         }, 2000)
                    //     },
                    //     error: function(data){
                    //         // swal({title: "Terjadi kesalahan pada transaksi, Transaksi dibatalkan"})
                    //         swal({title: data.responseJSON.message, text: data.responseJSON.errors})
                    //         console.log(data.responseJSON.errors)
                    //         setTimeout(function(){
                    //             // window.location.reload()
                    //         }, 2000)
                    //     }
                    // });
                    axios.post('{{ route('api.tagihan') }}/'+siswa_id, data, {headers: {'Authorization': 'Bearer ' + tok}})
                    .then(function(resp) {
                        $('#btn-simpan').removeClass("btn-loading")
                        swal({title:'Berhasil disimpan', text:resp.data.msg, icon: 'success'})
                        // console.log(resp)
                        setTimeout(function(){
                            window.location.reload()
                        }, 2000)
                    }).catch(function(err){
                        console.log(err.response)
                        var msg ='';
                        $.each(err.response.data.errors, function(k, m){
                            if (Array.isArray(m)) {
                                
                                msg += k + ': ' + m[0] + ' '
                            }
                        })
                        if(!msg) {
                            msg = err.response.statusText
                        }
                        swal({title:msg, text:err.response.data.message, icon: 'warning'})
                        $('#btn-simpan').removeClass("btn-loading")
                    })
                }
                
            })

            $('#mass-cetak').on('click', function(){
                var ids = []
                $('.tandai').each(function(){
                    if(this.checked){
                        ids.push(this.value)
                    }
                })

                var form = document.createElement("form");
                form.setAttribute("style", "display: none");
                form.setAttribute("method", "post");
                form.setAttribute("action", "{{ route('transaksi.print') }}");
                form.setAttribute("target", "_blank");
                
                var token = document.createElement("input");
                token.setAttribute("name", "_token");
                token.setAttribute("value", "{{csrf_token()}}");
                
                var idsForm = document.createElement("input");
                idsForm.setAttribute("name", "ids");
                idsForm.setAttribute("value", ids);

                form.appendChild(token);
                form.appendChild(idsForm);
                document.body.appendChild(form);
                form.submit();

            })
        });
    });
</script>
@endsection
<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('saldo/{siswa?}','SiswaController@getSaldo')->name('api.getsaldo');
Route::get('modifier/{siswa?}','SiswaController@getModifier')->name('api.getmodifier');
Route::post('menabung/{siswa?}', 'TabunganController@menabung')->name('api.menabung');
Route::get('tagihan/{siswa?}','TransaksiController@tagihan')->name('api.gettagihan');
Route::post('transaksi-spp/{siswa?}','TransaksiController@store')->name('api.tagihan');
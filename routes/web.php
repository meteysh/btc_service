<?php

use App\Http\Controllers\BtcController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/user/{id}', [BtcController::class, 'show'])->where('id', '[0-9]+');
Route::post('/from_user', [BtcController::class, 'depositFromUserAccount']);
Route::post('/to_partner', [BtcController::class, 'depositPartnerAccount']);
Route::post('/to_cashback', [BtcController::class, 'depositCashbackAccount']);


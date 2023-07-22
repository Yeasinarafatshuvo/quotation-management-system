<?php

use App\Http\Controllers\Payment\FlutterwavePaymentController;
use App\Http\Controllers\Payment\PaypalPaymentController;
use App\Http\Controllers\Payment\PaystackPaymentController;
use App\Http\Controllers\Payment\PaytmPaymentController;
use App\Http\Controllers\Payment\SSLCommerzPaymentController;
use App\Http\Controllers\Payment\StripePaymentController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingControler;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Payment\RazorpayPaymentController;

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


// Route::get('/offline', 'HomeController@index')->name('offline');


Route::any('/social-login/redirect/{provider}', [LoginController::class,'redirectToProvider'])->name('social.login');
Route::get('/social-login/{provider}/callback', [LoginController::class,'handleProviderCallback'])->name('social.callback');


Route::get('/product/{slug}', [HomeController::class,'index'])->name('product');
Route::get('/category/{slug}', [HomeController::class,'index'])->name('products.category');

Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('{slug}', [HomeController::class,'index'])->where('slug','.*');



Route::get('/booking/new',[BookingControler::class,'index']);


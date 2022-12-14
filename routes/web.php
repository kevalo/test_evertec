<?php

use App\Http\Controllers\FormController;
use App\Http\Controllers\OrderController;
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

Route::get('/', static function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', [OrderController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('orders', OrderController::class);
Route::get('/new_payment/{id}', [OrderController::class, 'newPayment'])->name('orders.newPayment');

Route::get('/buy', [FormController::class, 'shopping'])->name('forms.shopping');
Route::post('/preview', [FormController::class, 'preview'])->name('forms.preview');

require __DIR__ . '/auth.php';

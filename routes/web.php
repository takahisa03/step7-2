<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProductController;

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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [ProductController::class,  'index'])->name('home');

Route::get('/home', [ProductController::class,  'index'])->name('home');

// 一覧ページ表示
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

//登録画面移行
Route::get('/create_show', [ProductController::class, 'showCreate'])->name('show.create');

//登録機能
Route::post('/create', [ProductController::class, 'create'])->name('create');
//詳細画面
Route::get('/show/{id}', [ProductController::class, 'show'])->name('show');
//編集画面移行
Route::get('/edit_show/{id}', [ProductController::class, 'showEdit'])->name('show.edit');
// 編集画面表示
Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit'); 
// 更新処理
Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('products.update');

//削除機能
Route::post('/delete/{id}', [ProductController::class, 'delete'])->name('delete'); 
//検索機能
Route::get('search', [ProductController::class, 'search'])->name('search');





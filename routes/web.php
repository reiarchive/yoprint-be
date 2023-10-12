<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\uploadFile;
use App\Http\Controllers\uploadPage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [uploadPage::class, 'index'])->name('index');
Route::post('/upload', [uploadFile::class, 'processFile'])->name('processFile');
Route::get('/', [uploadPage::class, 'index'])->name('index');

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BookReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/customer',[CustomerController::class,'customer'])->name('customer');
Route::post('/customer/create',[CustomerController::class,'create'])->name('customer#create');
Route::get('/customer/delete',[CustomerController::class,'delete'])->name('customer#delete');
Route::get('/customer/search/{key}',[CustomerController::class,'search'])->name('customer#search');
Route::post('/customer/update',[CustomerController::class,'update'])->name('customer#update');

Route::get('/book',[BookController::class,'book'])->name('book');
Route::post('/book/create',[BookController::class,'create'])->name('book#create');
Route::get('/book/delete',[BookController::class,'delete'])->name('book#delete');
Route::get('/book/search/{key}',[BookController::class,'search'])->name('book#search');
Route::post('/book/update',[BookController::class,'update'])->name('book#update');

Route::get('/bookReview',[BookReviewController::class,'bookReview'])->name('bookReview');
Route::post('/bookReview/create',[BookReviewController::class,'create'])->name('bookReview#create');
Route::get('/bookReview/delete',[BookReviewController::class,'delete'])->name('bookReview#delete');
Route::post('/bookReview/update',[BookReviewController::class,'update'])->name('bookReview#update');

Route::post('/order',[OrderController::class,'order'])->name('order');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

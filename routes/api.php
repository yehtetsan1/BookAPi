<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BookReviewController;
use App\Http\Controllers\OrderDetailController;

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

Route::get('/customers/list',[CustomerController::class,'index'])->name('customer#list');
Route::post('/customers/create',[CustomerController::class,'create'])->name('customer#create');
Route::post('/customers/delete',[CustomerController::class,'delete'])->name('customer#delete');
Route::post('/customers/search',[CustomerController::class,'search'])->name('customer#search');
Route::post('/customers/update',[CustomerController::class,'update'])->name('customer#update');

Route::get('/books/list',[BookController::class,'index'])->name('book#list');
Route::post('/books/create',[BookController::class,'create'])->name('book#create');
Route::post('/books/delete',[BookController::class,'delete'])->name('book#delete');
Route::post('/books/search',[BookController::class,'search'])->name('book#search');
Route::post('/books/update',[BookController::class,'update'])->name('book#update');
Route::post('/books/image/upload',[BookController::class,'imageUpload'])->name('book#imageUpload');

Route::post('/bookReviews/list',[BookReviewController::class,'index'])->name('bookReview#list');
Route::post('/bookReviews/create',[BookReviewController::class,'create'])->name('bookReview#create');
Route::post('/bookReviews/delete',[BookReviewController::class,'delete'])->name('bookReview#delete');
Route::post('/bookReviews/update',[BookReviewController::class,'update'])->name('bookReview#update');

Route::post('/orders/create',[OrderController::class,'create'])->name('order#create');

// Route::get('/orderDetails/test',[OrderDetailController::class,'testing'])->name('orderDetail#testing');

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

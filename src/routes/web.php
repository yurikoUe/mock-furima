<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use Laravel\Fortify\Fortify;

Route::get('/', [ItemController::class, 'index'])->name('index');
Route::get('/email/verify', function () {
    return view('auth.verify');
})->name('verification.notice');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('product.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/purchase/:{item_id}', [PurchaseController::class, 'index'])->name('purchase');
    Route::post('item/{item_id}/comment', [ItemController::class, 'storeComment'])->name('product.comment');
    Route::post('/favorite/{product}', [FavoriteController::class, 'store'])->name('favorite.store');
    Route::post('/unfavorite/{product}', [FavoriteController::class, 'destroy'])->name('favorite.destroy');
});

// メール認証再送信用ルート
Route::post('/email/resend', function () {
    if (auth()->user() && !auth()->user()->hasVerifiedEmail()) {
        auth()->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent!');
    }

    return redirect()->route('verification.notice');
})->name('verification.resend');
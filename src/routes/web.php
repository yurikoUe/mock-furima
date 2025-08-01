<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RatingController;
use Laravel\Fortify\Fortify;


Route::get('/', [ItemController::class, 'index'])->name('index');
Route::get('/email/verify', function () {
    return view('auth.verify');
})->name('verification.notice');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('product.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 取引チャット機能
    Route::prefix('chat')->group(function () {
        Route::get('{order}', [ChatController::class, 'show'])->name('chat.show');
        Route::post('{order}', [ChatController::class, 'store'])->name('chat.store');
        Route::get('chat/{order}/message/{message}/edit', [ChatController::class, 'edit'])->name('chat.edit');
        Route::put('{order}/message/{message}', [ChatController::class, 'update'])->name('chat.update');
        Route::delete('{order}/message/{message}', [ChatController::class, 'destroy'])->name('chat.destroy');
    });

    // 取引評価
    Route::prefix('orders')->group(function (){
        // 購入者が取引完了を押して評価を送信
        Route::post('{order}/complete', [RatingController::class, 'completeAndRate'])->name('orders.complete');
        // 出品者が評価を送信
        Route::post('{order}/rate', [RatingController::class, 'rate'])->name('orders.rate');
    });

    Route::get('/sell', [ExhibitionController::class, 'create'])->name('sell.create');
    Route::post('/sell', [ExhibitionController::class, 'store'])->name('sell.store');

    Route::get('/purchase/{item_id}', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('address.edit');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('address.update');

    Route::post('item/{item_id}/comment', [ItemController::class, 'storeComment'])->name('product.comment.store');

    Route::post('/favorite/{product}', [FavoriteController::class, 'store'])->name('favorite.store');
    Route::post('/unfavorite/{product}', [FavoriteController::class, 'destroy'])->name('favorite.destroy');

    Route::post('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
    Route::get('/success', [PaymentController::class, 'success'])->name('checkout.success');
    Route::get('/cancel', [PaymentController::class, 'cancel'])->name('checkout.cancel');

    // メール認証再送信用ルート
    Route::post('/email/resend', function () {
        if (auth()->user() && !auth()->user()->hasVerifiedEmail()) {
            auth()->user()->sendEmailVerificationNotification();
            return back()->with('status', 'Verification link sent!');
        }

        return redirect()->route('verification.notice');
    })->name('verification.resend');
});


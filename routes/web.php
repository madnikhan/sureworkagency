<?php

use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/services', [SiteController::class, 'services'])->name('services');
Route::get('/services/{slug}', [SiteController::class, 'service'])->name('services.show');
Route::get('/industries', [SiteController::class, 'industries'])->name('industries');
Route::get('/industries/{slug}', [SiteController::class, 'industry'])->name('industries.show');
Route::get('/work', [SiteController::class, 'work'])->name('work');
Route::get('/work/{slug}', [SiteController::class, 'workShow'])->name('work.show');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::get('/insights', [SiteController::class, 'insights'])->name('insights');
Route::get('/insights/{slug}', [SiteController::class, 'insight'])->name('insights.show');
Route::get('/privacy', fn () => app(SiteController::class)->legal('privacy'))->name('privacy');
Route::get('/terms', fn () => app(SiteController::class)->legal('terms'))->name('terms');
Route::get('/cookies', fn () => app(SiteController::class)->legal('cookies'))->name('cookies');
Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', function () {
    return response("User-agent: *\nDisallow: /admin\nDisallow: /portal\nDisallow: /login\nDisallow: /magic\n", 200, [
        'Content-Type' => 'text/plain',
    ]);
})->name('robots');

Route::get('/quote', [EnquiryController::class, 'quote'])->name('quote');
Route::post('/quote', [EnquiryController::class, 'storeQuote'])->middleware('throttle:enquiries')->name('quote.store');
Route::get('/book', [EnquiryController::class, 'book'])->name('book');
Route::post('/book', [EnquiryController::class, 'storeBooking'])->middleware('throttle:enquiries')->name('book.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:login');
    Route::post('/login/magic', [SessionController::class, 'sendMagicLink'])->middleware('throttle:login')->name('login.magic');
    Route::get('/magic/{token}', [SessionController::class, 'consumeMagicLink'])->middleware('throttle:login')->name('magic.consume');
});

Route::post('/logout', [SessionController::class, 'destroy'])->middleware('auth')->name('logout');

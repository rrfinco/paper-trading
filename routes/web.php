<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/trade', function () {
    return view('trade');
})->middleware(['auth', 'verified'])->name('trade');

Route::get('/locates', function () {
    return view('locates');
})->middleware(['auth', 'verified'])->name('locates');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/broker/connect', [\App\Http\Controllers\BrokerConnectionController::class, 'connect'])->name('broker.connect');
    Route::post('/broker/disconnect', [\App\Http\Controllers\BrokerConnectionController::class, 'disconnect'])->name('broker.disconnect');
    Route::post('/broker/order', [\App\Http\Controllers\TradeOrderController::class, 'placeOrder'])->name('broker.order');
    Route::post('/broker/order/cancel', [\App\Http\Controllers\TradeOrderController::class, 'cancelOrder'])->name('broker.order.cancel');
    Route::get('/broker/order/{client_order_id}', [\App\Http\Controllers\TradeOrderController::class, 'showOrder'])->name('broker.order.show');
    Route::get('/broker/orders/{client_order_id}', [\App\Http\Controllers\TradeOrderController::class, 'showOrder'])->name('broker.orders.show');
    Route::get('/broker/snapshot', [\App\Http\Controllers\TradeOrderController::class, 'dashboardSnapshot'])->name('broker.snapshot');
    
    // Locates & Borrows
    Route::get('/broker/locate/check-etb', [\App\Http\Controllers\TradeOrderController::class, 'checkEtb'])->name('broker.locate.check-etb');
    Route::post('/broker/locate/quote', [\App\Http\Controllers\TradeOrderController::class, 'requestLocateQuote'])->name('broker.locate.quote');
    Route::get('/broker/locate/history', [\App\Http\Controllers\TradeOrderController::class, 'locateHistory'])->name('broker.locate.history');
    Route::post('/broker/locate/accept', [\App\Http\Controllers\TradeOrderController::class, 'acceptLocate'])->name('broker.locate.accept');
    Route::post('/broker/locate/cancel', [\App\Http\Controllers\TradeOrderController::class, 'cancelLocate'])->name('broker.locate.cancel');
    Route::post('/broker/locate/sell-back', [\App\Http\Controllers\TradeOrderController::class, 'sellBackLocate'])->name('broker.locate.sell-back');
    Route::get('/broker/locate/inventory', [\App\Http\Controllers\TradeOrderController::class, 'locateInventory'])->name('broker.locate.inventory');
    Route::get('/broker/orders-history', [\App\Http\Controllers\TradeOrderController::class, 'ordersHistory'])->name('broker.orders-history');
    Route::get('/broker/orders-fills', [\App\Http\Controllers\TradeOrderController::class, 'ordersFills'])->name('broker.orders-fills');
    Route::get('/broker/routes', [\App\Http\Controllers\TradeOrderController::class, 'getRoutes'])->name('broker.routes');
    Route::get('/broker/positions', [\App\Http\Controllers\TradeOrderController::class, 'getPositions'])->name('broker.positions');
    Route::get('/broker/symbols/search', [\App\Http\Controllers\TradeOrderController::class, 'searchSymbols'])->name('broker.symbols.search');
    Route::post('/broker/ws-token', [\App\Http\Controllers\TradeOrderController::class, 'getWebSocketToken'])->name('broker.ws-token');

    // Admin Dashboard Desk
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/users/{user}', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'show'])->name('admin.users.show');
    });
});

require __DIR__.'/auth.php';

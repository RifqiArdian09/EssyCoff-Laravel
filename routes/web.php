<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Categories
use App\Livewire\Categories\Index as CategoryIndex;
use App\Livewire\Categories\Create as CategoryCreate;
use App\Livewire\Categories\Edit as CategoryEdit;

// Products
use App\Livewire\Products\Index as ProductIndex;
use App\Livewire\Products\Create as ProductCreate;
use App\Livewire\Products\Edit as ProductEdit;

// POS
use App\Livewire\Pos\Cashier;
use App\Livewire\Pos\History;
use App\Livewire\Pos\TransactionDetail;

// Report
use App\Livewire\Report\Index as ReportIndex;
use App\Exports\OrdersExport;

// User;
use App\Livewire\User\Index;
use App\Livewire\User\Create;
use App\Livewire\User\Edit;

// Dashboard
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;

use App\Livewire\Pos\OrderDetail;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


    Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Settings (Volt Routes)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

/*
|--------------------------------------------------------------------------
| Categories CRUD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Volt::route('categories', CategoryIndex::class)->name('categories.index');
    Volt::route('categories/create', CategoryCreate::class)->name('categories.create');
    Volt::route('categories/{category}/edit', CategoryEdit::class)->name('categories.edit');
});

/*
|--------------------------------------------------------------------------
| Products CRUD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('products')->name('products.')->group(function() {
    Volt::route('/', ProductIndex::class)->name('index');
    Volt::route('/create', ProductCreate::class)->name('create');
    Volt::route('/{product}/edit', ProductEdit::class)->name('edit');
});

Route::middleware(['auth'])->prefix('users')->name('users.')->group(function() {
    Volt::route('/', Index::class)->name('index');
    Volt::route('/create', Create::class)->name('create');
    Volt::route('/{user}/edit', Edit::class)->name('edit');
});

/*
|--------------------------------------------------------------------------
| POS Kasir
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('pos')->group(function() {
    Volt::route('cashier', Cashier::class)->name('pos.cashier');
    Volt::route('history', History::class)->name('pos.history');
    Volt::route('transaction/{order}', TransactionDetail::class)->name('pos.transaction-detail');
    Volt::route('transaction/{order}', OrderDetail::class)
    ->name('pos.detail');
});

/*
|--------------------------------------------------------------------------
| Reports
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('report')->group(function() {
    Volt::route('/', ReportIndex::class)->name('report.index');
});

Route::get('customer', [CustomerController::class, 'index'])->name('customer');
Route::post('customer/order', [CustomerController::class, 'createOrder'])->name('customer.order');
Route::get('customer/history', [CustomerController::class, 'getOrderHistory'])->name('customer.history');

require __DIR__.'/auth.php';

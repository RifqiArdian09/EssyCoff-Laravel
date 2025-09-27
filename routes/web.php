<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Http\Request;

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
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Actions\CreateOrder;
use App\Livewire\Actions\GetOrderHistory;
    


use App\Livewire\Pos\OrderDetail;
use App\Livewire\Pos\Receipt\Index as ReceiptIndex;
use App\Livewire\Pos\Tables as TablesManage;
use App\Http\Controllers\QrController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', \App\Livewire\Customer\Home::class)->name('home');

Volt::route('dashboard', DashboardIndex::class)
    ->middleware(['auth', 'single.session', 'verified'])
    ->name('dashboard');

// Customer-facing menu and ordering (Livewire component for UI)
Route::get('customer', \App\Livewire\Customer\Order::class)->name('customer');
// Pretty route for QR: /customer/table/{code} -> redirect to /customer?table=CODE
Route::get('customer/table/{code}', function (Request $request, string $code) {
    // Check if table exists before redirecting
    $table = \App\Models\CafeTable::where('code', $code)->first();
    if (!$table) {
        return redirect()->route('customer.table.not-found', ['code' => $code]);
    }
    return redirect()->route('customer', ['table' => $code] + $request->only(['search','category']));
})->name('customer.table');
// Table not found page
Route::get('customer/table-not-found/{code?}', \App\Livewire\Customer\TableNotFound::class)->name('customer.table.not-found');
Route::post('customer/order', CreateOrder::class)->name('customer.order');
/*
|--------------------------------------------------------------------------
| Settings (Volt Routes)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'single.session'])->group(function () {
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
Route::middleware(['auth', 'single.session'])->group(function () {
    Volt::route('categories', CategoryIndex::class)->name('categories.index');
    Volt::route('categories/create', CategoryCreate::class)->name('categories.create');
    Volt::route('categories/{category}/edit', CategoryEdit::class)->name('categories.edit');
});

/*
|--------------------------------------------------------------------------
| Products CRUD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'single.session'])->prefix('products')->name('products.')->group(function() {
    Volt::route('/', ProductIndex::class)->name('index');
    Volt::route('/create', ProductCreate::class)->name('create');
    Volt::route('/{product}/edit', ProductEdit::class)->name('edit');
});

Route::middleware(['auth', 'single.session'])->prefix('users')->name('users.')->group(function() {
    Volt::route('/', Index::class)->name('index');
    Volt::route('/create', Create::class)->name('create');
    Volt::route('/{user}/edit', Edit::class)->name('edit');
});
/*
|--------------------------------------------------------------------------
| POS Kasir
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'single.session'])->prefix('pos')->group(function() {
    Volt::route('cashier', Cashier::class)->name('pos.cashier');
    Volt::route('history', History::class)->name('pos.history');
    Volt::route('transaction/{order}', OrderDetail::class)->name('pos.detail');
    Volt::route('tables', TablesManage::class)->name('pos.tables');
});
/*
|--------------------------------------------------------------------------
| Reports
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'single.session'])->prefix('report')->group(function() {
    Volt::route('/', ReportIndex::class)->name('report.index');
});
// Local QR generator for tables
Route::get('/qr/table/{code}.{format?}', [QrController::class, 'table'])->name('qr.table');




require __DIR__.'/auth.php';

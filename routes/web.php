<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Livewire\Dashboard\DashboardWidget;
use App\Livewire\WorkOrder\WorkOrderList;
use App\Livewire\WorkOrder\WorkOrderCreate;
use App\Livewire\WorkOrder\WorkOrderDetail;
use App\Livewire\WorkOrder\FeedbackForm;
use App\Livewire\DirectSale\DirectSaleList;
use App\Livewire\DirectSale\DirectSaleCreate;
use App\Livewire\Customer\CustomerList;
use App\Livewire\Customer\CustomerForm;
use App\Livewire\Customer\CustomerDetail;
use App\Livewire\Customer\WarrantyList;
use App\Livewire\SparePart\SparePartList;
use App\Livewire\SparePart\SparePartForm;
use App\Livewire\SparePart\RestockForm;
use App\Livewire\AuditLog\AuditLogList;
use App\Livewire\Report\RevenueReport;
use App\Livewire\Report\InventoryReport;
use App\Livewire\Report\FeedbackSummary;
use App\Livewire\RecycleBin\RecycleBinList;
use App\Livewire\Auth\LoginForm;

// ─── GUEST ROUTES (belum login) ─────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginForm::class)->name('login');                    // Livewire full-page
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate'); // fallback non-JS
});

// ─── AUTH ROUTES (semua role yang sudah login) ───────────────
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard — semua role bisa akses
    Route::get('/dashboard', DashboardWidget::class)->name('dashboard');

    // ─── KASIR + OWNER ────────────────────────────────────────
    Route::middleware('role:owner,kasir')->group(function () {

        Route::prefix('work-orders')->name('work-orders.')->group(function () {
            Route::get('/', WorkOrderList::class)->name('index');
            Route::get('/create', WorkOrderCreate::class)->name('create');
            Route::get('/{workOrder}', WorkOrderDetail::class)->name('show');
            Route::get('/{workOrder}/feedback', FeedbackForm::class)->name('feedback');
        });

        Route::prefix('direct-sales')->name('direct-sales.')->group(function () {
            Route::get('/', DirectSaleList::class)->name('index');
            Route::get('/create', DirectSaleCreate::class)->name('create');
        });

        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', CustomerList::class)->name('index');
            Route::get('/create', CustomerForm::class)->name('create');
            Route::get('/warranties', WarrantyList::class)->name('warranties');
            Route::get('/{customer}', CustomerDetail::class)->name('show');
            Route::get('/{customer}/edit', CustomerForm::class)->name('edit');
        });
    });

    // ─── STAF GUDANG + OWNER ──────────────────────────────────
    Route::middleware('role:owner,staf_gudang')->group(function () {

        Route::prefix('spare-parts')->name('spare-parts.')->group(function () {
            Route::get('/', SparePartList::class)->name('index');
            Route::get('/create', SparePartForm::class)->name('create');
            Route::get('/{sparePart}/edit', SparePartForm::class)->name('edit');
            Route::get('/{sparePart}/restock', RestockForm::class)->name('restock');
        });

        Route::get('/stock-movements', SparePartList::class)->name('stock-movements.index');
    });

    // ─── OWNER ONLY ───────────────────────────────────────────
    Route::middleware('role:owner')->group(function () {

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/revenue', RevenueReport::class)->name('revenue');
            Route::get('/inventory', InventoryReport::class)->name('inventory');
            Route::get('/feedback', FeedbackSummary::class)->name('feedback');
        });

        Route::get('/audit-logs', AuditLogList::class)->name('audit-logs.index');
        Route::get('/recycle-bin', RecycleBinList::class)->name('recycle-bin');
    });
});

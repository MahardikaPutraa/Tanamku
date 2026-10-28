<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PlantController as AdminPlantController;
use App\Http\Controllers\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\PlantController as SellerPlantController;
use App\Http\Controllers\Seller\StoreController as SellerStoreController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — TanamKu (marketplace)
|--------------------------------------------------------------------------
| Tiga peran: admin, seller, buyer.
*/

// ============================================================ PUBLIK
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [PlantController::class, 'index'])->name('plants.index');
Route::get('/tanaman/{plant:slug}', [PlantController::class, 'show'])->name('plants.show');
Route::get('/toko/{store:slug}', [StoreController::class, 'show'])->name('stores.show');

// Pintu masuk panel: tidak ada tautan di UI publik.
Route::get('/masuk', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('login');
    }

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'seller' => redirect()->route('seller.dashboard'),
        default => redirect()->route('home'),
    };
})->name('masuk');

// ======================================================== AUTENTIKASI
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ------------------------------------------------ Pembeli (buyer)
    Route::get('/keranjang', [CartController::class, 'index'])->name('carts.index');
    Route::post('/keranjang/{plant:slug}', [CartController::class, 'store'])->name('carts.store');
    Route::patch('/keranjang/{cartItem}', [CartController::class, 'update'])->name('carts.update');
    Route::delete('/keranjang/{cartItem}', [CartController::class, 'destroy'])->name('carts.destroy');

    Route::get('/checkout/{store:slug}', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout/{store:slug}', [CheckoutController::class, 'store'])->middleware('throttle:15,1')->name('checkout.store');

    Route::get('/pesanan', [OrderController::class, 'mine'])->name('orders.mine');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/pesanan/{order}/selesai', [OrderController::class, 'markCompleted'])->name('orders.complete');
    Route::post('/pesanan/{order}/ulasan', [ReviewController::class, 'store'])->name('reviews.store');
});

// ------------------------------------------------- Penjual (seller)
Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');

    Route::get('/toko', [SellerStoreController::class, 'edit'])->name('store.edit');
    Route::put('/toko', [SellerStoreController::class, 'update'])->name('store.update');

    Route::get('/produk', [SellerPlantController::class, 'index'])->name('plants.index');
    Route::get('/produk/create', [SellerPlantController::class, 'create'])->name('plants.create');
    Route::post('/produk', [SellerPlantController::class, 'store'])->name('plants.store');
    Route::get('/produk/{plant:slug}/edit', [SellerPlantController::class, 'edit'])->name('plants.edit');
    Route::put('/produk/{plant:slug}', [SellerPlantController::class, 'update'])->name('plants.update');
    Route::delete('/produk/{plant:slug}', [SellerPlantController::class, 'destroy'])->name('plants.destroy');

    Route::get('/pesanan', [SellerOrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/export', [SellerOrderController::class, 'exportCsv'])->name('orders.export');
    Route::patch('/pesanan/{order}/status', [SellerOrderController::class, 'updateStatus'])->name('orders.status');
});

// --------------------------------------------------- Admin platform
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/pengguna/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
    Route::patch('/pengguna/{user}/status', [AdminUserController::class, 'toggleStatus'])->name('users.status');

    Route::get('/toko', [AdminStoreController::class, 'index'])->name('stores.index');
    Route::patch('/toko/{store:slug}/status', [AdminStoreController::class, 'updateStatus'])->name('stores.status');

    Route::get('/produk', [AdminPlantController::class, 'index'])->name('plants.index');
    Route::patch('/produk/{plant:slug}/toggle', [AdminPlantController::class, 'toggleActive'])->name('plants.toggle');
    Route::delete('/produk/{plant:slug}', [AdminPlantController::class, 'destroy'])->name('plants.destroy');

    Route::get('/kategori', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/kategori', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/kategori/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/kategori/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/export', [AdminOrderController::class, 'exportCsv'])->name('orders.export');
});

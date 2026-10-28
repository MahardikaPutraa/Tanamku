<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Order;
use App\Models\Plant;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

echo "\n=======================================================\n";
echo "       TANAMKU - DOM & FUNCTIONAL INSPECTION REPORT    \n";
echo "=======================================================\n\n";

$testsPassed = 0;
$testsFailed = 0;

function runDomCheck($label, $url, $method = 'GET', $actingAs = null, $assertions = [])
{
    global $kernel, $testsPassed, $testsFailed;

    if ($actingAs) {
        auth()->login($actingAs);
    } else {
        auth()->logout();
    }

    $request = Request::create($url, $method);
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    $content = $response->getContent();

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($content);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    $pageOk = ($status === 200 || $status === 302);
    $results = [];

    foreach ($assertions as $desc => $xpathQuery) {
        $nodes = $xpath->query($xpathQuery);
        $found = ($nodes && $nodes->length > 0);
        $results[] = [
            'desc' => $desc,
            'ok' => $found,
            'count' => $nodes ? $nodes->length : 0,
        ];
        if (!$found) {
            $pageOk = false;
        }
    }

    if ($pageOk) {
        $testsPassed++;
        echo " [\033[32mPASS\033[0m] " . str_pad($label, 36) . " [HTTP $status]\n";
    } else {
        $testsFailed++;
        echo " [\033[31mFAIL\033[0m] " . str_pad($label, 36) . " [HTTP $status]\n";
    }

    foreach ($results as $r) {
        if ($r['ok']) {
            echo "        \033[32m✓\033[0m " . $r['desc'] . " (" . $r['count'] . " node)\n";
        } else {
            echo "        \033[31m✗\033[0m " . $r['desc'] . " (0 node)\n";
        }
    }
    echo "\n";
}

$buyer = User::where('role', 'buyer')->first();
$seller = User::where('role', 'seller')->first();
$admin = User::where('role', 'admin')->first();
$plant = Plant::first();
$store = Store::where('status', 'aktif')->first();
$order = Order::first();

// 1. Publik
runDomCheck('1. Beranda (Home)', '/', 'GET', null, [
    'Tag Title & Favicon' => '//head//title',
    'Hero H1 & CTA' => '//h1',
    'Trust Bar 4 Pilar' => '//section[contains(@class, "border-b")]//h4',
    'Kartu Kategori' => '//a[contains(@href, "/katalog?kategori=")]',
    'Produk Unggulan Cards' => '//a[contains(@href, "/tanaman/")]',
]);

runDomCheck('2. Katalog Tanaman', '/katalog', 'GET', null, [
    'Input Pencarian q' => '//form//input[@name="q"]',
    'Filter Kategori' => '//form//select[@name="kategori"]',
    'Filter Urut Harga' => '//form//select[@name="urut"]',
    'Filter Kota Toko' => '//form//select[@name="kota"]',
    'Filter Harga Min-Max' => '//form//input[@name="harga_min"]',
    'Tombol Terapkan Filter' => '//form//button[@type="submit"]',
    'Kartu Produk Tanaman' => '//div[contains(@class, "group") and contains(@class, "rounded-xl")]',
]);

if ($plant) {
    runDomCheck('3. Detail Tanaman (' . $plant->slug . ')', '/tanaman/' . $plant->slug, 'GET', null, [
        'Nama Tanaman (H1)' => '//h1',
        'Harga Tanaman' => '//*[contains(text(), "Rp ")]',
        'Highlight Spesifikasi' => '//span[contains(text(), "Kategori")]',
        'Tombol Bagikan WhatsApp' => '//a[contains(@href, "wa.me")]',
        'Section Ulasan Pembeli' => '//*[@id="ulasan"]',
    ]);
}

if ($store) {
    runDomCheck('4. Profil Toko (' . $store->slug . ')', '/toko/' . $store->slug, 'GET', null, [
        'Nama Toko' => '//h1[contains(text(), "' . $store->nama_toko . '")]',
        'Kota & Metode Serah' => '//*[contains(text(), "' . $store->kota . '")]',
        'Tombol WhatsApp Toko' => '//a[contains(@href, "wa.me")]',
    ]);
}

runDomCheck('5. Halaman Login', '/login', 'GET', null, [
    'Input Email & Password' => '//form//input[@name="email"]',
    'Token CSRF' => '//form//input[@name="_token"]',
    '3 Tombol Pintasan Demo UTS' => '//button[contains(@onclick, "isiAkunDemo")]',
    'Tombol Submit' => '//form//button[@type="submit"]',
]);

runDomCheck('6. Halaman Register', '/register', 'GET', null, [
    'Input Nama, Email, No HP' => '//form//input[@name="name"]',
    'Pilihan Peran Pembeli/Penjual' => '//input[@name="role"]',
    'Token CSRF' => '//form//input[@name="_token"]',
]);

// 2. Pembeli
if ($buyer) {
    runDomCheck('7. Keranjang Pembeli', '/keranjang', 'GET', $buyer, [
        'Judul Keranjang' => '//h1[contains(text(), "Keranjang")]',
        'Lanjut Belanja Link' => '//a[contains(@href, "/katalog")]',
    ]);

    if ($store && $plant) {
        \App\Models\CartItem::firstOrCreate(
            ['user_id' => $buyer->id, 'plant_id' => $plant->id],
            ['jumlah' => 1]
        );

        runDomCheck('8. Checkout Pesanan', '/checkout/' . $store->slug, 'GET', $buyer, [
            'Stepper Alur Checkout' => '//*[contains(text(), "Data Pengiriman")]',
            'Input Nama Penerima' => '//form//input[@name="nama_penerima"]',
            'Input No HP' => '//form//input[@name="no_hp"]',
            'Textarea Alamat' => '//form//textarea[@name="alamat"]',
            'Pilihan Metode Serah' => '//form//input[@name="metode_serah"]',
            'Tombol Submit WhatsApp' => '//form//button[@type="submit"]',
        ]);
    }

    runDomCheck('9. Pesanan Saya', '/pesanan', 'GET', $buyer, [
        'H1 Pesanan Saya' => '//h1[contains(text(), "Pesanan Saya")]',
    ]);

    if ($order) {
        runDomCheck('10. Detail Pesanan (' . $order->kode_pesanan . ')', '/pesanan/' . $order->id, 'GET', $buyer, [
            'Kode Pesanan' => '//*[contains(text(), "' . $order->kode_pesanan . '")]',
            'Status Badge' => '//*[contains(text(), "' . $order->status_label . '")]',
            'Tabel Produk' => '//*[contains(text(), "Total")]',
        ]);
    }
}

// 3. Penjual
if ($seller) {
    runDomCheck('11. Dashboard Penjual', '/seller/dashboard', 'GET', $seller, [
        'Kartu Statistik Omzet' => '//*[contains(text(), "Omzet")]',
        'Kartu Stok Menipis' => '//*[contains(text(), "Stok Menipis")]',
        'Navigasi Sidebar Penjual' => '//aside//a[contains(@href, "/seller/")]',
    ]);

    runDomCheck('12. Pengaturan Toko Penjual', '/seller/toko', 'GET', $seller, [
        'Form Nama Toko' => '//form//input[@name="nama_toko"]',
        'Form WhatsApp' => '//form//input[@name="no_wa"]',
        'Form Kota' => '//form//input[@name="kota"]',
    ]);

    runDomCheck('13. Katalog Produk Penjual', '/seller/produk', 'GET', $seller, [
        'Tombol Tambah Produk' => '//a[contains(@href, "/seller/produk/create")]',
        'Tabel Daftar Produk' => '//table',
    ]);

    runDomCheck('14. Form Tambah Produk', '/seller/produk/create', 'GET', $seller, [
        'Input Nama Produk' => '//form//input[@name="nama"]',
        'Select Kategori' => '//form//select[@name="category_id"]',
        'Input Harga' => '//form//input[@name="harga"]',
        'Input Stok' => '//form//input[@name="stok"]',
        'Upload Foto' => '//form//input[@name="foto"]',
    ]);

    runDomCheck('15. Pesanan Masuk Penjual', '/seller/pesanan', 'GET', $seller, [
        'Tombol Unduh CSV Laporan' => '//a[contains(@href, "/seller/pesanan/export")]',
        'Filter Status Pesanan' => '//*[contains(text(), "Semua")]',
        'Tabel Pesanan' => '//table',
    ]);
}

// 4. Admin
if ($admin) {
    // Ensure at least one store and one category exist for admin pages
    if (App\\Models\\Store::count() === 0) {
        App\\Models\\Store::factory()->create([
            'nama_toko' => 'Demo Toko',
            'slug' => 'demo-toko',
            'kota' => 'Jakarta',
            'status' => 'aktif',
            'metode_serah' => 'kirim',
        ]);
    }
    if (App\\Models\\Category::count() === 0) {
        App\\Models\\Category::factory()->create([
            'nama' => 'Demo Kategori',
            'slug' => 'demo-kategori',
        ]);
    }

    runDomCheck('16. Dashboard Admin', '/admin/dashboard', 'GET', $admin, [
        'Statistik Pembeli' => '//*[contains(text(), "Pembeli")]',
        'Statistik Penjual' => '//*[contains(text(), "Penjual")]',
        'Sidebar Navigasi Admin' => '//aside//a[contains(@href, "/admin/")]',
    ]);

    runDomCheck('17. Kelola Pengguna (Admin)', '/admin/pengguna', 'GET', $admin, [
        'Tabel Pengguna' => '//table',
        'Baris Pengguna' => '//table//tbody//tr',
    ]);

    runDomCheck('18. Kelola Toko (Admin)', '/admin/toko', 'GET', $admin, [
        'Tabel Toko' => '//table',
        'Baris Toko' => '//table//tbody//tr',
    ]);

    runDomCheck('19. Kelola Kategori (Admin)', '/admin/kategori', 'GET', $admin, [
        'Form Tambah Kategori' => '//form//input[@name="nama"]',
        'Tabel Kategori' => '//table',
    ]);

    runDomCheck('20. Rekap Pesanan (Admin)', '/admin/pesanan', 'GET', $admin, [
        'Tombol Unduh CSV Marketplace' => '//a[contains(@href, "/admin/pesanan/export")]',
        'Tabel Seluruh Pesanan' => '//table',
    ]);
}

echo "=======================================================\n";
echo " TOTAL SELESAI: $testsPassed Halaman PASS, $testsFailed FAIL\n";
echo "=======================================================\n";

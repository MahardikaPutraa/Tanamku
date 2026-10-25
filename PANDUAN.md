# TanamKu — Panduan Menjalankan (Marketplace)

Platform marketplace tanaman: mempertemukan **pembeli**, **penjual**, dan **admin**.
Pesanan diteruskan ke WhatsApp penjual; TanamKu tidak memproses pembayaran.

Spesifikasi lengkap: **`ARSITEKTUR.md`** (use case, ERD, peta halaman).

---

## Menjalankan

```bash
C:\xampp\php\php.exe artisan serve --port=8123
```

Buka **http://127.0.0.1:8123**

> PHP yang dipakai adalah PHP bawaan XAMPP. Kalau ingin `php artisan ...` biasa,
> tambahkan `C:\xampp\php` ke Environment Variables → Path.

Reset data ke kondisi awal:

```bash
C:\xampp\php\php.exe artisan migrate:fresh --seed
```

---

## Akun Demo

| Peran | Email | Password | Catatan |
|---|---|---|---|
| Admin | `admin@tanamku.test` | `password` | Mengelola toko, pengguna, produk, kategori |
| Penjual 1 | `seller@tanamku.test` | `password` | Toko **Kebun Hijau Jaya** (aktif, 5 produk) |
| Penjual 2 | `seller2@tanamku.test` | `password` | Toko **Rumah Daun Bandung** (aktif, 5 produk) |
| Penjual 3 | `seller3@tanamku.test` | `password` | Toko **Kaktus Corner** (menunggu persetujuan) |
| Pembeli 1 | `buyer@tanamku.test` | `password` | Rina — punya riwayat pesanan |
| Pembeli 2 | `buyer2@tanamku.test` | `password` | Budi — punya riwayat pesanan |

**Skenario demo yang bagus:**
1. Login admin → `/admin/toko` → setujui **Kaktus Corner** → produknya langsung
   muncul di katalog (buka di tab lain untuk menunjukkan perubahannya).
2. Login pembeli → tambah 2 produk dari toko berbeda → keranjang terpisah per toko
   → checkout salah satu → tombol WhatsApp dengan pesan terisi.
3. Login penjual → lihat pesanan masuk → ubah status.

---

## Cara masuk panel (tanpa tombol di UI)

- `/login` — form login semua peran
- `/masuk` — pintu pintar: otomatis diarahkan sesuai peran yang sedang login

Setelah login, navbar menampilkan menu sesuai peran (Keranjang / Dashboard Toko /
Dashboard Admin).

**Khusus admin:** tidak ada beranda publik. Membuka `/` saat login sebagai admin
otomatis dialihkan ke `/admin/dashboard`, dan panel admin tidak memakai navbar atas
(navigasi sepenuhnya lewat sidebar). Halaman publik lain seperti `/katalog` dan
`/toko/{slug}` tetap bisa dibuka admin untuk keperluan moderasi.

---

## Alur utama

### Pembeli
Katalog → detail produk → **Tambah ke Keranjang** → `/keranjang` (dikelompokkan per
toko) → **Checkout via WhatsApp** → isi alamat → pesanan dibuat, stok berkurang →
halaman pesanan menampilkan tombol **Buka WhatsApp** dengan pesan siap kirim.

### Penjual
Daftar → isi profil toko → toko berstatus **menunggu** → admin setujui → unggah
produk (foto + cara merawat) → terima pesanan → ubah status.

### Admin
Dashboard → setujui/nonaktifkan toko → moderasi produk (sembunyikan/hapus) →
kelola peran & status akun → kelola kategori → pantau pesanan.

---

## Struktur Kode

```
app/Http/Controllers/
├── HomeController.php          # landing
├── PlantController.php         # katalog + detail
├── StoreController.php         # halaman publik toko
├── CartController.php          # keranjang (CRUD item)
├── CheckoutController.php      # form + buat pesanan + kurangi stok
├── OrderController.php         # pesanan pembeli + detail
├── AuthController.php          # login/register (pilih peran)
├── Seller/
│   ├── DashboardController.php # statistik toko
│   ├── StoreController.php     # profil toko
│   ├── PlantController.php     # CRUD produk milik sendiri
│   └── OrderController.php     # pesanan masuk + ubah status
└── Admin/
    ├── DashboardController.php # statistik platform
    ├── StoreController.php     # approve / nonaktifkan toko
    ├── PlantController.php     # moderasi produk
    ├── UserController.php      # peran & status akun
    ├── CategoryController.php  # CRUD kategori
    └── OrderController.php     # pantau semua pesanan

app/Models/          User, Store, Category, Plant, CartItem, Order, OrderItem
app/Services/        WhatsAppMessage.php   # penyusun teks & URL wa.me
app/Http/Middleware/ EnsureRole.php        # middleware 'role:seller|admin|buyer'
```

---

## Catatan Teknis

- **Database:** SQLite (`database/database.sqlite`).
- **Peran dijaga di server:** middleware `role:` pada setiap grup route. Pembeli yang
  membuka `/admin/*` mendapat **403**, bukan sekadar menu disembunyikan.
- **Total harga** dihitung server-side (`harga × jumlah` dari database), tidak pernah
  dari input browser.
- **Stok** berkurang saat checkout; kembali otomatis bila penjual membatalkan pesanan.
- **Snapshot** nama & harga produk disimpan di `order_items`.
- **Foto** produk & logo toko disimpan di `storage/app/public/{plants|stores}`.
  Symlink `public/storage` sudah ada; kalau hilang: `php artisan storage:link`.
- **Route model binding** memakai slug untuk `Plant` dan `Store`.
- **Nomor WhatsApp** dinormalisasi `08xx` → `62xx` oleh `Store::wa_number`.
- **Registrasi** hanya menerima peran `buyer` / `seller`; `admin` tidak bisa dipilih.

---

## Verifikasi

```bash
C:\xampp\php\php.exe artisan route:list --except-vendor   # 43 route
C:\xampp\php\php.exe artisan test                          # 15 test, 53 assertion
C:\xampp\php\php.exe artisan migrate:status
```

Test mencakup: isolasi akses antar peran, keranjang tidak melebihi stok, checkout
mengurangi stok & mengosongkan keranjang, format pesan WhatsApp, produk toko yang
belum disetujui tidak tampil, dan registrasi tidak bisa membuat admin.

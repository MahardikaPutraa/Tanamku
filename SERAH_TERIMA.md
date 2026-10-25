# Serah Terima Proyek TanamKu

Dokumen ini untuk pengembang berikutnya yang melanjutkan **bagian database & deployment**.
Tulisannya dibuat sebagai instruksi kerja, bukan teori.

**Status terakhir:** aplikasi berjalan penuh di lokal dengan SQLite. 18 test otomatis
lolos (67 assertion). Yang belum: database produksi (Neon/Postgres) dan deploy.

---

## 1. Apa proyek ini

TanamKu adalah **marketplace tanaman hias** berbasis Laravel 12 + Blade. Tiga peran:

| Peran | Bisa apa |
|---|---|
| **Pembeli** | Lihat katalog, keranjang, checkout, pesan diteruskan ke WhatsApp penjual |
| **Penjual** | Kelola profil toko + produk (foto, harga, stok, cara merawat), terima pesanan |
| **Admin** | Setujui toko, moderasi produk, kelola pengguna & kategori, pantau pesanan |

TanamKu **tidak memproses pembayaran**. Tidak ada payment gateway, tidak ada tabel
transaksi keuangan. Pesanan dicatat, lalu pembeli diarahkan ke `wa.me` penjual.

Baca juga **`ARSITEKTUR.md`** (use case, ERD lengkap, peta 21 halaman) dan
**`PANDUAN.md`** (cara menjalankan).

---

## 2. Stack & versi (jangan diubah tanpa alasan)

| Komponen | Versi |
|---|---|
| PHP | 8.2.12 (XAMPP) |
| Laravel | 12.70.1 |
| Database lokal | SQLite 3.39.2 — `database/database.sqlite` |
| CSS | Tailwind via CDN (tanpa build step / npm) |
| Chart | Chart.js via CDN |
| Test | PHPUnit — 18 test, 67 assertion |

Tidak ada `npm install`, tidak ada Vite build. Semua CSS/JS lewat CDN.

---

## 3. Menjalankan di komputer kamu

### 3.1 Prasyarat
- PHP 8.2+ dengan ekstensi `pdo_sqlite` (sudah ada di XAMPP)
- Composer
- Untuk Neon nanti: **`pdo_pgsql`** — lihat bagian 4.1, ini belum aktif

### 3.2 Langkah
```bash
# dari folder proyek
C:\xampp\php\php.exe composer install

copy .env.example .env
C:\xampp\php\php.exe artisan key:generate

C:\xampp\php\php.exe artisan migrate:fresh --seed
C:\xampp\php\php.exe artisan storage:link
C:\xampp\php\php.exe artisan serve --port=8123
```

Buka `http://127.0.0.1:8123`

> Catatan: di mesin sebelumnya `php` tidak ada di PATH, jadi semua perintah memakai
> path absolut `C:\xampp\php\php.exe`. Kalau di mesinmu `php` sudah di PATH, cukup
> `php artisan ...`.

### 3.3 Akun demo (dari seeder)

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@tanamku.test` | `password` |
| Penjual 1 | `seller@tanamku.test` | `password` |
| Penjual 2 | `seller2@tanamku.test` | `password` |
| Penjual 3 (toko menunggu) | `seller3@tanamku.test` | `password` |
| Pembeli 1 | `buyer@tanamku.test` | `password` |
| Pembeli 2 | `buyer2@tanamku.test` | `password` |

Akun ini **sengaja tidak ditampilkan di halaman login** — jangan tambahkan lagi.

---

## 4. TUGAS UTAMA — pindah SQLite → Neon (Postgres)

Neon itu **PostgreSQL**, bukan MySQL. Ada tiga hal yang akan menggigit kalau
langsung pindah tanpa persiapan. Kerjakan berurutan.

### 4.1 Aktifkan `pdo_pgsql` di PHP

Saat dicek, XAMPP punya DLL-nya tapi belum diaktifkan:

```
C:\xampp\php\ext\php_pdo_pgsql.dll     (ada)
C:\xampp\php\ext\php_pgsql.dll         (ada)

C:\xampp\php\php.ini
  baris 947:  ;extension=pdo_pgsql      ← hapus titik komanya
  baris 949:  ;extension=pgsql          ← hapus titik komanya
```

Restart terminal, lalu verifikasi:
```bash
C:\xampp\php\php.exe -m | findstr pgsql
```
Harus muncul `pdo_pgsql` dan `pgsql`. Tanpa ini Laravel akan error
`could not find driver`.

### 4.2 Buat project di Neon

1. Daftar di https://neon.tech → **New Project** (pilih region terdekat, mis. Singapore)
2. Buka **Connection Details**, ada dua jenis koneksi:

| Jenis | Host | Dipakai untuk |
|---|---|---|
| **Pooled** | `...-pooler.<region>.aws.neon.tech` | **Aplikasi Laravel** (disarankan) |
| Direct | `...<region>.aws.neon.tech` | Migrasi / admin tool |

Pooled wajib untuk aplikasi karena Neon membatasi jumlah koneksi langsung.
Kalau salah pakai, akan muncul error `too many connections` begitu ada beberapa
pengunjung bersamaan.

3. Catat: `host`, `database` (biasanya `neondb`), `user`, `password`, dan pastikan
   `sslmode=require` (Neon menolak koneksi tanpa SSL).

### 4.3 Ubah `.env`

```env
DB_CONNECTION=pgsql
DB_HOST=ep-xxxx-pooler.ap-southeast-1.aws.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=neondb_owner
DB_PASSWORD=password_dari_neon
DB_SSLMODE=require
```

Alternatif satu baris (Laravel mendukung `DB_URL`):
```env
DB_CONNECTION=pgsql
DB_URL="postgresql://user:pass@ep-xxxx-pooler.ap-southeast-1.aws.neon.tech/neondb?sslmode=require"
```

Sekalian sesuaikan, karena Neon serverless sering menutup koneksi saat idle:
```env
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 4.4 WAJIB — perbaiki 7 query pencarian

SQLite: `LIKE` **tidak** membedakan huruf besar/kecil.
Postgres: `LIKE` **membedakan**. Akibatnya pencarian "monstera" tidak menemukan
"Monstera Deliciosa" setelah pindah.

Jangan pakai `ilike` (tidak ada di SQLite, jadi lokal kamu rusak). Pakai
**`whereLike()`** milik Laravel 12 — otomatis menyesuaikan driver:

```php
// SEBELUM
->where('nama', 'like', '%' . $request->q . '%')

// SESUDAH — jalan di SQLite maupun Postgres
->whereLike('nama', '%' . $request->q . '%')
```

Daftar lengkap yang harus diubah:

| File | Baris | Kolom |
|---|---|---|
| `app/Http/Controllers/PlantController.php` | 18 | `nama` |
| `app/Http/Controllers/Seller/PlantController.php` | 22 | `nama` |
| `app/Http/Controllers/Admin/PlantController.php` | 18 | `nama` |
| `app/Http/Controllers/Admin/StoreController.php` | 16 | `nama_toko` |
| `app/Http/Controllers/Admin/OrderController.php` | 20 | `kode_pesanan` |
| `app/Http/Controllers/Admin/UserController.php` | 17 | `name` |
| `app/Http/Controllers/Admin/UserController.php` | 18 | `email` |

Khusus `UserController` yang memakai `orWhere`, ubah jadi:

```php
->where(fn ($w) => $w
    ->whereLike('name', '%' . $request->q . '%')
    ->orWhereLike('email', '%' . $request->q . '%'))
```

### 4.5 Yang aman, tidak perlu diubah

Sudah saya periksa — ini semua **kompatibel** dengan Postgres:

- `selectRaw('status, count(*) as jumlah')->groupBy('status')` di 4 controller admin/seller
  → valid di Postgres (semua kolom non-agregat ada di `GROUP BY`)
- Tidak ada `DB::statement`, tidak ada SQL mentah, tidak ada trik khusus SQLite
- `decimal(12,2)`, `foreignId()->constrained()`, `enum`, `boolean`, `timestamps`
  semuanya diterjemahkan Laravel dengan benar

### 4.6 Soal kolom `enum` — baca sebelum mengubah nilai

Ada 6 kolom enum:

| Tabel | Kolom | Nilai |
|---|---|---|
| `users` | `role` | admin, seller, buyer |
| `stores` | `metode_serah` | ambil, kirim, keduanya |
| `stores` | `status` | menunggu, aktif, nonaktif |
| `plants` | `kondisi` | sehat, perlu_perhatian |
| `orders` | `metode_serah` | ambil, kirim |
| `orders` | `status` | baru, diproses, selesai, batal |

Di Postgres, `$table->enum()` dibuat sebagai `varchar` + **CHECK constraint**.
Jalan normal, **tapi** kalau nanti perlu menambah nilai baru (misal status
`dikirim`), constraint-nya harus di-drop dulu — ribet.

**Rekomendasi:** kalau mau menambah nilai status, ubah dulu kolomnya jadi `string`
lewat migration baru, dan pindahkan validasinya ke FormRequest/controller.
Lakukan **sebelum** ada data produksi.

### 4.7 Jalankan migrasi ke Neon

```bash
C:\xampp\php\php.exe artisan migrate:fresh --seed
```

`--seed` penting: tanpa itu tidak ada kategori, tidak ada admin, dan aplikasi
tidak bisa dipakai.

Kalau `migrate:fresh` ditolak karena Neon pooled connection:
pakai host **direct** (tanpa `-pooler`) khusus untuk perintah migrasi, lalu
kembalikan ke pooled untuk aplikasi.

### 4.8 Verifikasi setelah pindah

Jalankan berurutan, jangan ada yang dilewat:

```bash
C:\xampp\php\php.exe artisan db:show          # harus "pgsql", bukan sqlite
C:\xampp\php\php.exe artisan migrate:status   # semua Ran
C:\xampp\php\php.exe artisan test             # HARUS 18 passed
```

Lalu uji manual di browser:

- [ ] `/katalog` menampilkan 10 produk (2 produk toko "menunggu" tidak boleh tampil)
- [ ] Cari "monstera" **dan** "Monstera" → dua-duanya menemukan hasil (uji `whereLike`)
- [ ] Login pembeli → tambah ke keranjang → checkout → pesanan dibuat, stok berkurang
- [ ] Halaman pesanan menampilkan tombol WhatsApp dengan nomor `62...`
- [ ] Login penjual → ubah status pesanan → batal → **stok kembali**
- [ ] Login admin → setujui toko "Kaktus Corner" → produknya muncul di katalog
- [ ] Pesanan berstatus batal → tombol WhatsApp **tidak** muncul

### 4.9 Jebakan Neon yang perlu diketahui

| Masalah | Penyebab | Solusi |
|---|---|---|
| `too many connections` | pakai host direct | pakai host `-pooler` |
| Request pertama lambat (2–5 detik) | Neon auto-suspend saat idle | normal di free tier; naikkan plan kalau mengganggu |
| `SSL required` | lupa `sslmode` | tambahkan `?sslmode=require` |
| `could not find driver` | `pdo_pgsql` belum aktif | lihat 4.1 |
| Migrasi gagal di pooled | PgBouncer tidak suport sebagian DDL | pakai host direct untuk migrasi |

---

## 5. TUGAS KEDUA — deploy

### 5.1 Pilih platform

| Platform | Cocok? | Catatan |
|---|---|---|
| **Railway** | ✅ paling cepat | deteksi PHP otomatis, env vars gampang |
| **Render** | ✅ | free tier tidur setelah idle |
| **Fly.io** | ✅ | butuh Dockerfile |
| **VPS (nginx + php-fpm)** | ✅ paling fleksibel | kamu urus sendiri |
| Shared hosting / cPanel | ⚠️ | biasanya MySQL saja; Postgres ke Neon bisa, tapi `pdo_pgsql` sering tidak tersedia |
| Vercel / Netlify | ❌ | bukan untuk PHP |

### 5.2 Environment variables produksi

```env
APP_NAME=TanamKu
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...              # php artisan key:generate --show
APP_URL=https://domainkamu.com
APP_LOCALE=id
APP_FALLBACK_LOCALE=id

DB_CONNECTION=pgsql
DB_URL="postgresql://...-pooler.../neondb?sslmode=require"

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

LOG_CHANNEL=stack
LOG_LEVEL=error
```

`APP_DEBUG=false` wajib — kalau `true`, halaman error menampilkan isi `.env`
termasuk password database.

### 5.3 Perintah build & rilis

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

`storage:link` tidak boleh dilewat: symlink `public/storage` **tidak ikut Git**
(sudah di `.gitignore`), jadi harus dibuat di server. Tanpa itu semua foto produk
menjadi gambar rusak.

### 5.4 ⚠️ Masalah terbesar saat deploy: file upload

Sekarang foto produk & logo toko disimpan di **disk lokal server**:
`storage/app/public/{plants,stores}`.

Di platform seperti Render/Railway/Fly, filesystem bersifat **ephemeral** — setiap
deploy ulang, semua foto yang diunggah **hilang**. Foto 12 produk awal aman karena
ikut di repo, tapi upload baru akan lenyap.

Tiga pilihan, pilih satu sebelum go-live:

1. **Object storage (disarankan)** — Cloudflare R2 / AWS S3 / Supabase Storage.
   Butuh: `composer require league/flysystem-aws-s3-v3`, isi kredensial di `.env`,
   lalu ubah semua `->store('plants', 'public')` jadi `->store('plants', 's3')`
   di `Seller/PlantController` dan `Seller/StoreController`.
   **Perhatian:** view memakai `asset('storage/' . $foto)` di 6 file. Semua harus
   diganti helper agar membaca disk yang aktif, misal:
   ```php
   Storage::disk(config('filesystems.default'))->url($foto)
   ```
2. **Volume persisten** — kalau platform mendukung (Fly volume, Railway volume).
   Paling sedikit perubahan kode, tapi terikat platform.
3. **VPS** — filesystem permanen, tidak ada masalah. Paling simpel secara teknis,
   paling repot secara operasional.

### 5.5 Git

Folder ini **belum** berupa repo Git. Langkah pertama:

```bash
git init
git add .
git commit -m "TanamKu: marketplace tanaman - baseline sebelum Neon"
```

Dua hal yang perlu diputuskan sebelum push:

- `database/database.sqlite` **tidak** ada di `.gitignore` bawaan. Kalau ingin
  temanmu punya data contoh, biarkan ikut. Kalau tidak, tambahkan
  `database/*.sqlite` ke `.gitignore`.
- `storage/app/public/plants/*.jpg` (2,3 MB, 12 foto) **ikut** ke repo — memang
  perlu, supaya produk seed punya foto.

---

## 6. Yang JANGAN diubah (kontrak desain)

Ini keputusan yang sudah dibahas dan dipakai sebagai bahan penilaian. Kalau mau
mengubah, diskusikan dulu:

1. **Satu layout** — `resources/views/layouts/app.blade.php` dipakai semua halaman.
   Jangan bikin layout kedua.
2. **Total harga dihitung server-side** — `harga × jumlah` dari database, tidak
   pernah dari input browser.
3. **Snapshot di `order_items`** — `nama_produk` dan `harga_satuan` dibekukan saat
   checkout. Jangan diganti jadi join ke `plants`, atau riwayat pesanan berubah
   saat penjual menaikkan harga.
4. **Checkout dipisah per toko** — satu pesan WhatsApp = satu penjual.
5. **Stok berkurang saat checkout**, kembali otomatis bila penjual membatalkan.
6. **Toko harus disetujui admin** sebelum produknya tampil di katalog.
7. **Registrasi hanya buyer/seller** — peran admin tidak pernah bisa dipilih dari form.
8. **Admin tidak punya beranda publik** — `/` dialihkan ke panel.
9. **Tombol WhatsApp hilang bila pesanan `batal`.**
10. **Peran dijaga di server** lewat middleware `role:` — bukan sekadar menu disembunyikan.

---

## 7. Checklist serah terima

**Database**
- [ ] `pdo_pgsql` aktif
- [ ] Project Neon dibuat, connection string (pooled) dicatat
- [ ] `.env` menunjuk ke Neon, `sslmode=require`
- [ ] 7 query `like` → `whereLike` sudah diubah
- [ ] `migrate:fresh --seed` sukses
- [ ] `db:show` menampilkan `pgsql`
- [ ] `artisan test` → 18 passed

**Deploy**
- [ ] Platform dipilih
- [ ] Semua env var produksi terisi, `APP_DEBUG=false`
- [ ] `APP_KEY` di-generate
- [ ] `storage:link` dijalankan
- [ ] Strategi penyimpanan foto diputuskan (S3 / volume / VPS)
- [ ] HTTPS aktif
- [ ] Uji 8 skenario di bagian 4.8 di domain produksi

**Dokumentasi**
- [ ] `ARSITEKTUR.md` diperbarui kalau ada perubahan skema
- [ ] `PANDUAN.md` diperbarui kalau ada perubahan cara jalan
- [ ] `.env.example` disesuaikan dengan variabel baru

---

## 8. Ide pengembangan lanjutan (belum dikerjakan)

Tidak wajib, tapi ini yang paling masuk akal untuk dilanjutkan:

1. **Ulasan & rating produk** — tabel `reviews` (order_id, buyer_id, rating, komentar),
   hanya boleh diisi setelah pesanan `selesai`.
2. **Notifikasi pesanan masuk** ke penjual (email/WhatsApp API).
3. **Pencarian dengan filter harga & kota** — sekarang hanya nama, kategori, urutan harga.
4. **Upload multi-foto per produk** — sekarang 1 foto.
5. **Soft delete** untuk produk & toko, agar riwayat tidak bergantung pada flag `is_active`.
6. **Rate limiting** pada endpoint checkout untuk mencegah spam pesanan.
7. **Pagination pada keranjang** kalau item banyak (sekarang tampil semua).

---

## 9. Kalau ada yang rusak

| Gejala | Cek dulu |
|---|---|
| `could not find driver` | `pdo_pgsql` belum aktif (4.1) |
| `too many connections` | pakai host `-pooler` |
| Foto produk jadi ikon rusak | `php artisan storage:link` |
| Login gagal terus | `sessions` table ada? `SESSION_DRIVER=database` butuh tabel itu |
| Pencarian tidak menemukan hasil | query `like` belum diganti `whereLike` |
| Error 419 saat submit form | `@csrf` hilang, atau cookie/session tidak tersimpan |
| Halaman putih | `storage/logs/laravel.log`, atau `APP_DEBUG=true` sementara |

---

*Dokumen ini dibuat pada kondisi commit terakhir sebelum pindah ke Neon.
Kalau ada perubahan besar, perbarui bagian yang relevan — jangan biarkan dokumen
menjadi bohong.*

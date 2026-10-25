# PRD — TanamKu

**Produk:** TanamKu — Katalog & Pemesanan Tanaman Hias Online
**Versi dokumen:** 1.0
**Konteks:** Tugas Ujian Tengah Semester, mata kuliah Pemrograman Web Enterprise
**Stack:** Laravel (Blade), Tailwind CSS, Chart.js, SQLite/MySQL
**Status:** Siap eksekusi

---

## 1. Ringkasan Produk

TanamKu adalah aplikasi web sederhana yang berfungsi sebagai **etalase digital dan kanal pemesanan** untuk penjual tanaman hias skala rumahan (UMKM). Aplikasi terdiri dari dua sisi:

1. **Sisi publik** — pengunjung dapat melihat landing page, menelusuri katalog tanaman, memfilter berdasarkan kategori, melihat detail tanaman, dan mengirim pesanan.
2. **Sisi admin** — pemilik toko masuk ke dashboard untuk memantau statistik penjualan, mengelola data tanaman dan kategori, serta memproses pesanan yang masuk.

Satu kalimat positioning: *"Instagram toko tanaman, tapi punya sistem."*

---

## 2. Latar Belakang & Masalah

Penjual tanaman hias rumahan umumnya berjualan lewat DM Instagram atau WhatsApp. Masalah yang muncul:

- **Katalog tidak terstruktur** — pembeli harus scroll postingan lama untuk melihat stok yang masih ada.
- **Tidak ada informasi stok** — pembeli menanyakan ketersediaan satu per satu, penjual menjawab berulang.
- **Pesanan tercampur** dengan chat pribadi sehingga rawan terlewat.
- **Pemilik tidak punya gambaran** tanaman mana yang paling laku dan mana yang stoknya menipis.

TanamKu menjawab keempat masalah itu dengan satu katalog terpusat dan satu dashboard admin.

---

## 3. Tujuan & Metrik Keberhasilan

| Tujuan | Metrik |
|---|---|
| Pembeli dapat menemukan tanaman tanpa bertanya | Katalog dapat difilter per kategori dalam ≤ 2 klik |
| Pembeli tahu ketersediaan sebelum memesan | Setiap tanaman menampilkan status stok (Aman/Menipis/Habis) |
| Pesanan tidak terlewat | Setiap pesanan masuk memiliki kode unik dan status terlacak |
| Pemilik tahu kondisi toko sekilas | Dashboard menampilkan 4 metrik kunci + grafik 7 hari |

Karena ini proyek akademik, metrik di atas dipakai sebagai **kriteria penerimaan fungsional**, bukan target bisnis terukur.

---

## 4. Persona & Peran

### 4.1 Pengunjung (guest)
Pembeli potensial. Tidak punya akun. Tujuan: melihat tanaman, menilai harga & kondisi, lalu memesan.

### 4.2 Admin
Pemilik toko. Punya akun (dibuat lewat seeder, tanpa registrasi publik). Tujuan: menjaga katalog akurat dan memproses pesanan.

### 4.3 Staff (opsional, tidak wajib diimplementasikan)
Peran tambahan di kolom `role` untuk menunjukkan sistem siap multi-user. Bila tidak dipakai, cukup tampil sebagai nilai enum.

---

## 5. Ruang Lingkup

### 5.1 In Scope (wajib)
- Landing page publik dengan hero, kategori unggulan, tanaman unggulan, tips
- Halaman katalog publik dengan pencarian dan filter kategori
- Halaman detail tanaman + form pemesanan (checkout guest)
- Autentikasi admin (login/logout)
- Dashboard admin dengan statistik dan grafik
- CRUD tanaman (termasuk upload foto)
- Pengelolaan status pesanan (minimal di dashboard, idealnya halaman terpisah)
- Data dummy realistis lewat seeder

### 5.2 Out of Scope (tidak dikerjakan — tulis di laporan)
- Payment gateway / integrasi Midtrans-Xendit
- Perhitungan ongkos kirim otomatis
- Akun & login pembeli
- Multi-vendor / marketplace
- Notifikasi email atau WhatsApp
- Wishlist, ulasan, rating produk
- Manajemen stok otomatis berbasis transaksi

### 5.3 Alasan batasan
Enam halaman Blade adalah target penilaian utama. Menambah fitur transaksional berisiko membuat halaman inti setengah jadi. Semua fitur di 5.2 dicatat sebagai **pengembangan lanjutan**, bukan kekurangan.

---

## 6. Alur Pengguna Utama

### 6.1 Alur pembeli (happy path)
1. Pengunjung membuka `/` — melihat hero dan tanaman unggulan
2. Klik "Lihat Katalog" → `/katalog`
3. Memfilter kategori "Tanaman Hias Daun"
4. Klik satu kartu → `/tanaman/monstera-deliciosa`
5. Melihat harga, stok, kondisi, deskripsi
6. Mengisi form pesan: nama, no HP, alamat, jumlah, catatan
7. Submit → pesanan tersimpan, tampil kode pesanan + pesan sukses

### 6.2 Alur admin
1. Buka `/login`, masuk dengan kredensial seeder
2. Diarahkan ke `/admin/dashboard` — melihat 4 stat card, grafik 7 hari, 5 pesanan terbaru, daftar stok menipis
3. Klik "Kelola Tanaman" → `/admin/plants`
4. Cari tanaman, edit harga/stok, atau tambah tanaman baru
5. Upload foto, simpan, kembali ke list dengan notifikasi sukses
6. Kembali ke dashboard, angka statistik ikut berubah

### 6.3 Alur alternatif & error
- Login salah → kembali ke form dengan pesan "Email atau password salah"
- Stok tanaman = 0 → tombol pesan nonaktif, label "Habis"
- Jumlah pesanan melebihi stok → validasi menolak dengan pesan jelas
- Akses `/admin/*` tanpa login → redirect ke `/login`
- Hapus tanaman yang sudah punya pesanan → ditolak (FK restrict) dengan pesan informatif

---

## 7. Kebutuhan Fungsional

Format: **FR-xx** — deskripsi. Diterima bila semua kriteria terpenuhi.

### Modul Publik

**FR-01 — Landing page**
Menampilkan hero (headline, subheadline, CTA), 3 kategori unggulan, grid 6 tanaman dengan `is_featured = true`, section 3 tips perawatan statis, footer.
*Diterima bila:* halaman dapat diakses tanpa login; tanaman unggulan diambil dari database, bukan hardcoded; CTA mengarah ke `/katalog`.

**FR-02 — Katalog**
Menampilkan seluruh tanaman dalam grid kartu (foto, nama, harga, badge stok, kategori). Mendukung pencarian nama dan filter kategori.
*Diterima bila:* search dan filter dapat dikombinasikan; hasil kosong menampilkan empty state; data dipaginasi bila lebih dari 12 item.

**FR-03 — Detail tanaman**
Menampilkan foto besar, nama, harga (format Rupiah), status stok, kondisi, deskripsi, dan form pemesanan.
*Diterima bila:* URL memakai slug (`/tanaman/{slug}`); tanaman tidak ditemukan menghasilkan 404; tombol pesan nonaktif saat stok 0.

**FR-04 — Buat pesanan**
Menyimpan pesanan baru dari form publik.
*Diterima bila:* `kode_pesanan` tergenerate unik otomatis; `total_harga` dihitung server-side (`harga × jumlah`), bukan dari input client; jumlah divalidasi 1..stok; pesanan tersimpan dengan status `baru`; halaman menampilkan kode pesanan setelah sukses.

### Modul Autentikasi

**FR-05 — Login & logout**
Autentikasi admin berbasis session.
*Diterima bila:* kredensial salah ditolak dengan pesan; route `/admin/*` dilindungi middleware `auth`; logout menghapus session dan kembali ke landing; tidak ada route registrasi publik.

### Modul Admin

**FR-06 — Dashboard**
Menampilkan 4 stat card (Total Tanaman, Total Pesanan, Pesanan Baru, Tanaman Stok Menipis), grafik garis total penjualan 7 hari terakhir, tabel 5 pesanan terbaru, dan daftar tanaman dengan stok < 5.
*Diterima bila:* semua angka berasal dari query database; grafik ter-render; badge "Menipis" berwarna merah; halaman tetap rapi saat data kosong.

**FR-07 — CRUD tanaman**
List (search + filter kategori + paginasi), create, edit, delete.
*Diterima bila:* validasi wajib pada nama, kategori, harga (> 0), stok (≥ 0); upload foto menerima jpg/png maks 2 MB dan menyimpan ke `storage/public`; slug auto-generate dari nama dan unik; delete memakai konfirmasi; notifikasi sukses tampil setelah aksi.

**FR-08 — Kelola pesanan (bonus, dikerjakan bila waktu cukup)**
List pesanan dengan filter status, aksi ubah status (`baru → diproses → dikirim → selesai`, atau `batal`).
*Diterima bila:* status hanya bisa berpindah sesuai urutan yang diizinkan; perubahan tercatat dan tampil di tabel.

**FR-09 — Kelola kategori (bonus)**
CRUD kategori sederhana. *Diterima bila:* kategori yang masih dipakai tanaman tidak bisa dihapus.

---

## 8. Kebutuhan Non-Fungsional

- **Konsistensi UI** — satu layout, satu palet warna, satu font, satu gaya tombol di semua halaman.
- **Responsif** — tampil baik di 375 px dan 1440 px (grid 1 kolom → 4 kolom).
- **Aksesibilitas dasar** — setiap `<img>` punya `alt`, input punya `<label>`, kontras teks memadai.
- **Keamanan** — semua form memakai `@csrf`; query lewat Eloquent (tidak ada raw string concat); password di-hash; mass assignment dibatasi `$fillable`; upload divalidasi tipe & ukuran.
- **Performa** — halaman < 2 detik pada data dummy; hindari N+1 dengan `with('category')`.
- **Keterbacaan kode** — controller ramping, validasi dipisah (FormRequest bila memungkinkan), penamaan route konsisten.

---

## 9. Model Data

### 9.1 Tabel `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned, PK | |
| name | varchar(100) | |
| email | varchar(150) | unique |
| password | varchar(255) | hashed |
| role | enum('admin','staff') | default 'staff' |
| remember_token | varchar(100) | nullable |
| timestamps | | |

### 9.2 Tabel `categories`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned, PK | |
| nama | varchar(80) | |
| slug | varchar(100) | unique |
| deskripsi | text | nullable |
| timestamps | | |

### 9.3 Tabel `plants`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned, PK | |
| category_id | bigint unsigned, FK → categories.id | onDelete restrict |
| nama | varchar(120) | |
| slug | varchar(150) | unique |
| deskripsi | text | |
| harga | decimal(12,2) | > 0 |
| stok | integer | default 0 |
| foto | varchar(255) | nullable |
| kondisi | enum('sehat','perlu_perhatian') | default 'sehat' |
| is_featured | boolean | default false |
| timestamps | | |

### 9.4 Tabel `orders`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned, PK | |
| kode_pesanan | varchar(20) | unique, auto |
| plant_id | bigint unsigned, FK → plants.id | onDelete restrict |
| nama_pemesan | varchar(100) | |
| no_hp | varchar(20) | |
| alamat | text | |
| jumlah | integer | 1..stok saat dibuat |
| total_harga | decimal(12,2) | dihitung server-side |
| status | enum('baru','diproses','dikirim','selesai','batal') | default 'baru' |
| catatan | text | nullable |
| timestamps | | |

### 9.5 Tabel `order_items` — OPSIONAL (varian multi-item)
Ditambahkan hanya bila ingin relasi many-to-many di ERD. Bila dipakai: hapus `plant_id`, `jumlah`, `total_harga` dari `orders`; pindahkan ke `order_items`.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned, PK | |
| order_id | bigint unsigned, FK → orders.id | onDelete cascade |
| plant_id | bigint unsigned, FK → plants.id | onDelete restrict |
| jumlah | integer | |
| subtotal | decimal(12,2) | harga × jumlah |
| timestamps | | |

### 9.6 Relasi & kardinalitas

**Varian dasar (4 tabel):**
- `categories` 1 ──── N `plants`
- `plants` 1 ──── N `orders`

**Varian lanjutan (5 tabel):**
- `categories` 1 ──── N `plants`
- `orders` 1 ──── N `order_items`
- `plants` 1 ──── N `order_items`
- `plants` N ──── N `orders` (via `order_items`)

### 9.7 Urutan migration
`users` → `categories` → `plants` → `orders` → (`order_items`)

---

## 10. Peta Halaman (6 Halaman Blade)

### 10.1 Struktur template

```
resources/views/
├── layouts/app.blade.php          # layout tunggal semua halaman
├── partials/
│   ├── navbar.blade.php           # menu adaptif @auth
│   ├── footer.blade.php
│   ├── sidebar.blade.php          # khusus area admin
│   ├── alert.blade.php            # notifikasi sukses/gagal
│   └── plant-card.blade.php       # kartu tanaman reusable
├── home.blade.php                 # H1
├── auth/login.blade.php           # H2
├── plants/
│   ├── index.blade.php            # katalog publik
│   └── show.blade.php             # H6
└── admin/
    ├── dashboard.blade.php        # H3
    └── plants/
        ├── index.blade.php        # H4
        └── form.blade.php         # H5 (create & edit)
```

**`layouts/app.blade.php` wajib memuat:**
- `<head>`: Tailwind CDN, Google Font Plus Jakarta Sans, `<title>@yield('title')</title>`, `@stack('styles')`
- `@include('partials.navbar')`
- `<main>@yield('content')</main>`
- `@include('partials.footer')`
- `@stack('scripts')`

Setiap halaman wajib `@extends('layouts.app')` + `@section('content')`.

### 10.2 Detail per halaman

**H1 — `home.blade.php` (Landing)**
Hero: headline "Tanaman sehat, rumah lebih hidup", subheadline, CTA "Lihat Katalog", gambar. Section "Kategori Pilihan" (3 kartu). Section "Tanaman Unggulan" (grid 6 kartu dari `Plant::featured()`). Section "Tips Perawatan" (3 kartu statis). Footer.

**H2 — `auth/login.blade.php`**
Kartu terpusat, logo/nama produk, input email, input password, checkbox "ingat saya" (opsional), tombol Masuk, tampilkan `@error` per field. Tanpa link registrasi.

**H3 — `admin/dashboard.blade.php`**
4 stat card (ikon + angka + label). Grafik garis Chart.js total penjualan 7 hari. Tabel 5 pesanan terbaru (kode, tanaman, pemesan, total, status badge). Panel "Stok Menipis" (nama + sisa stok + badge merah). Sidebar admin aktif.

**H4 — `admin/plants/index.blade.php`**
Header + tombol "+ Tambah Tanaman". Baris filter: input search, select kategori, tombol Reset. Tabel: foto thumbnail, nama, kategori, harga, stok, kondisi, aksi (Edit/Hapus). Paginasi di bawah. Empty state bila kosong.

**H5 — `admin/plants/form.blade.php`**
Satu file untuk create & edit, judul dinamis. Field: nama, kategori (select), harga (number), stok (number), kondisi (select), deskripsi (textarea), foto (file, preview opsional). Tombol Simpan + Batal. Error validasi tampil di bawah field terkait.

**H6 — `plants/show.blade.php` (Detail publik)**
Breadcrumb. Grid 2 kolom: kiri foto besar + badge stok, kanan nama, harga, kondisi, deskripsi, dan form pesan (nama, no_hp, alamat, jumlah dengan `max=stok`, catatan, tombol "Pesan Sekarang"). Pesan sukses menampilkan kode pesanan.

---

## 11. Arsitektur Teknis

### 11.1 Stack
- PHP 8.2+, Laravel 11/12
- Blade + Tailwind CSS (CDN, tanpa build step)
- Chart.js (CDN)
- Database: SQLite (default, paling cepat) atau MySQL
- Storage: `storage/app/public` + `php artisan storage:link`

### 11.2 Routing

**Publik**
```
GET  /                        → HomeController@index
GET  /katalog                 → PlantController@index
GET  /tanaman/{plant:slug}    → PlantController@show
POST /pesan                   → OrderController@store
```

**Auth**
```
GET  /login                   → AuthController@showLogin      (guest)
POST /login                   → AuthController@login
POST /logout                  → AuthController@logout         (auth)
```

**Admin (middleware: auth)**
```
GET    /admin/dashboard            → DashboardController@index
GET    /admin/plants               → Admin\PlantController@index
GET    /admin/plants/create        → Admin\PlantController@create
POST   /admin/plants               → Admin\PlantController@store
GET    /admin/plants/{plant}/edit  → Admin\PlantController@edit
PUT    /admin/plants/{plant}       → Admin\PlantController@update
DELETE /admin/plants/{plant}       → Admin\PlantController@destroy
GET    /admin/orders               → Admin\OrderController@index        (bonus)
PATCH  /admin/orders/{order}/status→ Admin\OrderController@updateStatus (bonus)
```

### 11.3 Model & relasi

- `Category` — `hasMany(Plant::class)`
- `Plant` — `belongsTo(Category::class)`, `hasMany(Order::class)`
- `Order` — `belongsTo(Plant::class)`
- `User` — helper `isAdmin(): bool`

**Tambahan yang menaikkan kualitas:**
- Accessor `Plant::getStokStatusAttribute()` → "Aman" / "Menipis" / "Habis"
- Accessor `Plant::getHargaRupiahAttribute()` → `Rp 45.000`
- Scope `Plant::scopeFeatured()`, `Plant::scopeStokMenipis()`
- Scope `Order::scopeStatus($status)`
- `Order::booted()` untuk generate `kode_pesanan` (`TMK-YYYYMMDD-XXX`)
- Route model binding slug untuk `plants`

### 11.4 Standar UI

- **Warna primer:** hijau tua `#2F6B4F`
- **Warna aksen:** hijau muda `#A3C9A8`
- **Latar:** `#F7F9F5`
- **Teks:** `#1F2937`
- **Bahaya/stok kritis:** `#DC2626`
- **Font:** Plus Jakarta Sans (400/500/600/700)
- **Radius:** `rounded-xl` konsisten untuk kartu, `rounded-lg` untuk tombol
- **Komponen wajib seragam:** kartu, badge status, tombol primer/sekunder, tabel, input dengan label

### 11.5 Menu aktif
Gunakan `request()->routeIs('admin.plants.*')` untuk memberi class aktif pada item navigasi — bukan membandingkan URL mentah.

---

## 12. Rencana Data Dummy (Seeder)

**Admin:** 1 akun — `admin@tanamku.test` / `password`

**Kategori (4):** Tanaman Hias Daun, Tanaman Hias Bunga, Kaktus & Sukulen, Tanaman Gantung

**Tanaman (12)** — campur agar dashboard tidak kosong:
- Monstera Deliciosa — 185.000 — stok 12
- Snake Plant (Lidah Mertua) — 75.000 — stok 25
- Calathea Orbifolia — 165.000 — stok **4** (memicu badge Menipis)
- ZZ Plant — 120.000 — stok 18
- Peace Lily — 95.000 — stok 9
- Philodendron Brasil — 85.000 — stok 15
- Anthurium Merah — 210.000 — stok **3** (Menipis)
- Kaktus Mini — 35.000 — stok 40
- Sukulen Echeveria — 45.000 — stok 30
- Pothos Marble Queen — 65.000 — stok **0** (Habis, untuk uji tombol nonaktif)
- Sirih Gading — 55.000 — stok 22
- Boston Fern — 70.000 — stok **2** (Menipis)

**Pesanan (8)** — status bervariasi (`baru` ×3, `diproses` ×2, `dikirim` ×1, `selesai` ×1, `batal` ×1), tanggal tersebar dalam 7 hari terakhir agar grafik dashboard terlihat bergerak.

---

## 13. Definition of Done

Proyek dinyatakan selesai bila:

- [ ] `php artisan migrate:fresh --seed` berjalan tanpa error
- [ ] Landing page tampil dengan data dari database
- [ ] Katalog bisa search + filter + paginasi
- [ ] Detail tanaman menampilkan form pesan yang berfungsi
- [ ] Pesanan tersimpan dengan kode unik dan total yang benar
- [ ] Login admin berfungsi; `/admin/*` tidak bisa diakses tanpa login
- [ ] Dashboard menampilkan 4 metrik + grafik + tabel pesanan terbaru
- [ ] CRUD tanaman lengkap termasuk upload foto
- [ ] Semua halaman `@extends('layouts.app')` dan konsisten secara visual
- [ ] Navigasi memakai `route()` dan menu aktif berfungsi
- [ ] Tidak ada error di `storage/logs/laravel.log` saat menelusuri semua halaman
- [ ] Tampil rapi di lebar 375 px dan 1440 px

---

## 14. Pemetaan ke Soal UTS

| Soal | Bobot | Deliverable dari PRD ini |
|---|---|---|
| **P1** — Tema, tujuan, fungsi | 2 | Bagian 1–3 PRD ini, tinggal diringkas jadi paragraf |
| **P2** — Use case diagram | 2 | Bagian 4 + 6: 2 aktor, 13 use case, relasi include/extend/generalisasi |
| **P3** — Database diagram | 6 | Bagian 9: 4 tabel (atau 5 dengan `order_items`), tipe data, FK, kardinalitas |
| **P4** — 6 halaman Blade | 10 | Bagian 10–11: layout tunggal, 6 halaman, partial, navigasi, standar UI |
| **P5** — Implementasi DB & model | 10 | Bagian 9, 11.3, 12: migration, model, relasi, accessor, scope, seeder |

---

## 15. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Terlalu banyak fitur bonus | Halaman inti setengah jadi | Kerjakan FR-01..FR-07 dulu; FR-08/09 hanya setelah DoD inti terpenuhi |
| Upload foto gagal (storage link) | CRUD terlihat rusak | Jalankan `storage:link` lebih awal; sediakan fallback gambar placeholder |
| Grafik kosong karena data sedikit | Dashboard tidak menarik | Seeder menyebar tanggal pesanan 7 hari terakhir |
| Relasi N:M menambah kompleksitas | Controller membengkak | Pakai varian 4 tabel bila waktu < 2 hari tersisa |
| Konflik gaya antar halaman | Nilai konsistensi turun | Selesaikan `layouts/app.blade.php` + partial lebih dulu, baru isi konten |
| PHP/Composer belum terpasang | Tidak bisa mulai | Pasang Laragon (bundel PHP 8 + MySQL + Composer) sebelum coding |

---

## 16. Rencana Kerja

| Tahap | Aktivitas | Estimasi |
|---|---|---|
| 1 | Setup project, install Laravel, konfigurasi DB, layout + partial | 1,5 jam |
| 2 | Migration, model, relasi, seeder | 1,5 jam |
| 3 | Landing page + katalog + detail + form pesan | 2 jam |
| 4 | Login + middleware auth | 0,5 jam |
| 5 | Dashboard admin + Chart.js | 2 jam |
| 6 | CRUD tanaman + validasi + upload | 2,5 jam |
| 7 | Rapikan styling, uji semua halaman, perbaiki bug | 1,5 jam |
| **Total** | | **±11,5 jam** |

Urutan pengerjaan sengaja menaruh layout di awal — supaya keenam halaman otomatis konsisten dan tidak ada rework styling.

---

## 17. Keputusan Terbuka

1. **Varian skema:** 4 tabel (cepat) atau 5 tabel dengan `order_items` (ERD lebih kuat)? *Rekomendasi: 4 tabel bila sisa waktu < 2 hari, 5 tabel bila lebih.*
2. **Database:** SQLite atau MySQL? *Rekomendasi: SQLite untuk kecepatan; MySQL bila dosen mensyaratkan.*
3. **Halaman bonus:** FR-08 (kelola pesanan) atau FR-09 (kelola kategori) — pilih satu saja bila waktu terbatas. *Rekomendasi: FR-08, karena status pesanan lebih sering ditanya saat presentasi.*
4. **Autentikasi:** manual atau Laravel Breeze? *Rekomendasi: manual sederhana — lebih sedikit file, lebih mudah dijelaskan.*

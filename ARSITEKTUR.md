# Arsitektur TanamKu (Marketplace) — v2

Dokumen ini menggantikan PRD versi 1 (single-seller). Dipakai untuk menjawab
**P2 (use case)**, **P3 (database diagram)**, dan **P4 (halaman Blade)**.

---

## 1. Perubahan inti dari versi sebelumnya

| Sebelum | Sesudah |
|---|---|
| Satu penjual (platform sebagai pemilik produk) | Banyak penjual, tiap produk milik satu toko |
| Role: admin / staff / customer | Role: **admin / seller / buyer** |
| Checkout langsung buat order | **Keranjang → checkout per toko → pesan WhatsApp** |
| Tidak ada profil toko | Tabel `stores` + persetujuan admin |
| Produk tanpa pemilik | `plants.store_id` wajib |

---

## 2. P2 — Use Case Diagram

### Aktor
1. **Pembeli (buyer)**
2. **Penjual (seller)**
3. **Admin**

### Use case Pembeli
- Melihat beranda & katalog
- Mencari / memfilter produk (kategori, harga)
- Melihat detail produk + panduan perawatan
- Melihat profil toko
- Menambah produk ke keranjang
- Mengubah jumlah / menghapus item keranjang
- Checkout per toko
- Membuka pesan WhatsApp ke penjual
- Melihat riwayat pesanan

### Use case Penjual
- Mendaftar akun penjual
- Melengkapi profil toko
- Mengunggah logo toko
- Menambah / mengubah / menghapus produk
- Mengunggah foto produk
- Mengisi panduan perawatan
- Melihat pesanan masuk
- Mengubah status pesanan (diproses / selesai / batal)

### Use case Admin
- Melihat dashboard platform
- Menyetujui / menonaktifkan toko
- Mengelola akun (ubah peran, aktif/nonaktif)
- Memoderasi produk (sembunyikan / hapus)
- Mengelola kategori
- Memantau seluruh pesanan

### Relasi yang wajib tampil di gambar
- `Checkout per toko` **<<include>>** `Membuat pesan WhatsApp`
- `Membuka pesan WhatsApp` **<<extend>>** `Checkout per toko`
- `Menambah ke keranjang` **<<extend>>** `Melihat detail produk`
- `Menyetujui toko` **<<include>>** `Memeriksa profil toko`
- Generalisasi: **Penjual** dan **Pembeli** mewarisi **Pengguna**; Admin berdiri sendiri
- `Mengunggah foto produk` **<<extend>>** `Menambah produk`

---

## 3. P3 — Database Diagram

### 3.1 Daftar tabel

**`users`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| name | varchar(100) | |
| email | varchar(150) | unique |
| password | varchar(255) | hashed |
| role | enum('admin','seller','buyer') | default buyer |
| no_hp | varchar(20) | nullable |
| alamat | text | nullable |
| is_active | boolean | default true |
| timestamps | | |

**`stores`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users | pemilik toko (1 seller = 1 toko) |
| nama_toko | varchar(100) | |
| slug | varchar(120) | unique |
| no_wa | varchar(20) | nomor tujuan checkout |
| alamat | text | |
| kota | varchar(80) | |
| deskripsi | text | |
| logo | varchar(255) | nullable |
| metode_serah | enum('ambil','kirim','keduanya') | |
| status | enum('menunggu','aktif','nonaktif') | default menunggu |
| timestamps | | |

**`categories`**
| Kolom | Tipe |
|---|---|
| id, nama varchar(80), slug varchar(100) unique, deskripsi text nullable, timestamps | |

**`plants`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| store_id | bigint FK → stores | pemilik produk |
| category_id | bigint FK → categories | |
| nama | varchar(120) | |
| slug | varchar(150) | unique |
| deskripsi | text | |
| perawatan | text | nullable, "Label: penjelasan" per baris |
| harga | decimal(12,2) | |
| stok | integer | |
| foto | varchar(255) | nullable |
| kondisi | enum('sehat','perlu_perhatian') | |
| is_featured | boolean | |
| is_active | boolean | moderasi admin |
| timestamps | | |

**`cart_items`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users | |
| plant_id | bigint FK → plants | |
| jumlah | integer | |
| timestamps | | |
| | | **unique(user_id, plant_id)** |

**`orders`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| kode_pesanan | varchar(20) | unique, otomatis |
| buyer_id | bigint FK → users | nullable (nullOnDelete) |
| store_id | bigint FK → stores | pesanan per toko |
| nama_penerima | varchar(100) | |
| no_hp | varchar(20) | |
| alamat | text | |
| metode_serah | enum('ambil','kirim') | |
| catatan | text | nullable |
| total_harga | decimal(12,2) | dihitung server |
| status | enum('baru','diproses','selesai','batal') | |
| timestamps | | |

**`order_items`**
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| order_id | bigint FK → orders | cascade |
| plant_id | bigint FK → plants | nullable (nullOnDelete) |
| nama_produk | varchar(120) | **snapshot** |
| harga_satuan | decimal(12,2) | **snapshot** |
| jumlah | integer | |
| subtotal | decimal(12,2) | |
| timestamps | | |

### 3.2 Relasi & kardinalitas

```
users 1 ──────── 1 stores            (seller punya satu toko)
users 1 ──────── N orders            (buyer membuat banyak pesanan)
users 1 ──────── N cart_items
stores 1 ─────── N plants
stores 1 ─────── N orders
categories 1 ─── N plants
plants 1 ─────── N cart_items
plants 1 ─────── N order_items
orders 1 ─────── N order_items
plants N ─────── N orders            (via order_items)
users  N ─────── N plants            (via cart_items)
```

### 3.3 Kenapa `order_items` menyimpan nama & harga (snapshot)
Supaya riwayat pesanan tidak berubah saat penjual menaikkan harga atau menghapus
produk. `plant_id` boleh null, tetapi `nama_produk` dan `harga_satuan` tetap ada.
Ini pertanyaan yang paling mungkin muncul saat presentasi.

---

## 4. P4 — Peta Halaman Blade

Semua memakai satu layout `resources/views/layouts/app.blade.php`.

### Publik
| URL | File | Isi |
|---|---|---|
| `/` | `home.blade.php` | Hero, kategori, produk unggulan, daftar toko, cara pesan |
| `/katalog` | `plants/index.blade.php` | Grid produk + cari + filter kategori + urut harga |
| `/tanaman/{slug}` | `plants/show.blade.php` | Foto, harga, toko, tombol keranjang, **cara merawat**, produk lain |
| `/toko/{slug}` | `stores/show.blade.php` | Profil toko + tombol chat + produk toko |
| `/login` | `auth/login.blade.php` | Login satu pintu |
| `/register` | `auth/register.blade.php` | Pilih peran Pembeli / Penjual |

### Pembeli
| URL | File |
|---|---|
| `/keranjang` | `carts/index.blade.php` (dikelompokkan per toko) |
| `/checkout/{toko}` | `checkout/create.blade.php` |
| `/pesanan` | `orders/mine.blade.php` |
| `/pesanan/{id}` | `orders/show.blade.php` (tombol + pratinjau WhatsApp) |

### Penjual
| URL | File |
|---|---|
| `/seller/dashboard` | `seller/dashboard.blade.php` |
| `/seller/toko` | `seller/store-form.blade.php` |
| `/seller/produk` | `seller/plants/index.blade.php` |
| `/seller/produk/create` | `seller/plants/form.blade.php` |
| `/seller/produk/{slug}/edit` | `seller/plants/form.blade.php` (file sama) |
| `/seller/pesanan` | `seller/orders/index.blade.php` |

### Admin
| URL | File |
|---|---|
| `/admin/dashboard` | `admin/dashboard.blade.php` |
| `/admin/toko` | `admin/stores/index.blade.php` |
| `/admin/produk` | `admin/plants/index.blade.php` |
| `/admin/pengguna` | `admin/users/index.blade.php` |
| `/admin/kategori` | `admin/categories/index.blade.php` |
| `/admin/pesanan` | `admin/orders/index.blade.php` |

**Total 21 halaman** (jauh di atas syarat minimum 6).

---

## 5. Alur checkout WhatsApp

1. Pembeli menekan **Tambah ke Keranjang** di halaman detail.
2. Keranjang dikelompokkan **per toko**.
3. Tombol **Checkout via WhatsApp** per toko → form checkout (nama, HP, alamat, metode, catatan).
4. Sistem: buat `orders` + `order_items`, **kurangi stok**, hapus `cart_items` toko itu.
5. Pembeli diarahkan ke `/pesanan/{id}` yang menampilkan tombol **Buka WhatsApp**.
6. Tautan: `https://wa.me/62xxx?text=<pesan terisi otomatis>`.

**Aturan tombol WhatsApp:** hanya tampil bila status pesanan **bukan `batal`**.
Pesanan yang dibatalkan menampilkan keterangan "Pesanan ini dibatalkan" dan tombol
kembali ke katalog — tanpa tautan WhatsApp sama sekali, karena tidak ada lagi yang
perlu dikirim ke penjual.

Isi pesan otomatis:
```
Halo {nama toko}, saya ingin memesan dari TanamKu.

Kode pesanan: TMK-20260815-A1B2
--------------------------------
1. Monstera Deliciosa
    2 x Rp 185.000 = Rp 370.000
--------------------------------
TOTAL: Rp 370.000

Nama     : ...
No HP    : ...
Alamat   : ...
Metode   : Dikirim
Catatan  : ...

Mohon konfirmasi ketersediaan stok dan total akhirnya. Terima kasih.
```

Normalisasi nomor: `0812...` → `62812...` (lihat `Store::wa_number`).

---

## 6. Keputusan desain yang perlu dijelaskan saat presentasi

1. **Checkout dipisah per toko** — satu pesan WhatsApp hanya untuk satu penjual,
   karena tiap toko punya nomor sendiri.
2. **Stok berkurang saat pesanan dibuat**, bukan saat dikonfirmasi penjual, agar
   tidak terjadi pesanan ganda. Bila penjual membatalkan, stok dikembalikan otomatis.
3. **Toko harus disetujui admin** — mencegah toko palsu/spam masuk katalog.
   Produk toko berstatus `menunggu` tetap bisa diunggah, tapi tidak tampil.
4. **TanamKu tidak memproses pembayaran** — tidak ada tabel transaksi/payment.
5. **Snapshot harga di `order_items`** — riwayat pesanan kebal perubahan harga.
6. **Hapus produk yang sudah pernah dipesan** hanya menonaktifkan (`is_active = false`)
   agar riwayat tidak rusak.

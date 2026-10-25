# Handoff Brief — TanamKu

Dokumen ini dibuat untuk diberikan ke asisten AI lain sebagai konteks awal. Salin seluruh isi di bawah garis ke dalam chat AI tersebut.

---

## KONTEKS

Saya mahasiswa semester 5 Teknik Informatika, sedang mengerjakan Ujian Tengah Semester mata kuliah **Pemrograman Web Enterprise**. Saya sudah punya PRD lengkap untuk aplikasi bernama **TanamKu** — katalog dan pemesanan tanaman hias online berbasis **Laravel + Blade**.

Baca PRD yang saya lampirkan/sertakan. Tugas kamu adalah membantu saya **mengimplementasikannya sesuai PRD**, bukan mendesain ulang produknya.

## APA YANG SUDAH DIPUTUSKAN (jangan diubah tanpa bertanya)

- **Framework:** Laravel 11/12, template engine **Blade** (bukan Livewire, bukan Inertia, bukan React/Vue)
- **Styling:** Tailwind CSS via CDN (tanpa build step, tanpa npm)
- **Chart:** Chart.js via CDN
- **Database:** SQLite (default) atau MySQL — tanya saya mana yang dipakai
- **Jumlah halaman:** tepat **6 halaman Blade** inti, sesuai daftar di PRD bagian 10
- **Skema database:** 4 tabel inti — `users`, `categories`, `plants`, `orders`. Tabel `order_items` hanya bila saya bilang mau varian 5 tabel
- **Autentikasi:** manual sederhana (bukan Breeze/Jetstream/Fortify), tanpa registrasi publik
- **Bahasa antarmuka:** Indonesia
- **Nama produk:** TanamKu

## BATASAN KERAS

1. **Jangan menambah fitur** di luar PRD bagian 5.1. Tidak ada payment gateway, keranjang multi-item, login pembeli, wishlist, atau notifikasi email.
2. **Jangan pakai library tambahan** selain yang disebut di atas, kecuali saya setujui.
3. **Jangan membuat satu layout per halaman.** Harus ada satu `layouts/app.blade.php` yang di-`@extends` semua halaman — ini kriteria penilaian.
4. **Jangan hardcode URL.** Semua navigasi lewat `route()`.
5. **Jangan hardcode data.** Semua konten dinamis diambil dari database lewat Eloquent.
6. **Jangan mengubah nama tabel, kolom, atau route** yang sudah ditetapkan PRD — konsistensi ini dinilai.

## KONVENSI KODE

- Penamaan route: `plants.index`, `plants.show`, `admin.plants.index`, `admin.plants.store`, dst.
- Controller admin di namespace `App\Http\Controllers\Admin`
- Validasi memakai FormRequest bila field lebih dari 5, selain itu `$request->validate()`
- Mass assignment selalu dibatasi `$fillable`
- Semua form wajib `@csrf`
- Relasi selalu eager-load di listing: `with('category')`
- Komentar kode dalam bahasa Indonesia, singkat, hanya pada logika yang tidak jelas

## URUTAN PENGERJAAN YANG DIHARAPKAN

1. Setup project + `layouts/app.blade.php` + semua partial
2. Migration, model, relasi, seeder
3. Landing page, katalog, detail, form pesan
4. Login + middleware auth
5. Dashboard admin + Chart.js
6. CRUD tanaman + upload foto
7. Perapian styling + uji semua halaman

Kerjakan **berurutan**. Jangan lompat ke dashboard sebelum layout dan seeder selesai.

## CARA SAYA INGIN KAMU BEKERJA

- Beri saya **kode lengkap per file** (path file disebutkan di atas setiap blok kode), bukan potongan "tambahkan kode ini di sini".
- Jika satu langkah butuh lebih dari 5 file, pecah jadi beberapa balasan dan beri tahu urutan eksekusinya.
- Setiap kali selesai satu tahap, sebutkan **cara memverifikasi** bahwa tahap itu berhasil (perintah artisan atau URL yang harus saya buka).
- Jika ada instruksi saya yang bertentangan dengan PRD, **tegur saya** dan sebutkan bagian PRD yang dilanggar.
- Jika kamu butuh keputusan (misal SQLite vs MySQL, varian 4 vs 5 tabel), **tanya dulu** — jangan asumsi.

## LINGKUNGAN SAYA

- Windows 11
- PHP & Composer **belum terpasang** — beri saya langkah instalasi (Laragon) sebelum perintah artisan pertama
- Editor: VS Code
- Saya nyaman dengan PHP dasar, tapi belum pernah pakai Laravel secara mendalam — jelaskan hal yang tidak umum

## DEFINISI SELESAI

Acuan tunggal: **PRD bagian 13 (Definition of Done)**. Jangan menyatakan selesai sebelum semua checkbox di bagian itu terpenuhi.

---

## PROMPT PEMBUKA (salin ini)

> Saya lampirkan PRD aplikasi TanamKu untuk tugas UTS Pemrograman Web Enterprise. Baca PRD itu sepenuhnya, lalu mulai dari Tahap 1: setup project Laravel dan pembuatan `layouts/app.blade.php` beserta semua partial. Jangan mengerjakan tahap lain sebelum saya konfirmasi tahap 1 berhasil. PHP dan Composer belum terpasang di laptop saya, jadi mulai dengan panduan instalasi Laragon.

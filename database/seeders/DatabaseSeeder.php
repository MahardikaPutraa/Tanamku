<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Plant;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Data awal marketplace TanamKu:
     * 1 admin, 3 penjual (2 toko aktif + 1 menunggu), 2 pembeli,
     * 4 kategori, 12 produk, 4 pesanan.
     */
    public function run(): void
    {
        // ---------------------------------------------------------- Pengguna
        $admin = User::firstOrCreate(
            ['email' => 'admin@tanamku.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'no_hp' => '081200000001',
            ]
        );

        $seller1 = User::create([
            'name' => 'Pak Hendra',
            'email' => 'seller@tanamku.test',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'no_hp' => '081211110001',
            'alamat' => 'Jl. Kebon Jeruk Raya No. 12, Jakarta Barat',
        ]);

        $seller2 = User::create([
            'name' => 'Bu Ratna',
            'email' => 'seller2@tanamku.test',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'no_hp' => '081222220002',
            'alamat' => 'Jl. Dago Atas No. 45, Bandung',
        ]);

        $seller3 = User::create([
            'name' => 'Mas Dimas',
            'email' => 'seller3@tanamku.test',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'no_hp' => '081233330003',
            'alamat' => 'Jl. Kaliurang KM 5 No. 8, Sleman, Yogyakarta',
        ]);

        $buyer1 = User::create([
            'name' => 'Rina Pelanggan',
            'email' => 'buyer@tanamku.test',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'no_hp' => '081298765432',
            'alamat' => 'Jl. Anggrek No. 21, RT 04/RW 07, Kebon Jeruk, Jakarta Barat',
        ]);

        $buyer2 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'buyer2@tanamku.test',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'no_hp' => '081277778888',
            'alamat' => 'Jl. Melati Indah No. 4, Tangerang Selatan',
        ]);

        // ------------------------------------------------------------- Toko
        $toko1 = Store::create([
            'user_id' => $seller1->id,
            'nama_toko' => 'Kebun Hijau Jaya',
            'slug' => 'kebun-hijau-jaya',
            'no_wa' => '081211110001',
            'alamat' => 'Jl. Kebon Jeruk Raya No. 12, Jakarta Barat',
            'kota' => 'Jakarta Barat',
            'deskripsi' => 'Penjual tanaman hias indoor sejak 2018. Semua tanaman dirawat sendiri di kebun belakang rumah, dikirim dengan packing kayu agar aman sampai tujuan.',
            'metode_serah' => 'keduanya',
            'status' => 'aktif',
        ]);

        $toko2 = Store::create([
            'user_id' => $seller2->id,
            'nama_toko' => 'Rumah Daun Bandung',
            'slug' => 'rumah-daun-bandung',
            'no_wa' => '081222220002',
            'alamat' => 'Jl. Dago Atas No. 45, Bandung',
            'kota' => 'Bandung',
            'deskripsi' => 'Spesialis tanaman berdaun lebar dan bunga potong. Melayani pengiriman se-Bandung dan sekitarnya, bisa juga ambil langsung di toko.',
            'metode_serah' => 'keduanya',
            'status' => 'aktif',
        ]);

        $toko3 = Store::create([
            'user_id' => $seller3->id,
            'nama_toko' => 'Kaktus Corner',
            'slug' => 'kaktus-corner',
            'no_wa' => '081233330003',
            'alamat' => 'Jl. Kaliurang KM 5 No. 8, Sleman, Yogyakarta',
            'kota' => 'Sleman',
            'deskripsi' => 'Koleksi kaktus dan sukulen untuk pemula. Cocok untuk meja kerja dan dekorasi minimalis.',
            'metode_serah' => 'kirim',
            'status' => 'menunggu',
        ]);

        // --------------------------------------------------------- Kategori
        $catDaun = Category::create([
            'nama' => 'Tanaman Hias Daun',
            'slug' => 'tanaman-hias-daun',
            'deskripsi' => 'Tanaman berdaun rimbun untuk interior.',
        ]);

        $catBunga = Category::create([
            'nama' => 'Tanaman Hias Bunga',
            'slug' => 'tanaman-hias-bunga',
            'deskripsi' => 'Tanaman berbunga yang memberi warna pada ruangan.',
        ]);

        $catKaktus = Category::create([
            'nama' => 'Kaktus & Sukulen',
            'slug' => 'kaktus-sukulen',
            'deskripsi' => 'Tahan banting dan mudah dirawat.',
        ]);

        $catGantung = Category::create([
            'nama' => 'Tanaman Gantung',
            'slug' => 'tanaman-gantung',
            'deskripsi' => 'Tanaman rambat dan menjuntai untuk rak tinggi.',
        ]);

        // ----------------------------------------------------------- Produk
        $dataProduk = [
            [
                'store_id' => $toko1->id, 'category_id' => $catDaun->id,
                'nama' => 'Monstera Deliciosa', 'slug' => 'monstera-deliciosa',
                'deskripsi' => 'Tanaman berdaun lebar dengan pola sobekan khas. Paling dicari untuk dekorasi ruang tamu bergaya minimalis maupun bohemian.',
                'harga' => 185000, 'stok' => 12, 'is_featured' => true,
                'perawatan' => "Cahaya: Terang namun tidak kena matahari langsung.\nAir: Siram 1 kali seminggu, setelah 2-3 cm media terasa kering.\nMedia: Campuran tanah, sekam, dan perlite.\nSuhu: 18-30 °C.\nPupuk: NPK seimbang sebulan sekali.\nCatatan: Beri ajir atau moss pole agar daun makin besar.",
            ],
            [
                'store_id' => $toko1->id, 'category_id' => $catDaun->id,
                'nama' => 'Snake Plant (Lidah Mertua)', 'slug' => 'snake-plant',
                'deskripsi' => 'Pembersih udara alami yang tahan di ruangan minim cahaya. Sangat cocok untuk pemula yang sibuk.',
                'harga' => 75000, 'stok' => 25, 'is_featured' => true,
                'perawatan' => "Cahaya: Toleran dari minim cahaya sampai terang tidak langsung.\nAir: 2-3 minggu sekali, media harus kering dulu.\nMedia: Media berpasir dengan drainase baik.\nSuhu: 15-30 °C.\nPupuk: 2 bulan sekali, dosis setengah.\nCatatan: Paling sering mati karena kelebihan air.",
            ],
            [
                'store_id' => $toko1->id, 'category_id' => $catDaun->id,
                'nama' => 'Calathea Orbifolia', 'slug' => 'calathea-orbifolia',
                'deskripsi' => 'Daun bundar besar dengan corak garis silver yang elegan. Perlu kelembapan cukup agar ujung daun tidak mengering.',
                'harga' => 165000, 'stok' => 4, 'kondisi' => 'perlu_perhatian', 'is_featured' => true,
                'perawatan' => "Cahaya: Sedang, tidak langsung.\nAir: Jaga media lembap, 2-3 kali seminggu.\nKelembapan: Di atas 60%.\nSuhu: 18-27 °C, jauh dari AC.\nPupuk: Pupuk cair encer tiap 2 minggu.\nCatatan: Gunakan air bebas klorin; ujung kering berarti kurang lembap.",
            ],
            [
                'store_id' => $toko1->id, 'category_id' => $catDaun->id,
                'nama' => 'Philodendron Brasil', 'slug' => 'philodendron-brasil',
                'deskripsi' => 'Daun berbentuk hati dengan goresan hijau limau cerah. Cepat tumbuh dan menawan bila digantung.',
                'harga' => 85000, 'stok' => 15, 'is_featured' => false,
                'perawatan' => "Cahaya: Terang tidak langsung agar variegasi tidak pudar.\nAir: 1 kali seminggu.\nMedia: Gembur dan porous.\nSuhu: 18-29 °C.\nPupuk: Sebulan sekali.\nCatatan: Mudah diperbanyak lewat stek batang.",
            ],
            [
                'store_id' => $toko1->id, 'category_id' => $catGantung->id,
                'nama' => 'Pothos Marble Queen', 'slug' => 'pothos-marble-queen',
                'deskripsi' => 'Pothos dengan variegasi putih krem yang kontras. Cocok untuk pot gantung di dekat jendela.',
                'harga' => 65000, 'stok' => 18, 'is_featured' => false,
                'perawatan' => "Cahaya: Terang tidak langsung, butuh lebih banyak cahaya dari pothos biasa.\nAir: 1 kali seminggu.\nMedia: Tanah dan perlite.\nSuhu: 18-29 °C.\nPupuk: Sebulan sekali, dosis ringan.\nCatatan: Daun putih lebih tipis dan mudah terbakar matahari langsung.",
            ],
            [
                'store_id' => $toko2->id, 'category_id' => $catDaun->id,
                'nama' => 'ZZ Plant', 'slug' => 'zz-plant',
                'deskripsi' => 'Daun tebal mengkilap yang tangguh. Umbinya menyimpan air sehingga tahan berminggu-minggu tanpa disiram.',
                'harga' => 120000, 'stok' => 18, 'is_featured' => true,
                'perawatan' => "Cahaya: Rendah sampai sedang.\nAir: 2-3 minggu sekali.\nMedia: Porous, tanah dan perlite.\nSuhu: 16-28 °C.\nPupuk: 2-3 bulan sekali.\nCatatan: Daun menguning biasanya karena terlalu sering disiram.",
            ],
            [
                'store_id' => $toko2->id, 'category_id' => $catBunga->id,
                'nama' => 'Peace Lily', 'slug' => 'peace-lily',
                'deskripsi' => 'Bunga putih anggun yang ampuh menyaring udara. Memberi kesegaran di area kerja atau kamar tidur.',
                'harga' => 95000, 'stok' => 9, 'is_featured' => true,
                'perawatan' => "Cahaya: Sedang, terang tidak langsung.\nAir: Siram saat daun mulai menurun.\nMedia: Tanah gembur dengan drainase baik.\nSuhu: 18-27 °C.\nPupuk: Tiap 6 minggu.\nCatatan: Sensitif klorin; ujung daun menghitam bila air kurang baik.",
            ],
            [
                'store_id' => $toko2->id, 'category_id' => $catBunga->id,
                'nama' => 'Anthurium Merah', 'slug' => 'anthurium-merah',
                'deskripsi' => 'Bunga merah menyala mengkilap yang mekar tahan lama. Memberi aksen mewah pada interior.',
                'harga' => 210000, 'stok' => 3, 'is_featured' => true,
                'perawatan' => "Cahaya: Terang tidak langsung 60-70%.\nAir: 1 kali seminggu.\nMedia: Kasar dan berongga (kulit kayu, perlite).\nSuhu: 20-29 °C, kelembapan 60-80%.\nPupuk: Tinggi fosfor tiap 2 minggu.\nCatatan: Bagian merah adalah daun pelindung (spathe), bukan bunga sebenarnya.",
            ],
            [
                'store_id' => $toko2->id, 'category_id' => $catGantung->id,
                'nama' => 'Sirih Gading', 'slug' => 'sirih-gading',
                'deskripsi' => 'Tanaman paling toleran untuk pemula. Bisa ditanam di tanah maupun di air.',
                'harga' => 55000, 'stok' => 22, 'is_featured' => false,
                'perawatan' => "Cahaya: Rendah sampai sedang.\nAir: 1 kali seminggu.\nMedia: Tanah, sekam, atau air saja.\nSuhu: 18-30 °C.\nPupuk: Sebulan sekali bila di media.\nCatatan: Sangat mudah diperbanyak dengan stek.",
            ],
            [
                'store_id' => $toko2->id, 'category_id' => $catGantung->id,
                'nama' => 'Boston Fern', 'slug' => 'boston-fern',
                'deskripsi' => 'Pakis rimbun dengan helai daun lembut menjuntai. Menghadirkan nuansa hutan tropis di teras rumah.',
                'harga' => 70000, 'stok' => 2, 'is_featured' => false,
                'perawatan' => "Cahaya: Sedang, tidak langsung.\nAir: 2-3 kali seminggu, media selalu lembap.\nKelembapan: Di atas 60%.\nSuhu: 16-24 °C.\nPupuk: Pupuk cair encer sebulan sekali.\nCatatan: Sulit pulih bila media pernah kering total.",
            ],
            [
                'store_id' => $toko3->id, 'category_id' => $catKaktus->id,
                'nama' => 'Kaktus Mini', 'slug' => 'kaktus-mini',
                'deskripsi' => 'Kaktus bulat kecil berduri rapi, pas untuk meja kerja. Tahan panas dan jarang disiram.',
                'harga' => 35000, 'stok' => 40, 'is_featured' => false,
                'perawatan' => "Cahaya: Penuh, 4-6 jam matahari langsung.\nAir: 2-3 minggu sekali.\nMedia: Media kaktus berpasir.\nSuhu: 15-32 °C.\nPupuk: Pupuk kaktus 2 bulan sekali.\nCatatan: Jangan siram saat dorman; kelebihan air menyebabkan busuk pangkal.",
            ],
            [
                'store_id' => $toko3->id, 'category_id' => $catKaktus->id,
                'nama' => 'Sukulen Echeveria', 'slug' => 'sukulen-echeveria',
                'deskripsi' => 'Sukulen berbentuk roset seperti bunga mawar. Warnanya makin cantik bila cukup matahari.',
                'harga' => 45000, 'stok' => 30, 'is_featured' => false,
                'perawatan' => "Cahaya: Penuh; kurang cahaya membuat bentuk memanjang pucat.\nAir: 2 minggu sekali, hindari air di tengah roset.\nMedia: Berpasir dan porous.\nSuhu: 15-28 °C.\nPupuk: Pupuk sukulen encer sebulan sekali.\nCatatan: Siram pagi hari agar air menguap sebelum malam.",
            ],
        ];

        $produk = [];
        foreach ($dataProduk as $item) {
            $item['foto'] = 'plants/' . $item['slug'] . '.jpg';
            $produk[$item['slug']] = Plant::create($item);
        }

        // --------------------------------------------------------- Pesanan
        $pesanan = [
            [
                'buyer' => $buyer1, 'toko' => $toko1, 'status' => 'selesai', 'hari' => 6,
                'items' => [['slug' => 'monstera-deliciosa', 'jumlah' => 1]],
                'catatan' => 'Minta yang daunnya paling rimbun ya mas.',
            ],
            [
                'buyer' => $buyer1, 'toko' => $toko2, 'status' => 'diproses', 'hari' => 3,
                'items' => [
                    ['slug' => 'peace-lily', 'jumlah' => 1],
                    ['slug' => 'sirih-gading', 'jumlah' => 2],
                ],
                'catatan' => 'Packing mohon dilapisi kardus tebal.',
            ],
            [
                'buyer' => $buyer2, 'toko' => $toko1, 'status' => 'baru', 'hari' => 1,
                'items' => [['slug' => 'snake-plant', 'jumlah' => 2]],
                'catatan' => null,
            ],
            [
                'buyer' => $buyer2, 'toko' => $toko2, 'status' => 'baru', 'hari' => 0,
                'items' => [['slug' => 'anthurium-merah', 'jumlah' => 1]],
                'catatan' => 'Untuk kado, tolong diberi pita.',
            ],
        ];

        foreach ($pesanan as $p) {
            $total = 0;
            foreach ($p['items'] as $baris) {
                $total += (float) $produk[$baris['slug']]->harga * $baris['jumlah'];
            }

            $dibuat = now()->subDays($p['hari'])->subHours(rand(1, 5));

            $order = Order::create([
                'buyer_id' => $p['buyer']->id,
                'store_id' => $p['toko']->id,
                'nama_penerima' => $p['buyer']->name,
                'no_hp' => $p['buyer']->no_hp,
                'alamat' => $p['buyer']->alamat,
                'metode_serah' => 'kirim',
                'catatan' => $p['catatan'],
                'total_harga' => $total,
                'status' => $p['status'],
                'created_at' => $dibuat,
                'updated_at' => $dibuat,
            ]);

            foreach ($p['items'] as $baris) {
                $plant = $produk[$baris['slug']];

                $order->items()->create([
                    'plant_id' => $plant->id,
                    'nama_produk' => $plant->nama,
                    'harga_satuan' => $plant->harga,
                    'jumlah' => $baris['jumlah'],
                    'subtotal' => (float) $plant->harga * $baris['jumlah'],
                ]);
            }

            if ($p['status'] === 'selesai') {
                Review::create([
                    'order_id' => $order->id,
                    'plant_id' => $produk['monstera-deliciosa']->id,
                    'user_id' => $p['buyer']->id,
                    'rating' => 5,
                    'komentar' => 'Tanaman segar sekali saat sampai, akar rimbun, daun besar dan mulus. Packing kayu sangat rapi & aman!',
                    'created_at' => $dibuat->copy()->addDays(2),
                ]);

                Review::create([
                    'order_id' => $order->id,
                    'plant_id' => $produk['calathea-orbifolia']->id,
                    'user_id' => $p['buyer']->id,
                    'rating' => 5,
                    'komentar' => 'Motif daunnya sangat cantik, seller sangat ramah dan responsif di WhatsApp. Recommended!',
                    'created_at' => $dibuat->copy()->addDays(2),
                ]);
            }
        }
    }
}

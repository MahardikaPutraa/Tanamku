<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan', 20)->unique();
            $table->foreignId('buyer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('store_id')->constrained('stores')->restrictOnDelete();
            $table->string('nama_penerima', 100);
            $table->string('no_hp', 20);
            $table->text('alamat');
            $table->enum('metode_serah', ['ambil', 'kirim'])->default('kirim');
            $table->text('catatan')->nullable();
            $table->decimal('total_harga', 12, 2);
            $table->enum('status', ['baru', 'diproses', 'selesai', 'batal'])->default('baru');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('plant_id')->nullable()->constrained('plants')->nullOnDelete();
            // Snapshot: harga & nama dibekukan saat checkout, agar riwayat tidak
            // berubah bila seller mengubah harga atau menghapus produk.
            $table->string('nama_produk', 120);
            $table->decimal('harga_satuan', 12, 2);
            $table->integer('jumlah');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};

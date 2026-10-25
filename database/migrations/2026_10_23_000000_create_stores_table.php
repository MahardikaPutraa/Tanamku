<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_toko', 100);
            $table->string('slug', 120)->unique();
            $table->string('no_wa', 20);
            $table->text('alamat');
            $table->string('kota', 80);
            $table->text('deskripsi');
            $table->string('logo', 255)->nullable();
            $table->enum('metode_serah', ['ambil', 'kirim', 'keduanya'])->default('keduanya');
            $table->enum('status', ['menunggu', 'aktif', 'nonaktif'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};

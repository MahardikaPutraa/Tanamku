<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plant extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'category_id',
        'nama',
        'slug',
        'deskripsi',
        'perawatan',
        'harga',
        'stok',
        'foto',
        'kondisi',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Produk yang boleh tampil di katalog publik:
     * produk aktif DAN tokonya sudah disetujui admin.
     */
    public function scopeTampil(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereHas('store', fn (Builder $q) => $q->where('status', 'aktif'));
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeStokMenipis(Builder $query): Builder
    {
        return $query->where('stok', '<', 5);
    }

    public function getStokStatusAttribute(): string
    {
        if ($this->stok <= 0) {
            return 'Habis';
        }

        if ($this->stok <= 4) {
            return 'Menipis';
        }

        return 'Aman';
    }

    public function getHargaRupiahAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->harga, 0, ',', '.');
    }

    /**
     * Pecah kolom "perawatan" menjadi daftar poin siap tampil.
     * Format penyimpanan: satu poin per baris, "Label: penjelasan".
     *
     * @return array<int, array{label: ?string, isi: string}>
     */
    public function getPerawatanPoinAttribute(): array
    {
        if (blank($this->perawatan)) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $this->perawatan))
            ->map(fn ($baris) => trim($baris))
            ->filter()
            ->map(function (string $baris) {
                $bagian = explode(':', $baris, 2);

                return count($bagian) === 2
                    ? ['label' => trim($bagian[0]), 'isi' => trim($bagian[1])]
                    : ['label' => null, 'isi' => $baris];
            })
            ->values()
            ->all();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_pesanan',
        'buyer_id',
        'store_id',
        'nama_penerima',
        'no_hp',
        'alamat',
        'metode_serah',
        'catatan',
        'total_harga',
        'status',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
    ];

    /**
     * Kode pesanan otomatis: TMK-YYYYMMDD-XXXX (dijamin unik).
     */
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (! empty($order->kode_pesanan)) {
                return;
            }

            do {
                $kode = 'TMK-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
            } while (static::where('kode_pesanan', $kode)->exists());

            $order->kode_pesanan = $kode;
        });
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function getTotalHargaRupiahAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_harga, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'baru' => 'Baru',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            default => 'Batal',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'baru' => 'bg-sky-100 text-sky-800 border-sky-200',
            'diproses' => 'bg-amber-100 text-amber-800 border-amber-200',
            'selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            default => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }

    public function getMetodeSerahLabelAttribute(): string
    {
        return $this->metode_serah === 'ambil' ? 'Ambil di toko' : 'Dikirim';
    }
}

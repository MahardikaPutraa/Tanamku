<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_toko',
        'slug',
        'no_wa',
        'alamat',
        'kota',
        'deskripsi',
        'logo',
        'metode_serah',
        'status',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plants(): HasMany
    {
        return $this->hasMany(Plant::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Toko yang sudah disetujui admin.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Nomor WhatsApp dalam format internasional (62...) untuk tautan wa.me.
     * "0812-3456-7890" -> "6281234567890"
     */
    public function getWaNumberAttribute(): string
    {
        $nomor = preg_replace('/\D/', '', (string) $this->no_wa);

        if (str_starts_with($nomor, '0')) {
            return '62' . substr($nomor, 1);
        }

        if (str_starts_with($nomor, '8')) {
            return '62' . $nomor;
        }

        return $nomor;
    }

    public function getMetodeSerahLabelAttribute(): string
    {
        return match ($this->metode_serah) {
            'ambil' => 'Ambil di toko',
            'kirim' => 'Dikirim',
            default => 'Ambil / kirim',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
            default => 'Menunggu persetujuan',
        };
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (blank($this->logo)) {
            return null;
        }

        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
            return $this->logo;
        }

        if (config('filesystems.default') === 's3') {
            return Storage::disk('s3')->url($this->logo);
        }

        return asset('storage/' . $this->logo);
    }
}

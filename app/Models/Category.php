<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
    ];

    /**
     * Relasi satu kategori memiliki banyak tanaman.
     */
    public function plants(): HasMany
    {
        return $this->hasMany(Plant::class);
    }
}

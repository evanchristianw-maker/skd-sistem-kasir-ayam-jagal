<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_menu',
        'kategori',
        'harga',
        'status_menu',
        'gambar',
        'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'status_menu' => 'boolean',
        ];
    }

    public function detailTransaksis(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\DetailTransaksi;

class Menu extends Model
{
    protected $fillable = [
        'nama_menu',
        'kategori',
        'harga',
        'status_menu',
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
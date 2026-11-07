<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    protected $fillable = [
        'nomor_transaksi',
        'user_id',
        'total_harga',
        'metode_pembayaran',
        'uang_dibayarkan',
        'kembalian',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2',
            'uang_dibayarkan' => 'decimal:2',
            'kembalian' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailTransaksis(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}
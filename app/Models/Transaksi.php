<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    // Konstanta status transaksi
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_MENUNGGU_KONFIRMASI = 'menunggu_konfirmasi';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    // Konstanta status pembayaran
    public const PEMBAYARAN_TERKONFIRMASI = 'terkonfirmasi';
    public const PEMBAYARAN_MENUNGGU_KONFIRMASI = 'menunggu_konfirmasi';
    public const PEMBAYARAN_DIBATALKAN = 'dibatalkan';

    // Konstanta metode pembayaran
    public const METODE_TUNAI = 'tunai';
    public const METODE_QRIS = 'qris';
    public const METODE_TRANSFER = 'transfer';

    protected $fillable = [
        'nomor_transaksi',
        'user_id',
        'total_harga',
        'metode_pembayaran',
        'uang_dibayarkan',
        'kembalian',
        'status',
        'rekening_bank_id',
        'nama_bank',
        'nomor_rekening',
        'atas_nama',
        'status_pembayaran',
        'dikonfirmasi_oleh',
        'dikonfirmasi_pada',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2',
            'uang_dibayarkan' => 'decimal:2',
            'kembalian' => 'decimal:2',
            'dikonfirmasi_pada' => 'datetime',
        ];
    }

    /**
     * Relasi ke kasir pembuat transaksi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke kasir yang mengonfirmasi transaksi transfer.
     */
    public function konfirmator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

    /**
     * Relasi ke rekening bank tujuan transfer.
     */
    public function rekeningBank(): BelongsTo
    {
        return $this->belongsTo(RekeningBank::class);
    }

    /**
     * Relasi ke detail menu yang dibeli.
     */
    public function detailTransaksis(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}

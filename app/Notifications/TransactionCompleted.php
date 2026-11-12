<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransactionCompleted extends Notification
{
    use Queueable;

    public function __construct(
        public array $transactionData
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'transaction_completed',
            'title' => 'Transaksi Berhasil',
            'message' => 'Transaksi ' . $this->transactionData['nomor_transaksi']
                . ' berhasil diselesaikan.',
            'nomor_transaksi' => $this->transactionData['nomor_transaksi'],
            'total_harga' => $this->transactionData['total_harga'],
            'nama_kasir' => $this->transactionData['nama_kasir'],
            'metode_pembayaran' => $this->transactionData['metode_pembayaran'],
            'waktu_transaksi' => $this->transactionData['waktu_transaksi'],
        ];
    }
}

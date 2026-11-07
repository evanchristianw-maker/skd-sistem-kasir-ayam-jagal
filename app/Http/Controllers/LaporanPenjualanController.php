<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LaporanPenjualanController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil parameter filter dari URL
        $periode = $request->query('period', '7d');
        $tanggalMulai = $request->query('start_date');
        $tanggalAkhir = $request->query('end_date');

        // Validasi parameter
        $request->validate([
            'period' => ['nullable', 'in:7d,30d,12m,custom,all'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        // Menentukan tanggal berdasarkan periode
        if ($periode === '7d' && !$tanggalMulai && !$tanggalAkhir) {
            $tanggalMulai = now()->subDays(6)->toDateString();
            $tanggalAkhir = now()->toDateString();
        } elseif ($periode === '30d' && !$tanggalMulai && !$tanggalAkhir) {
            $tanggalMulai = now()->subDays(29)->toDateString();
            $tanggalAkhir = now()->toDateString();
        } elseif ($periode === '12m' && !$tanggalMulai && !$tanggalAkhir) {
            $tanggalMulai = now()->subMonths(11)->startOfMonth()->toDateString();
            $tanggalAkhir = now()->toDateString();
        }

        // Mengambil transaksi beserta relasinya
        $query = Transaksi::with(['user', 'detailTransaksis']);

        if ($tanggalMulai) {
            $query->whereDate('created_at', '>=', $tanggalMulai);
        }

        if ($tanggalAkhir) {
            $query->whereDate('created_at', '<=', $tanggalAkhir);
        }

        // Mengambil transaksi terbaru
        $transaksi = $query->latest()->get();

        // Mengubah data agar sesuai dengan kebutuhan frontend
        $daftarTransaksi = $transaksi->map(function ($item) {
            return [
                'id' => $item->nomor_transaksi,
                'date' => $item->created_at->format('Y-m-d'),
                'time' => $item->created_at->format('H:i'),
                'cashier' => $item->user->nama_lengkap ?? 'Tidak diketahui',
                'items' => $item->detailTransaksis->sum('jumlah'),
                'total' => (float) $item->total_harga,

                'payment' => match ($item->metode_pembayaran) {
                    'tunai' => 'Tunai',
                    'qris' => 'QRIS',
                    'transfer' => 'Transfer',
                    default => 'Belum tersedia',
                },

                'status' => match ($item->status) {
                    'selesai' => 'Selesai',
                    default => 'Belum tersedia',
                },
            ];
        });

        // Menghitung ringkasan penjualan
        $totalPenjualan = $transaksi->sum('total_harga');
        $jumlahTransaksi = $transaksi->count();

        $jumlahProduk = $transaksi->sum(function ($item) {
            return $item->detailTransaksis->sum('jumlah');
        });

        $rataRata = $jumlahTransaksi > 0
            ? $totalPenjualan / $jumlahTransaksi
            : 0;

        return Inertia::render('Admin/Reports/Index', [
            'transactions' => $daftarTransaksi,
            'summary' => [
                'total_sales' => (float) $totalPenjualan,
                'transaction_count' => $jumlahTransaksi,
                'products_sold' => $jumlahProduk,
                'average_transaction' => (float) $rataRata,
            ],
            'filters' => [
                'period' => $periode,
                'start_date' => $tanggalMulai,
                'end_date' => $tanggalAkhir,
            ],
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->isAdmin(), 403, 'Hanya Admin yang dapat mengakses dashboard.');
        // Hanya transaksi yang berhasil diselesaikan.
        $transaksiSelesai = Transaksi::where('status', 'selesai');

        // Ringkasan dashboard.
        $summary = [
            'total_sales' => (float) (clone $transaksiSelesai)
                ->sum('total_harga'),

            'transaction_count' => (clone $transaksiSelesai)
                ->count(),

            'active_menus' => Menu::where('status_menu', true)
                ->count(),

            'inactive_menus' => Menu::where('status_menu', false)
                ->count(),
        ];

        // Grafik: penjualan per tanggal selama 12 bulan terakhir.
        $salesChart = Transaksi::query()
            ->where('status', 'selesai')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('SUM(total_harga) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(fn ($item) => [
                'date' => $item->date,
                'total' => (float) $item->total,
            ]);

        // Lima transaksi terbaru.
        $recentTransactions = Transaksi::query()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($item) => [
                'id' => $item->nomor_transaksi,
                'created_at' => $item->created_at,
                'total' => (float) $item->total_harga,
                'status' => $item->status,
            ]);

        return Inertia::render('Admin/Dashboard/Index', [
            'summary' => $summary,
            'sales_chart' => $salesChart,
            'recent_transactions' => $recentTransactions,
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'period' => ['nullable', 'in:7d,30d,12m'],
            'start_date' => ['nullable', 'date'],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
        ]);

        $period = $validated['period'] ?? '7d';

        $startDate = !empty($validated['start_date'])
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : match ($period) {
                '30d' => now()->subDays(29)->startOfDay(),
                '12m' => now()->subMonths(11)->startOfMonth(),
                default => now()->subDays(6)->startOfDay(),
            };

        $endDate = !empty($validated['end_date'])
            ? Carbon::parse($validated['end_date'])->endOfDay()
            : now()->endOfDay();

        // Pemasukan dari transaksi selesai.
        $incomeQuery = Transaksi::query()
            ->where('status', 'selesai')
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Pengeluaran operasional.
        $expenseQuery = Pengeluaran::query()
            ->whereBetween('tanggal', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ]);

        $totalIncome = (clone $incomeQuery)->sum('total_harga');
        $totalExpense = (clone $expenseQuery)->sum('jumlah');

        $transactions = collect();

        // Data pemasukan.
        $incomeTransactions = (clone $incomeQuery)
            ->latest()
            ->get()
            ->map(fn ($item) => [
                'id' => 'income-' . $item->id,
                'date' => $item->created_at->format('d/m/Y'),
                'description' => $item->nomor_transaksi,
                'category' => $item->metode_pembayaran ?? 'Penjualan',
                'type' => 'income',
                'amount' => (float) $item->total_harga,
                'sort_date' => $item->created_at,
            ]);

        // Data pengeluaran.
        $expenseTransactions = (clone $expenseQuery)
            ->latest('tanggal')
            ->latest('created_at')
            ->get()
            ->map(fn ($item) => [
                'id' => 'expense-' . $item->id,
                'date' => Carbon::parse($item->tanggal)->format('d/m/Y'),
                'description' => $item->keterangan ?? '-',
                'category' => $item->kategori,
                'type' => 'expense',
                'amount' => (float) $item->jumlah,
                'sort_date' => Carbon::parse($item->tanggal),
            ]);

        $transactions = $incomeTransactions
            ->concat($expenseTransactions)
            ->sortByDesc('sort_date')
            ->map(function ($item) {
                unset($item['sort_date']);
                return $item;
            })
            ->values();

        // Rincian pemasukan berdasarkan metode pembayaran.
        $incomeBreakdown = (clone $incomeQuery)
            ->selectRaw(
                'COALESCE(metode_pembayaran, ?) as category, SUM(total_harga) as amount',
                ['Penjualan']
            )
            ->groupBy('metode_pembayaran')
            ->get()
            ->map(fn ($item, $index) => [
                'id' => 'income-' . $index,
                'category' => $item->category,
                'type' => 'income',
                'amount' => (float) $item->amount,
            ]);

        // Rincian pengeluaran berdasarkan kategori.
        $expenseBreakdown = (clone $expenseQuery)
            ->selectRaw('kategori as category, SUM(jumlah) as amount')
            ->groupBy('kategori')
            ->get()
            ->map(fn ($item, $index) => [
                'id' => 'expense-' . $index,
                'category' => $item->category,
                'type' => 'expense',
                'amount' => (float) $item->amount,
            ]);

        $breakdown = $incomeBreakdown
            ->concat($expenseBreakdown)
            ->values();

        return Inertia::render('Admin/Finance/Index', [
            'summary' => [
                'total_income' => (float) $totalIncome,
                'total_expense' => (float) $totalExpense,
                'net_profit' => (float) $totalIncome - (float) $totalExpense,
            ],
            'filters' => [
                'period' => $period,
                'start_date' => $validated['start_date'] ?? '',
                'end_date' => $validated['end_date'] ?? '',
            ],
            'breakdown' => $breakdown,
            'transactions' => $transactions,
            'hasData' => $transactions->isNotEmpty(),
        ]);
    }
}
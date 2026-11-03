<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    
public function store(Request $request)
{
        // 1. Validasi data pesanan
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer', 'exists:menus,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        // 2. Proses penyimpanan transaksi
        $transaksi = DB::transaction(function () use ($validated, $request) {

            $totalHarga = 0;

            // Membuat nomor transaksi unik
            $nomorTransaksi = 'TRX-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));

            // Membuat transaksi utama
            $transaksi = Transaksi::create([
                'nomor_transaksi' => $nomorTransaksi,
                'user_id' => $request->user()->id,
                'total_harga' => 0,
            ]);

            // 3. Menyimpan setiap menu yang dipesan
            foreach ($validated['items'] as $item) {

                $menu = Menu::where('id', $item['menu_id'])
                    ->lockForUpdate()
                    ->first();

                // Memastikan menu masih aktif
                if (!$menu || !$menu->status_menu) {
                    throw ValidationException::withMessages([
                        'items' => ['Salah satu menu tidak tersedia atau sudah dinonaktifkan.'],
                    ]);
                }

                $hargaSatuan = (int) round((float) $menu->harga * 100);
                $subtotal = $hargaSatuan * $item['jumlah'];

                $totalHarga += $subtotal;

                $transaksi->detailTransaksis()->create([
                    'menu_id' => $menu->id,
                    'harga_satuan' => number_format($hargaSatuan / 100, 2, '.', ''),
                    'jumlah' => $item['jumlah'],
                    'subtotal' => number_format($subtotal / 100, 2, '.', ''),
                ]);
            }

            // 4. Memperbarui total transaksi
            $transaksi->update([
                'total_harga' => number_format($totalHarga / 100, 2, '.', ''),
            ]);

            return $transaksi;
        });

        // 5. Mengembalikan hasil transaksi dalam format JSON
        return response()->json([
            'message' => 'Transaksi berhasil dibuat.',
            'data' => $transaksi->load('detailTransaksis.menu'),
        ], 201);
    }

}

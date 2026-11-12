<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\User;
use App\Notifications\TransactionCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi request
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => [
                'required',
                'integer',
                'distinct',
                'exists:menus,id',
            ],
            'items.*.jumlah' => [
                'required',
                'integer',
                'min:1',
            ],
            'metode_pembayaran' => [
                'required',
                'string',
                'in:tunai,qris,transfer',
            ],
            'uang_dibayarkan' => [
                'required_if:metode_pembayaran,tunai',
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        // 2. Proses transaksi secara atomik
        $transaksi = DB::transaction(function () use ($validated, $request) {

            $totalHarga = 0;
            $itemsTersimpan = [];

            // 3. Ambil dan kunci menu, lalu hitung total
            foreach ($validated['items'] as $item) {

                $menu = Menu::where('id', $item['menu_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$menu || !$menu->status_menu) {
                    throw ValidationException::withMessages([
                        'items' => [
                            'Salah satu menu tidak tersedia atau sudah dinonaktifkan.',
                        ],
                    ]);
                }

                $hargaSatuan = (int) round((float) $menu->harga * 100);
                $subtotal = $hargaSatuan * $item['jumlah'];

                $totalHarga += $subtotal;

                $itemsTersimpan[] = [
                    'menu_id' => $menu->id,
                    'harga_satuan' => $hargaSatuan,
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $subtotal,
                ];
            }

            // 4. Tentukan nominal pembayaran
            $metodePembayaran = $validated['metode_pembayaran'];

            if ($metodePembayaran === 'tunai') {
                $uangDibayarkan = (int) round(
                    (float) $validated['uang_dibayarkan'] * 100
                );

                if ($uangDibayarkan < $totalHarga) {
                    throw ValidationException::withMessages([
                        'uang_dibayarkan' => [
                            'Uang yang dibayarkan kurang dari total belanja.',
                        ],
                    ]);
                }

                $kembalian = $uangDibayarkan - $totalHarga;
            } else {
                // QRIS dan transfer dianggap sudah diverifikasi kasir.
                $uangDibayarkan = $totalHarga;
                $kembalian = 0;
            }

            // 5. Buat nomor transaksi
            $nomorTransaksi = 'TRX-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(Str::random(5));

            // 6. Simpan header transaksi
            $transaksi = Transaksi::create([
                'nomor_transaksi' => $nomorTransaksi,
                'user_id' => $request->user()->id,
                'total_harga' => number_format(
                    $totalHarga / 100,
                    2,
                    '.',
                    ''
                ),
                'metode_pembayaran' => $metodePembayaran,
                'uang_dibayarkan' => number_format(
                    $uangDibayarkan / 100,
                    2,
                    '.',
                    ''
                ),
                'kembalian' => number_format(
                    $kembalian / 100,
                    2,
                    '.',
                    ''
                ),
                'status' => 'selesai',
            ]);

            // 7. Simpan detail transaksi
            foreach ($itemsTersimpan as $item) {
                $transaksi->detailTransaksis()->create([
                    'menu_id' => $item['menu_id'],
                    'harga_satuan' => number_format(
                        $item['harga_satuan'] / 100,
                        2,
                        '.',
                        ''
                    ),
                    'jumlah' => $item['jumlah'],
                    'subtotal' => number_format(
                        $item['subtotal'] / 100,
                        2,
                        '.',
                        ''
                    ),
                ]);
            }

            return $transaksi;
        });

        // 8. Kirim notifikasi ke seluruh akun Admin dan Kasir
        $notificationData = [
            'nomor_transaksi' => $transaksi->nomor_transaksi,
            'total_harga' => $transaksi->total_harga,
            'nama_kasir' => $request->user()->nama_lengkap,
            'metode_pembayaran' => $transaksi->metode_pembayaran,
            'waktu_transaksi' => $transaksi->created_at?->toIso8601String(),
        ];

        User::whereIn('role', ['admin', 'kasir'])
            ->get()
            ->each(function ($user) use ($notificationData) {
                $user->notify(new TransactionCompleted($notificationData));
            });

        // 9. Kembalikan hasil transaksi
        return response()->json([
            'message' => 'Transaksi berhasil dibuat.',
            'data' => $transaksi->load('detailTransaksis.menu'),
        ], 201);
    }
}
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
            'rekening_bank_id' => [
                'required_if:metode_pembayaran,transfer',
                'nullable',
                'integer',
                'exists:rekening_banks,id',
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

            // 3b. Validasi rekening bank aktif untuk transfer
            $rekeningBank = null;

            if ($validated['metode_pembayaran'] === 'transfer') {
                $rekeningBank = \App\Models\RekeningBank::find(
                    $validated['rekening_bank_id']
                );

                if (!$rekeningBank || !$rekeningBank->status_aktif) {
                    throw ValidationException::withMessages([
                        'rekening_bank_id' => [
                            'Rekening bank tidak ditemukan atau tidak aktif.',
                        ],
                    ]);
                }
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
            $statusTransaksi = $metodePembayaran === 'transfer'
                ? Transaksi::STATUS_MENUNGGU_KONFIRMASI
                : Transaksi::STATUS_SELESAI;

            $statusPembayaran = $metodePembayaran === 'transfer'
                ? Transaksi::PEMBAYARAN_MENUNGGU_KONFIRMASI
                : Transaksi::PEMBAYARAN_TERKONFIRMASI;

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
                'status' => $statusTransaksi,
                'status_pembayaran' => $statusPembayaran,
                'rekening_bank_id' => $rekeningBank?->id,
                'nama_bank' => $rekeningBank?->nama_bank,
                'nomor_rekening' => $rekeningBank?->nomor_rekening,
                'atas_nama' => $rekeningBank?->atas_nama,
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

        // 8. Kirim notifikasi hanya untuk transaksi yang benar-benar selesai
        if ($transaksi->status === Transaksi::STATUS_SELESAI) {
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
        }

        // 9. Kembalikan hasil transaksi
        return response()->json([
            'message' => 'Transaksi berhasil dibuat.',
            'data' => $transaksi->load('detailTransaksis.menu'),
        ], 201);
    }

    public function konfirmasi(Request $request, Transaksi $transaksi)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'kasir'], true)) {
            abort(403, 'Anda tidak memiliki akses ke aksi ini.');
        }

        if ($user->role === 'kasir' && $transaksi->user_id !== $user->id) {
            abort(403, 'Anda tidak dapat mengonfirmasi transaksi milik kasir lain.');
        }

        $updated = null;

        DB::transaction(function () use ($request, $transaksi, &$updated) {
            $locked = Transaksi::whereKey($transaksi->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->metode_pembayaran !== 'transfer') {
                throw ValidationException::withMessages([
                    'transaksi' => ['Hanya transaksi transfer yang dapat dikonfirmasi.'],
                ]);
            }

            if (
                $locked->status !== Transaksi::STATUS_MENUNGGU_KONFIRMASI ||
                $locked->status_pembayaran !== Transaksi::PEMBAYARAN_MENUNGGU_KONFIRMASI
            ) {
                throw ValidationException::withMessages([
                    'transaksi' => ['Transaksi tidak dalam status menunggu konfirmasi.'],
                ]);
            }

            $locked->update([
                'status' => Transaksi::STATUS_SELESAI,
                'status_pembayaran' => Transaksi::PEMBAYARAN_TERKONFIRMASI,
                'dikonfirmasi_oleh' => $request->user()->id,
                'dikonfirmasi_pada' => now(),
            ]);

            $updated = $locked->fresh()->load('detailTransaksis.menu');
        });

        $notificationData = [
            'nomor_transaksi' => $updated->nomor_transaksi,
            'total_harga' => $updated->total_harga,
            'nama_kasir' => $updated->user?->nama_lengkap ?? $request->user()->nama_lengkap,
            'metode_pembayaran' => $updated->metode_pembayaran,
            'waktu_transaksi' => $updated->created_at?->toIso8601String(),
        ];

        User::whereIn('role', ['admin', 'kasir'])
            ->get()
            ->each(function ($user) use ($notificationData) {
                $user->notify(new TransactionCompleted($notificationData));
            });

        return response()->json([
            'message' => 'Pembayaran transfer berhasil dikonfirmasi.',
            'data' => $updated,
        ], 200);
    }

    public function batalkan(Request $request, Transaksi $transaksi)
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'kasir'], true)) {
            abort(403, 'Anda tidak memiliki akses ke aksi ini.');
        }

        if ($user->role === 'kasir' && $transaksi->user_id !== $user->id) {
            abort(403, 'Anda tidak dapat membatalkan transaksi milik kasir lain.');
        }

        $updated = null;

        DB::transaction(function () use ($transaksi, &$updated) {
            $locked = Transaksi::whereKey($transaksi->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->metode_pembayaran !== 'transfer') {
                throw ValidationException::withMessages([
                    'transaksi' => ['Hanya transaksi transfer yang dapat dibatalkan.'],
                ]);
            }

            if (
                $locked->status !== Transaksi::STATUS_MENUNGGU_KONFIRMASI ||
                $locked->status_pembayaran !== Transaksi::PEMBAYARAN_MENUNGGU_KONFIRMASI
            ) {
                throw ValidationException::withMessages([
                    'transaksi' => ['Transaksi tidak dapat dibatalkan.'],
                ]);
            }

            $locked->update([
                'status' => Transaksi::STATUS_DIBATALKAN,
                'status_pembayaran' => Transaksi::PEMBAYARAN_DIBATALKAN,
            ]);

            $updated = $locked->fresh()->load('detailTransaksis.menu');
        });

        return response()->json([
            'message' => 'Transaksi transfer berhasil dibatalkan.',
            'data' => $updated,
        ], 200);
    }
}
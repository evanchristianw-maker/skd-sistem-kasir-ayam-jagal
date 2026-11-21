<?php

namespace App\Http\Controllers;

use App\Models\RekeningBank;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RekeningBankController extends Controller
{
    /**
     * Menampilkan daftar seluruh rekening bank untuk admin.
     */
    public function index(): Response
    {
        $rekeningBanks = RekeningBank::latest()
            ->withCount('transaksis')
            ->get()
            ->map(function ($rekening) {
                return [
                    'id' => $rekening->id,
                    'nama_bank' => $rekening->nama_bank,
                    'nomor_rekening' => $rekening->nomor_rekening,
                    'atas_nama' => $rekening->atas_nama,
                    'status_aktif' => $rekening->status_aktif,
                    'transaksis_count' => $rekening->transaksis_count,
                    'created_at' => $rekening->created_at->format('d M Y'),
                ];
            });

        return Inertia::render('Admin/RekeningBank/Index', [
            'rekeningBanks' => $rekeningBanks,
        ]);
    }

    /**
     * Menambahkan rekening bank baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bank' => 'required|string|max:50',
            'nomor_rekening' => 'required|string|max:50',
            'atas_nama' => 'required|string|max:100',
            'status_aktif' => 'nullable|boolean',
        ]);

        RekeningBank::create([
            'nama_bank' => $validated['nama_bank'],
            'nomor_rekening' => $validated['nomor_rekening'],
            'atas_nama' => $validated['atas_nama'],
            'status_aktif' => $validated['status_aktif'] ?? true,
        ]);

        Inertia::flash('success', 'Rekening bank berhasil ditambahkan.');

        return redirect()->route('rekening-bank.index');
    }

    /**
     * Memperbarui data rekening bank.
     */
    public function update(Request $request, RekeningBank $rekeningBank)
    {
        $validated = $request->validate([
            'nama_bank' => 'required|string|max:50',
            'nomor_rekening' => 'required|string|max:50',
            'atas_nama' => 'required|string|max:100',
            'status_aktif' => 'nullable|boolean',
        ]);

        $rekeningBank->update([
            'nama_bank' => $validated['nama_bank'],
            'nomor_rekening' => $validated['nomor_rekening'],
            'atas_nama' => $validated['atas_nama'],
            'status_aktif' => array_key_exists('status_aktif', $validated)
                ? (bool) $validated['status_aktif']
                : $rekeningBank->status_aktif,
        ]);

        Inertia::flash('success', 'Rekening bank berhasil diperbarui.');

        return redirect()->route('rekening-bank.index');
    }

    /**
     * Menghapus rekening bank (soft delete).
     * Hanya boleh dihapus jika belum pernah digunakan dalam transaksi.
     */
    public function destroy(RekeningBank $rekeningBank)
    {
        if ($rekeningBank->transaksis()->exists()) {
            Inertia::flash('error', 'Rekening bank tidak dapat dihapus karena sudah pernah digunakan dalam transaksi. Silakan nonaktifkan status rekening.');

            return redirect()->route('rekening-bank.index');
        }

        $rekeningBank->delete();

        Inertia::flash('success', 'Rekening bank berhasil dihapus.');

        return redirect()->route('rekening-bank.index');
    }

    /**
     * Mengubah status aktif/nonaktif rekening bank.
     */
    public function toggleStatus(RekeningBank $rekeningBank)
    {
        $rekeningBank->update([
            'status_aktif' => !$rekeningBank->status_aktif,
        ]);

        $status = $rekeningBank->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        Inertia::flash('success', "Rekening bank berhasil {$status}.");

        return redirect()->route('rekening-bank.index');
    }
}
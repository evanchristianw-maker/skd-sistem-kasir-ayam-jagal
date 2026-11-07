<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Menampilkan seluruh daftar menu.
     */
    public function index(): Response
    {
        $menus = Menu::latest()->get()->map(function ($menu) {
            return [
                'id' => $menu->id,
                'name' => $menu->nama_menu,
                'category' => $menu->kategori,
                'price' => (float) $menu->harga,
                'status' => $menu->status_menu,
                'description' => '',
                'image' => '',
            ];
        });

        return Inertia::render('Admin/Menu/Index', [
        'menus' => $menus,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori' => 'required|in:Makanan,Minuman,Paket,Tambahan',
            'harga' => 'required|numeric|min:1',
        ]);

        Menu::create([
            'nama_menu' => $validated['nama_menu'],
            'kategori' => $validated['kategori'],
            'harga' => $validated['harga'],
            'status_menu' => true,
        ]);

        return redirect()
            ->route('menu.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Memperbarui data menu.
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori' => 'required|in:Makanan,Minuman,Paket,Tambahan',
            'harga' => 'required|numeric|min:1',
        ]);

        $menu->update([
            'nama_menu' => $validated['nama_menu'],
            'kategori' => $validated['kategori'],
            'harga' => $validated['harga'],
        ]);

        return redirect()
            ->route('menu.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Menghapus data menu.
     */
    public function destroy(Menu $menu)
    {
        // Periksa apakah menu pernah digunakan dalam transaksi.
        if ($menu->detailTransaksis()->exists()) {
            return back()->withErrors([
                'menu' => 'Menu tidak dapat dihapus karena sudah digunakan dalam transaksi.',
            ]);
        }

        $menu->delete();

        return redirect()
            ->route('menu.index')
            ->with('success', 'Menu berhasil dihapus.');
    }

    /**
     * Mengubah status aktif atau nonaktif menu.
     */
    public function toggleStatus(Menu $menu)
    {
        $menu->update([
            'status_menu' => !$menu->status_menu,
        ]);

        return redirect()
            ->route('menu.index')
            ->with('success', 'Status menu berhasil diperbarui.');
    }

    /**
     * Menampilkan daftar menu aktif untuk kasir.
     */
    public function cashierIndex(): Response
    {
        $menus = Menu::where('status_menu', true)
            ->latest()
            ->get()
            ->map(function ($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->nama_menu,
                    'category' => $menu->kategori,
                    'price' => (float) $menu->harga,
                ];
            });

        return Inertia::render('Cashier/Transactions/Index', [
            'menus' => $menus,
        ]);
    }
}
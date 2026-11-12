<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Menampilkan seluruh daftar menu untuk admin.
     */
    public function index(): Response
    {
        $menus = Menu::latest()->get()->map(function ($menu) {
            return [
                'id' => $menu->id,
                'name' => $menu->nama_menu,
                'description' => $menu->deskripsi ?? '',
                'category' => $menu->kategori,
                'price' => (float) $menu->harga,
                'status' => $menu->status_menu,
                'image' => $menu->gambar
                    ? Storage::disk('public')->url($menu->gambar)
                    : '',
            ];
        });

        return Inertia::render('Admin/Menu/Index', [
            'menus' => $menus,
        ]);
    }

    /**
     * Menambahkan menu baru beserta gambar dan deskripsi.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'kategori' => 'required|in:Makanan,Minuman,Paket,Tambahan',
            'harga' => 'required|numeric|min:1',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Upload gambar jika tersedia.
        $gambarPath = $request->hasFile('gambar')
            ? $request->file('gambar')->store('menus', 'public')
            : null;

        Menu::create([
            'nama_menu' => $validated['nama_menu'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'kategori' => $validated['kategori'],
            'harga' => $validated['harga'],
            'status_menu' => true,
            'gambar' => $gambarPath,
        ]);

        return redirect()
            ->route('menu.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Memperbarui data menu dan mengganti gambar jika ada gambar baru.
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'kategori' => 'required|in:Makanan,Minuman,Paket,Tambahan',
            'harga' => 'required|numeric|min:1',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'nama_menu' => $validated['nama_menu'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'kategori' => $validated['kategori'],
            'harga' => $validated['harga'],
        ];

        // Jika admin mengunggah gambar baru.
        if ($request->hasFile('gambar')) {
            $gambarPathBaru = $request->file('gambar')
                ->store('menus', 'public');

            $gambarPathLama = $menu->gambar;

            $data['gambar'] = $gambarPathBaru;

            $menu->update($data);

            // Hapus gambar lama setelah gambar baru tersimpan.
            if ($gambarPathLama) {
                Storage::disk('public')->delete($gambarPathLama);
            }
        } else {
            $menu->update($data);
        }

        return redirect()
            ->route('menu.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Menghapus data menu.
     */
    public function destroy(Menu $menu)
    {
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
                    'description' => $menu->deskripsi ?? '',
                    'category' => $menu->kategori,
                    'price' => (float) $menu->harga,
                    'image' => $menu->gambar
                        ? Storage::disk('public')->url($menu->gambar)
                        : '',
                ];
            });

        return Inertia::render('Cashier/Transactions/Index', [
            'menus' => $menus,
        ]);
    }
}
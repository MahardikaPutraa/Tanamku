<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    /**
     * Form profil toko (sekaligus dipakai saat pertama kali mendaftar).
     */
    public function edit()
    {
        $store = auth()->user()->store;

        return view('seller.store-form', compact('store'));
    }

    /**
     * Simpan / perbarui profil toko.
     */
    public function update(Request $request)
    {
        $store = auth()->user()->store;

        $data = $request->validate([
            'nama_toko' => ['required', 'string', 'max:100'],
            'no_wa' => ['required', 'string', 'max:20', 'regex:/^[0-9\+\-\s]{8,20}$/'],
            'alamat' => ['required', 'string', 'min:10', 'max:500'],
            'kota' => ['required', 'string', 'max:80'],
            'deskripsi' => ['required', 'string', 'min:20', 'max:1000'],
            'metode_serah' => ['required', 'in:ambil,kirim,keduanya'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'no_wa.regex' => 'Nomor WhatsApp hanya boleh angka, spasi, tanda + atau -.',
            'deskripsi.min' => 'Deskripsi toko minimal 20 karakter.',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('stores', 'public');
        }

        if ($store) {
            $store->update($data);

            return back()->with('success', 'Profil toko diperbarui.');
        }

        $data['user_id'] = auth()->id();
        $data['slug'] = $this->slugUnik($data['nama_toko']);
        $data['status'] = 'menunggu';

        Store::create($data);

        return redirect()->route('seller.dashboard')
            ->with('success', 'Toko berhasil didaftarkan. Menunggu persetujuan admin sebelum produk tampil di katalog.');
    }

    /**
     * Slug unik dari nama toko.
     */
    private function slugUnik(string $nama): string
    {
        $dasar = Str::slug($nama) ?: 'toko';
        $slug = $dasar;
        $i = 2;

        while (Store::where('slug', $slug)->exists()) {
            $slug = $dasar . '-' . $i;
            $i++;
        }

        return $slug;
    }
}

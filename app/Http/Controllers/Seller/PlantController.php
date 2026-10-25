<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlantController extends Controller
{
    /**
     * Daftar produk milik toko sendiri.
     */
    public function index(Request $request)
    {
        $store = $this->toko();

        $plants = $store->plants()
            ->with('category')
            ->when($request->filled('q'), fn ($q) => $q->where('nama', 'like', '%' . $request->q . '%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('seller.plants.index', compact('store', 'plants'));
    }

    public function create()
    {
        $store = $this->toko();
        $categories = Category::orderBy('nama')->get();

        return view('seller.plants.form', compact('store', 'categories'));
    }

    public function store(Request $request)
    {
        $store = $this->toko();
        $data = $this->validated($request);

        $data['store_id'] = $store->id;
        $data['slug'] = $this->slugUnik($data['nama']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = true;
        $data['foto'] = $request->hasFile('foto')
            ? $request->file('foto')->store('plants', 'public')
            : null;

        Plant::create($data);

        return redirect()->route('seller.plants.index')
            ->with('success', 'Produk "' . $data['nama'] . '" ditambahkan.');
    }

    public function edit(Plant $plant)
    {
        $this->pastikanMilikSendiri($plant);

        $store = $this->toko();
        $categories = Category::orderBy('nama')->get();

        return view('seller.plants.form', compact('store', 'plant', 'categories'));
    }

    public function update(Request $request, Plant $plant)
    {
        $this->pastikanMilikSendiri($plant);

        $data = $this->validated($request);

        if ($plant->nama !== $data['nama']) {
            $data['slug'] = $this->slugUnik($data['nama'], $plant->id);
        }

        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('plants', 'public');
        }

        $plant->update($data);

        return redirect()->route('seller.plants.index')
            ->with('success', 'Produk "' . $plant->nama . '" diperbarui.');
    }

    /**
     * Hapus produk. Bila sudah pernah dipesan, cukup dinonaktifkan
     * agar riwayat pesanan tetap utuh.
     */
    public function destroy(Plant $plant)
    {
        $this->pastikanMilikSendiri($plant);

        if ($plant->orderItems()->exists()) {
            $plant->update(['is_active' => false]);

            return back()->with('warning', 'Produk ini sudah pernah dipesan, jadi hanya dinonaktifkan (tidak tampil di katalog) agar riwayat pesanan tetap utuh.');
        }

        $plant->delete();

        return back()->with('success', 'Produk "' . $plant->nama . '" dihapus.');
    }

    private function toko()
    {
        $store = auth()->user()->store;

        abort_if(! $store, 403, 'Anda belum memiliki toko.');

        return $store;
    }

    private function pastikanMilikSendiri(Plant $plant): void
    {
        abort_if($plant->store_id !== $this->toko()->id, 403, 'Produk ini bukan milik toko Anda.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'deskripsi' => ['required', 'string', 'min:20'],
            'perawatan' => ['nullable', 'string', 'max:2000'],
            'harga' => ['required', 'numeric', 'min:1', 'max:9999999999'],
            'stok' => ['required', 'integer', 'min:0'],
            'kondisi' => ['required', 'in:sehat,perlu_perhatian'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
        ], [
            'deskripsi.min' => 'Deskripsi minimal 20 karakter.',
            'foto.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);
    }

    private function slugUnik(string $nama, ?int $abaikanId = null): string
    {
        $dasar = Str::slug($nama) ?: 'tanaman';
        $slug = $dasar;
        $i = 2;

        while (Plant::where('slug', $slug)
            ->when($abaikanId, fn ($q) => $q->whereKeyNot($abaikanId))
            ->exists()) {
            $slug = $dasar . '-' . $i;
            $i++;
        }

        return $slug;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('plants')->orderBy('nama')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:80'],
            'deskripsi' => ['nullable', 'string', 'max:300'],
        ]);

        $data['slug'] = $this->slugUnik($data['nama']);

        Category::create($data);

        return back()->with('success', 'Kategori "' . $data['nama'] . '" ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:80'],
            'deskripsi' => ['nullable', 'string', 'max:300'],
        ]);

        if ($category->nama !== $data['nama']) {
            $data['slug'] = $this->slugUnik($data['nama'], $category->id);
        }

        $category->update($data);

        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->plants()->exists()) {
            return back()->with('error', 'Kategori "' . $category->nama . '" masih dipakai oleh '
                . $category->plants()->count() . ' produk, jadi tidak bisa dihapus.');
        }

        $category->delete();

        return back()->with('success', 'Kategori "' . $category->nama . '" dihapus.');
    }

    private function slugUnik(string $nama, ?int $abaikanId = null): string
    {
        $dasar = Str::slug($nama) ?: 'kategori';
        $slug = $dasar;
        $i = 2;

        while (Category::where('slug', $slug)
            ->when($abaikanId, fn ($q) => $q->whereKeyNot($abaikanId))
            ->exists()) {
            $slug = $dasar . '-' . $i;
            $i++;
        }

        return $slug;
    }
}

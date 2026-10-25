<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_publik_merespons_ok(): void
    {
        $this->seed();

        $this->get('/')->assertStatus(200);
        $this->get('/katalog')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
    }

    public function test_halaman_terproteksi_mengarahkan_ke_login(): void
    {
        $this->seed();

        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/admin/produk')->assertRedirect('/login');
        $this->get('/seller/dashboard')->assertRedirect('/login');
        $this->get('/keranjang')->assertRedirect('/login');
        $this->get('/pesanan')->assertRedirect('/login');
    }

    /** Admin tidak punya beranda publik — langsung ke panel. */
    public function test_admin_diarahkan_dari_beranda_ke_panel(): void
    {
        $this->seed();

        $admin = \App\Models\User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get('/')
            ->assertRedirect(route('admin.dashboard'));

        // Pembeli & tamu tetap bisa membuka beranda.
        $buyer = \App\Models\User::where('role', 'buyer')->firstOrFail();
        $this->actingAs($buyer)->get('/')->assertOk();
        $this->get('/')->assertOk();
    }
}

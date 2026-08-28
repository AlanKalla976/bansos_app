<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityAccessControlTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function test_masyarakat_cannot_access_admin_dashboard()
    {
        $masyarakat = User::where('role', 'masyarakat')->first();
        if (!$masyarakat) {
            $masyarakat = User::create([
                'nik' => '3273010101900099',
                'name' => 'Warga Biasa',
                'email' => 'warga_test@bansos.com',
                'password' => Hash::make('warga123'),
                'role' => 'masyarakat',
            ]);
        }

        $response = $this->actingAs($masyarakat, 'web')
            ->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    /** @test */
    public function test_masyarakat_direct_access_via_url_to_admin_route_is_denied()
    {
        $masyarakat = User::where('role', 'masyarakat')->first();
        if (!$masyarakat) {
            $masyarakat = User::create([
                'nik' => '3273010101900099',
                'name' => 'Warga Biasa',
                'email' => 'warga_test@bansos.com',
                'password' => Hash::make('warga123'),
                'role' => 'masyarakat',
            ]);
        }

        $response = $this->actingAs($masyarakat, 'web')
            ->get('/admin/kriteria');

        $response->assertRedirect(route('admin.login'));
    }

    /** @test */
    public function test_admin_can_access_admin_dashboard_and_kriteria()
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'nik' => '3273010101900098',
                'name' => 'Administrator',
                'email' => 'admin_test@bansos.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
        }

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);

        $responseKriteria = $this->actingAs($admin, 'admin')
            ->get(route('admin.kriteria.index'));

        $responseKriteria->assertStatus(200);
    }

    /** @test */
    public function test_petugas_can_access_petugas_dashboard_and_validation()
    {
        $petugas = User::where('role', 'petugas')->first();
        if (!$petugas) {
            $petugas = User::create([
                'nik' => '3273010101900097',
                'name' => 'Petugas Bansos',
                'email' => 'petugas_test@bansos.com',
                'password' => Hash::make('petugas123'),
                'role' => 'petugas',
            ]);
        }

        $response = $this->actingAs($petugas, 'petugas')
            ->get(route('admin.petugas.dashboard'));

        $response->assertStatus(200);

        $responseValidasi = $this->actingAs($petugas, 'petugas')
            ->get(route('admin.petugas.validasi.index'));

        $responseValidasi->assertStatus(200);
    }

    /** @test */
    public function test_lurah_can_access_lurah_dashboard_and_persetujuan()
    {
        $lurah = User::where('role', 'lurah')->first();
        if (!$lurah) {
            $lurah = User::create([
                'nik' => '3273010101900096',
                'name' => 'Lurah Harjamukti',
                'email' => 'lurah_test@bansos.com',
                'password' => Hash::make('lurah123'),
                'role' => 'lurah',
            ]);
        }

        $response = $this->actingAs($lurah, 'lurah')
            ->get(route('admin.lurah.dashboard'));

        $response->assertStatus(200);

        $responsePersetujuan = $this->actingAs($lurah, 'lurah')
            ->get(route('admin.lurah.persetujuan.index'));

        $responsePersetujuan->assertStatus(200);
    }

    /** @test */
    public function test_admin_cannot_access_petugas_routes()
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'nik' => '3273010101900098',
                'name' => 'Administrator',
                'email' => 'admin_test@bansos.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
        }

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.petugas.validasi.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_petugas_cannot_access_admin_routes()
    {
        $petugas = User::where('role', 'petugas')->first();
        if (!$petugas) {
            $petugas = User::create([
                'nik' => '3273010101900097',
                'name' => 'Petugas Bansos',
                'email' => 'petugas_test@bansos.com',
                'password' => Hash::make('petugas123'),
                'role' => 'petugas',
            ]);
        }

        $response = $this->actingAs($petugas, 'petugas')
            ->get(route('admin.kriteria.index'));

        $response->assertStatus(403);
    }
}

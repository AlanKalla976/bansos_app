<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function test_ahp_moora_performance_response_time()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin, 'Admin user must exist in database.');

        // 1. Mengukur waktu respons perhitungan AHP
        $startAhp = microtime(true);
        $responseAhp = $this->actingAs($admin, 'admin')
            ->post(route('admin.kriteria.hitung'));
        $endAhp = microtime(true);
        
        $durationAhp = ($endAhp - $startAhp) * 1000; // dalam milidetik
        echo "\n[PERFORMANCE] AHP response time: " . number_format($durationAhp, 2) . " ms\n";

        $responseAhp->assertStatus(302); // Harus merujuk kembali (redirect)

        // 2. Mengukur waktu respons perhitungan MOORA
        $startMoora = microtime(true);
        $responseMoora = $this->actingAs($admin, 'admin')
            ->post(route('admin.penilaian.hitung'));
        $endMoora = microtime(true);

        $durationMoora = ($endMoora - $startMoora) * 1000; // dalam milidetik
        echo "[PERFORMANCE] MOORA response time: " . number_format($durationMoora, 2) . " ms\n";

        $responseMoora->assertStatus(302); // Harus merujuk kembali (redirect)
    }
}

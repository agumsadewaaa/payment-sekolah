<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Str;

class HomeViewFallbackTest extends TestCase
{
    /**
     * Render the home view with empty / null datasets and assert fallbacks appear.
     */
    public function test_home_view_shows_fallbacks_for_empty_data()
    {
        // make a temporary user so layout rendering (Auth::user()) succeeds
        $user = User::factory()->make(['name' => 'Tester', 'created_at' => now()]);
        $this->actingAs($user);

        $html = view('home', [
            'totalSiswa' => null,
            'totalKas' => null,
            'pemasukanRange' => null,
            'pengeluaranRange' => null,
            'tanggalBulan' => [],
            'dataPemasukan' => [],
            'dataPengeluaran' => [],
            'siswaProgress' => null,
            'latestPengeluaran' => null,
            'range' => 'month',
            'rangeLabel' => 'Bulan Ini',
            'start' => Carbon::today(),
            'end' => Carbon::today(),
        ])->render();

        // The page should display fallback dash for monetary values and 0 for total siswa
        $this->assertStringContainsString('<h3 class="mb-0 fw-bold">0</h3>', $html);
        $this->assertStringContainsString('—', $html); // fallback dash should appear for null currency fields

        // Table fallback messages
        $this->assertStringContainsString('Tidak ada data', $html);

        // Chart variables should be safe JSON arrays
        $this->assertStringContainsString('const labels   = []', $html);
        $this->assertStringContainsString('const pemasukan = []', $html);
        $this->assertStringContainsString('const pengeluaran = []', $html);
    }
}

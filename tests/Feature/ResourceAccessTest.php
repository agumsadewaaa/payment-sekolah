<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ResourceAccessTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // ensure roles exist
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'user', 'guard_name' => 'web']);
    }

    public function test_admin_can_crud_kelas()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post('/kelas', ['kode' => 'X-TEST', 'kelas' => '10', 'jurusan' => 'TEST'])
            ->assertStatus(302); // redirect after success

        $kelas = \DB::table('tb_kelas')->where('kode', 'X-TEST')->first();
        $this->assertNotNull($kelas);

        // edit
        $this->actingAs($admin)
            ->put('/kelas/'.$kelas->id, ['kode' => 'X-TEST', 'kelas' => '11', 'jurusan' => 'TEST'])
            ->assertStatus(302);

        // delete
        $this->actingAs($admin)
            ->delete('/kelas/'.$kelas->id)
            ->assertStatus(302);
    }

    public function test_regular_user_cannot_mutate_kelas_but_can_view()
    {
        $user = User::factory()->create(['role' => 'user']);
        $user->assignRole('user');

        // create attempt -> forbidden
        $this->actingAs($user)
            ->post('/kelas', ['kode' => 'X-USER', 'kelas' => '10', 'jurusan' => 'TEST'])
            ->assertStatus(403);

        // view index should be allowed (auth middleware only)
        $this->actingAs($user)
            ->get('/kelas')
            ->assertStatus(200);
    }

    public function test_admin_can_crud_siswa_and_user_cannot()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        // create Kelas for siswa jurusan and then create new siswa (admin)
        $kelas = \App\Models\Kelas::create(['kode' => 'RC-1', 'kelas' => '10', 'jurusan' => 'TKJ']);

        $this->actingAs($admin)
            ->post('/siswas', [
                'nama' => 'Test Siswa',
                'nis' => '1234567890',
                'kontak_ortu' => '0812345678',
                'kelas' => '10',
                'jurusan' => $kelas->id,
                'tahun_masuk' => 2025,
                'tahun_lulus' => null,
                'status_siswa' => 'Aktif'
            ])->assertStatus(302);

        $siswa = \DB::table('tb_siswa')->where('nama', 'Test Siswa')->first();
        $this->assertNotNull($siswa);

        // regular user cannot create
        $user = User::factory()->create(['role' => 'user']);
        $user->assignRole('user');

        $this->actingAs($user)
            ->post('/siswas', [
                'nama' => 'User Siswa',
            ])
            ->assertStatus(403);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_login_is_logged()
    {
        $password = 'password';
        $user = User::factory()->create(['password' => \Illuminate\Support\Facades\Hash::make($password)]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/home');

        $this->assertDatabaseHas('login_logs', ['user_id' => $user->id]);
    }

    public function test_admin_crud_generates_activity_logs()
    {
        // ensure role exists and make admin
        \Spatie\Permission\Models\Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // create a siswa
        $this->actingAs($admin)
            ->post('/siswas', [
                'nama' => 'Log Siswa',
                'nisn' => '999999999',
                'kontak_ortu' => '0812345678',
                'kelas' => '10',
                'jurusan' => 'RPL',
                'tahun_masuk' => 2025,
                'tahun_lulus' => null,
                'status_siswa' => 'Aktif'
            ])->assertRedirect();

        $siswa = \DB::table('tb_siswa')->where('nama', 'Log Siswa')->first();
        $this->assertNotNull($siswa);

        // there should be a created activity log
        $this->assertDatabaseHas('activity_logs', ['action' => 'created', 'model_type' => 'App\\Models\\Siswa', 'model_id' => (string) $siswa->id]);

        // update
        $this->actingAs($admin)
            ->put('/siswas/'.$siswa->id, ['nama' => 'Log Siswa Updated', 'nisn' => $siswa->nisn, 'kelas' => $siswa->kelas, 'jurusan' => $siswa->jurusan, 'kontak_ortu' => $siswa->kontak_ortu, 'tahun_masuk' => $siswa->tahun_masuk, 'tahun_lulus' => $siswa->tahun_lulus, 'status_siswa' => $siswa->status_siswa])
            ->assertRedirect();

        $this->assertDatabaseHas('activity_logs', ['action' => 'updated', 'model_type' => 'App\\Models\\Siswa', 'model_id' => (string) $siswa->id]);

        // delete
        $this->actingAs($admin)
            ->delete('/siswas/'.$siswa->id)
            ->assertRedirect();

        $this->assertDatabaseHas('activity_logs', ['action' => 'deleted', 'model_type' => 'App\\Models\\Siswa', 'model_id' => (string) $siswa->id]);
    }
}

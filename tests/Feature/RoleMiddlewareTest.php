<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_access_admin_route()
    {
        // create admin user in DB and make sure spatie role exists/assigned
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->assignRole('admin');

        // create a test route that uses the middleware
        $this->app['router']->get('/test-admin-route', function () {
            return response('ok', 200);
        })->middleware(['web', 'auth', 'role:admin']);

        $response = $this->actingAs($admin)->get('/test-admin-route');
        $response->assertStatus(200)->assertSee('ok');
    }

    public function test_non_admin_forbidden()
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->app['router']->get('/test-admin-route-2', function () {
            return response('ok', 200);
        })->middleware(['web', 'auth', 'role:admin']);

        $response = $this->actingAs($user)->get('/test-admin-route-2');
        $response->assertStatus(403);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
// Avoid running all project migrations in test environment (some migrations conflict in CI)
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    public function test_admin_can_access_admin_route()
    {
        // create admin user instance (not necessarily persisted) to avoid running full migrations in tests
        $admin = User::factory()->make(['role' => 'admin']);

        // create a test route that uses the middleware
        $this->app['router']->get('/test-admin-route', function () {
            return response('ok', 200);
        })->middleware(['web', 'auth', 'role:admin']);

        $response = $this->actingAs($admin)->get('/test-admin-route');
        $response->assertStatus(200)->assertSee('ok');
    }

    public function test_non_admin_forbidden()
    {
        $user = User::factory()->make(['role' => 'student']);

        $this->app['router']->get('/test-admin-route-2', function () {
            return response('ok', 200);
        })->middleware(['web', 'auth', 'role:admin']);

        $response = $this->actingAs($user)->get('/test-admin-route-2');
        $response->assertStatus(403);
    }
}

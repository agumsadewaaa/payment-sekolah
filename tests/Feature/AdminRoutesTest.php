<?php

namespace Tests\Feature;

use App\Models\User;
// Avoid running project migrations inside tests to keep them fast and isolated
use Tests\TestCase;

class AdminRoutesTest extends TestCase
{
    public function test_admin_can_access_admin_page()
    {
        $admin = User::factory()->make(['role' => 'admin']);

        $this->app['router']->get('/admin', function () {
            return response('admin page');
        })->middleware(['web', 'auth', 'role:admin']);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200)->assertSee('admin page');
    }

    public function test_non_admin_redirects_forbidden_admin_page()
    {
        $user = User::factory()->make(['role' => 'student']);

        $this->app['router']->get('/admin', function () {
            return response('admin page');
        })->middleware(['web', 'auth', 'role:admin']);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }
}

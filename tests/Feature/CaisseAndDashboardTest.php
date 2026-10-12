<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class CaisseAndDashboardTest extends TestCase
{
    public function test_authenticated_agent_can_view_ma_caisse(): void
    {
        $user = User::where('login', 'admin')->first();

        $response = $this->actingAs($user)->get('/caisse/ma-caisse');
        $response->assertStatus(200);
        $response->assertViewIs('caisse.ma_caisse');
    }

    public function test_authenticated_agent_can_view_caisse_par_depart(): void
    {
        $user = User::where('login', 'admin')->first();

        $response = $this->actingAs($user)->get('/caisse/par-depart');
        $response->assertStatus(200);
        $response->assertViewIs('caisse.par_depart');
    }

    public function test_administrator_can_view_caisse_global(): void
    {
        $admin = User::where('login', 'admin')->first();

        $response = $this->actingAs($admin)->get('/caisse/global');
        $response->assertStatus(200);
        $response->assertViewIs('caisse.global');
    }

    public function test_administrator_can_view_dashboard(): void
    {
        $admin = User::where('login', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Aduan;

class SuaraDesaFlowTest extends TestCase
{
    public function test_beranda_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SuaraDesa');
    }

    public function test_buat_pengaduan_page_is_accessible(): void
    {
        $response = $this->get('/aduan');
        $response->assertStatus(200);
        $response->assertSee('Ceritakan yang perlu diperbaiki');
    }

    public function test_lacak_pengaduan_page_is_accessible(): void
    {
        $response = $this->get('/aduan/lacak');
        $response->assertStatus(200);
        $response->assertSee('Lacak Pengaduan');
    }

    public function test_detail_pengaduan_page_is_accessible(): void
    {
        $aduan = Aduan::first();
        if ($aduan) {
            $response = $this->get("/aduan/detail/{$aduan->id_aduan}");
            $response->assertStatus(200);
            $response->assertSee($aduan->kode_tiket);
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_tiket_page_is_accessible(): void
    {
        $aduan = Aduan::first();
        if ($aduan) {
            $response = $this->get("/aduan/tiket/{$aduan->kode_tiket}");
            $response->assertStatus(200);
            $response->assertSee($aduan->kode_tiket);
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_unauthenticated_dashboard_redirects_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Pelayanan yang terus bergerak');
    }

    public function test_authenticated_admin_can_access_aduan_list(): void
    {
        $user = User::first() ?? User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard/aduan');
        $response->assertStatus(200);
        $response->assertSee('Daftar Pengaduan Masuk');
    }

    public function test_authenticated_admin_can_access_aduan_tindakan(): void
    {
        $user = User::first() ?? User::factory()->create();
        $aduan = Aduan::first();

        if ($aduan) {
            $response = $this->actingAs($user)->get("/dashboard/aduan/{$aduan->id_aduan}");
            $response->assertStatus(200);
            $response->assertSee($aduan->kode_tiket);
            $response->assertSee('Perbarui Tindak Lanjut');
        } else {
            $this->assertTrue(true);
        }
    }
}

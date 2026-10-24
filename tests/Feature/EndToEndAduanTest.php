<?php

namespace Tests\Feature;

use Tests\TestCase;
use Livewire\Livewire;
use App\Models\User;
use App\Models\Aduan;

class EndToEndAduanTest extends TestCase
{
    public function test_full_aduan_lifecycle_from_submission_to_petugas_action(): void
    {
        $uniqueJudul = 'Lampu Padam ' . uniqid();

        // 1. Citizen creates a complaint
        Livewire::test('aduan.create')
            ->set('judul_aduan', $uniqueJudul)
            ->set('deskripsi_aduan', 'Lampu jalan utama di RT 02 mati total sudah 3 malam dan jalan menjadi sangat gelap gulita membahayakan warga.')
            ->set('lokasi', 'Jl. Melati RT 02 dekat jembatan')
            ->set('kontak', '081234567890')
            ->set('setuju', true)
            ->call('save');

        // Check complaint exists
        $aduan = Aduan::where('judul_aduan', $uniqueJudul)->latest('id_aduan')->first();
        $this->assertNotNull($aduan);
        $this->assertEquals('diajukan', $aduan->status_aduan);

        // Check AI scoring was automatically performed
        $this->assertNotNull($aduan->penilaianPrioritas);
        $this->assertNotNull($aduan->penilaianPrioritas->targetRespon);
        $this->assertNotNull($aduan->laporan);

        // 2. Petugas accesses dashboard tindakan and resolves the complaint
        $admin = User::first() ?? User::factory()->create();

        Livewire::actingAs($admin)
            ->test('aduan.tindakan', ['id_aduan' => $aduan->id_aduan])
            ->set('status_aduan', 'selesai')
            ->set('tanggapan', 'Bohlam dan kabel penerangan jalan telah diganti oleh petugas teknis desa, lampu sudah menyala normal kembali.')
            ->set('petugas_penanggung_jawab', 'Budi Santoso (Tim Sarana Desa)')
            ->call('simpanTindakLanjut')
            ->assertHasNoErrors();

        // Check database was updated with petugas response
        $aduan->refresh();
        $this->assertEquals('selesai', $aduan->status_aduan);
        $this->assertEquals('Bohlam dan kabel penerangan jalan telah diganti oleh petugas teknis desa, lampu sudah menyala normal kembali.', $aduan->tanggapan);
        $this->assertEquals('Budi Santoso (Tim Sarana Desa)', $aduan->petugas_penanggung_jawab);
        $this->assertNotNull($aduan->penilaianPrioritas->targetRespon->waktu_respon);

        // 3. Citizen tracks the ticket and sees the updated status and tanggapan
        Livewire::test('aduan.lacak')
            ->set('kode_tiket', $aduan->kode_tiket)
            ->call('lacak')
            ->assertSet('result.status_aduan', 'selesai')
            ->assertSet('result.tanggapan', 'Bohlam dan kabel penerangan jalan telah diganti oleh petugas teknis desa, lampu sudah menyala normal kembali.')
            ->assertSee('Bohlam dan kabel penerangan jalan telah diganti');
    }
}

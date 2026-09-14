<?php

namespace Tests\Feature;

use App\Models\Ketidakhadiran;
use App\Models\LokasiPresensi;
use App\Models\Pegawai;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresensiFullFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $staffUser;
    protected Pegawai $adminPegawai;
    protected Pegawai $staffPegawai;
    protected LokasiPresensi $lokasi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->adminUser = User::where('role', 'admin')->first();
        $this->staffUser = User::where('role', 'staff')->first();
        $this->adminPegawai = $this->adminUser->pegawai;
        $this->staffPegawai = $this->staffUser->pegawai;
        $this->lokasi = $this->staffPegawai->lokasiPresensiModel();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/staff/dashboard');
        $response->assertRedirect(route('login'));

        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('login'));
    }

    public function test_staff_cannot_access_admin_pages(): void
    {
        $response = $this->actingAs($this->staffUser)->get('/admin/dashboard');
        $response->assertRedirect(route('staff.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_admin_cannot_access_staff_pages(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/staff/dashboard');
        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_staff_dashboard_loads_correctly(): void
    {
        $response = $this->actingAs($this->staffUser)->get('/staff/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Pegawai');
        $response->assertSee($this->staffPegawai->nama);
    }

    public function test_staff_presensi_page_loads(): void
    {
        $response = $this->actingAs($this->staffUser)->get('/staff/presensi');
        $response->assertStatus(200);
        $response->assertSee('Presensi Online');
        $response->assertSee($this->lokasi->nama_lokasi);
    }

    public function test_gps_location_validation_api(): void
    {
        // Coordinate exactly at office
        $response = $this->actingAs($this->staffUser)->postJson('/staff/presensi/validate-location', [
            'latitude' => (float) $this->lokasi->latitude,
            'longitude' => (float) $this->lokasi->longitude,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'in_radius' => true,
        ]);

        // Coordinate far away (e.g., Paris latitude)
        $responseFar = $this->actingAs($this->staffUser)->postJson('/staff/presensi/validate-location', [
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        $responseFar->assertStatus(200);
        $responseFar->assertJson([
            'success' => true,
            'in_radius' => false,
        ]);
    }

    public function test_staff_presensi_submission_validation(): void
    {
        // Reject if out of radius
        $dummyBase64 = 'data:image/jpeg;base64,' . base64_encode('fake-image-binary-data');

        $response = $this->actingAs($this->staffUser)->postJson('/staff/presensi/submit', [
            'tipe' => 'masuk',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'foto' => $dummyBase64,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);

        // Accept when inside office radius
        $clockInResponse = $this->actingAs($this->staffUser)->postJson('/staff/presensi/submit', [
            'tipe' => 'masuk',
            'latitude' => (float) $this->lokasi->latitude,
            'longitude' => (float) $this->lokasi->longitude,
            'foto' => $dummyBase64,
        ]);

        $clockInResponse->assertStatus(200);
        $clockInResponse->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('presensi', [
            'id_pegawai' => $this->staffPegawai->id,
            'tanggal_masuk' => date('Y-m-d'),
        ]);

        // Clock-out
        $clockOutResponse = $this->actingAs($this->staffUser)->postJson('/staff/presensi/submit', [
            'tipe' => 'keluar',
            'latitude' => (float) $this->lokasi->latitude,
            'longitude' => (float) $this->lokasi->longitude,
            'foto' => $dummyBase64,
        ]);

        $clockOutResponse->assertStatus(200);
        $clockOutResponse->assertJson([
            'success' => true,
        ]);
    }

    public function test_staff_riwayat_presensi_page(): void
    {
        $response = $this->actingAs($this->staffUser)->get('/staff/riwayat');
        $response->assertStatus(200);
        $response->assertSee('Riwayat Presensi Pribadi');
    }

    public function test_staff_can_submit_leave_request(): void
    {
        $response = $this->actingAs($this->staffUser)->get('/staff/ketidakhadiran/create');
        $response->assertStatus(200);
        $response->assertSee('Ajukan Izin / Ketidakhadiran');

        $postResponse = $this->actingAs($this->staffUser)->post('/staff/ketidakhadiran', [
            'keterangan' => 'Cuti',
            'tanggal' => date('Y-m-d'),
            'deskripsi' => 'Cuti tahunan keperluan keluarga penting',
        ]);

        $postResponse->assertRedirect(route('staff.ketidakhadiran.index'));
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('ketidakhadiran', [
            'id_pegawai' => $this->staffPegawai->id,
            'keterangan' => 'Cuti',
            'status_pengajuan' => 'menunggu',
        ]);
    }

    public function test_staff_profile_page_and_update(): void
    {
        $response = $this->actingAs($this->staffUser)->get('/staff/profile');
        $response->assertStatus(200);
        $response->assertSee('Profil Pegawai');

        $updateResponse = $this->actingAs($this->staffUser)->put('/staff/profile', [
            'no_handphone' => '081299998888',
            'alamat' => 'Jl. Pengujian Staff No. 123',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('pegawai', [
            'id' => $this->staffPegawai->id,
            'no_handphone' => '081299998888',
            'alamat' => 'Jl. Pengujian Staff No. 123',
        ]);
    }

    public function test_admin_profile_page_and_update(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/profile');
        $response->assertStatus(200);
        $response->assertSee('Profil Administrator');

        $updateResponse = $this->actingAs($this->adminUser)->put('/admin/profile', [
            'nama' => 'Budi Santoso Updated',
            'no_handphone' => '081211112222',
            'alamat' => 'Jl. Kantor Pusat No. 1',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('pegawai', [
            'id' => $this->adminPegawai->id,
            'nama' => 'Budi Santoso Updated',
        ]);
    }

    public function test_admin_can_approve_staff_leave_request(): void
    {
        $leave = Ketidakhadiran::create([
            'id_pegawai' => $this->staffPegawai->id,
            'keterangan' => 'Sakit',
            'tanggal' => date('Y-m-d'),
            'deskripsi' => 'Flu berat',
            'status_pengajuan' => 'menunggu',
        ]);

        $response = $this->actingAs($this->adminUser)->patch(route('admin.ketidakhadiran.status', $leave->id), [
            'status_pengajuan' => 'disetujui',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ketidakhadiran', [
            'id' => $leave->id,
            'status_pengajuan' => 'disetujui',
        ]);
    }
}

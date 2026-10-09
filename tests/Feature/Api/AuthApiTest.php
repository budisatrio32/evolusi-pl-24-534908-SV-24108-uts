<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private function buatUser(): User
    {
        return User::factory()->create([
            'email' => 'admin@kepl.test',
            'password' => 'password',
        ]);
    }

    public function test_login_berhasil_mengembalikan_token(): void
    {
        $this->buatUser();

        $this->postJson('/api/login', ['email' => 'admin@kepl.test', 'password' => 'password'])
            ->assertOk()
            ->assertJsonStructure(['message', 'token_type', 'access_token', 'user' => ['id', 'name', 'email']])
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonMissingPath('user.password');

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_login_dengan_password_salah_ditolak_401(): void
    {
        $this->buatUser();

        $this->postJson('/api/login', ['email' => 'admin@kepl.test', 'password' => 'salah'])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Email atau password salah.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_login_dengan_email_tidak_terdaftar_ditolak_401(): void
    {
        $this->postJson('/api/login', ['email' => 'tidak.ada@kepl.test', 'password' => 'password'])
            ->assertUnauthorized();
    }

    public function test_login_tanpa_isian_mengembalikan_422(): void
    {
        $this->postJson('/api/login', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_token_hasil_login_dapat_dipakai_mengakses_me(): void
    {
        $this->buatUser();

        $token = $this->postJson('/api/login', ['email' => 'admin@kepl.test', 'password' => 'password'])
            ->json('access_token');

        $this->withToken($token)
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'admin@kepl.test');
    }

    public function test_me_tanpa_token_mengembalikan_401(): void
    {
        $this->getJson('/api/me')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_request_tanpa_header_accept_json_tetap_dibalas_json(): void
    {
        // Postman/curl kadang tidak mengirim Accept: application/json.
        $this->get('/api/me')
            ->assertUnauthorized()
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_logout_mencabut_token(): void
    {
        $this->buatUser();

        $token = $this->postJson('/api/login', ['email' => 'admin@kepl.test', 'password' => 'password'])
            ->json('access_token');

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logout berhasil.');

        $this->assertDatabaseCount('personal_access_tokens', 0);

        // Guard di-reset supaya request berikutnya benar-benar memeriksa token ke basis data.
        $this->app['auth']->forgetGuards();

        $this->withToken($token)->getJson('/api/me')->assertUnauthorized();
    }
}

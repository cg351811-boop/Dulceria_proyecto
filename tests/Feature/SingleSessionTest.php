<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SingleSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_session_can_access_dashboard_and_check_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->get('/dashboard');
        $response->assertOk();

        $user->last_session_id = null;
        $user->save();
        $check = $this->actingAs($user)->getJson('/check-session');
        $check->assertOk();
        $check->assertJson(['status' => 'active']);
    }

    public function test_displaced_session_is_redirected_to_login_with_message_on_web_request(): void
    {
        $user = User::factory()->create();

        // El usuario tiene una sesión activa en otro dispositivo ('sesion_dispositivo_b')
        $user->last_session_id = 'sesion_dispositivo_b';
        $user->save();

        // Simulamos que el dispositivo A (con sesión actual distinta) intenta entrar al dashboard
        $this->actingAs($user);

        // La sesión de este dispositivo A tiene un ID diferente
        $this->assertNotEquals($user->last_session_id, session()->getId());

        $response = $this->get('/dashboard');

        // Debe ser redirigido a login con mensaje de sesion_cerrada
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('sesion_cerrada');

        // Debe haber cerrado la sesión del dispositivo A
        $this->assertGuest();
    }

    public function test_displaced_session_returns_401_on_check_session_call(): void
    {
        $user = User::factory()->create();

        // El usuario inició sesión en otro dispositivo
        $user->last_session_id = 'sesion_dispositivo_b';
        $user->save();

        // Dispositivo A hace ping a /check-session
        $this->actingAs($user);

        $response = $this->getJson('/check-session');

        // Debe retornar 401 indicando que la sesión fue cerrada
        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'session_closed',
            'message' => 'Se cerró la sesión porque se inició sesión en otro dispositivo con esta cuenta.'
        ]);

        $this->assertGuest();
    }

    public function test_logging_in_updates_user_last_session_id_and_activity(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $user->refresh();
        $this->assertNotEmpty($user->last_session_id);
        $this->assertEquals(session()->getId(), $user->last_session_id);
        $this->assertNotEmpty(session('last_user_activity'));
    }

    public function test_session_expires_after_5_minutes_of_inactivity_on_web_request(): void
    {
        $user = User::factory()->create();

        // Simulamos sesión autenticada con última actividad hace 6 minutos
        $this->actingAs($user);
        session(['last_user_activity' => now()->subMinutes(6)->timestamp]);

        $response = $this->get('/dashboard');

        // Debe redirigir a login con el mensaje de inactividad
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('inactividad');
        $this->assertGuest();
    }

    public function test_session_expires_after_5_minutes_of_inactivity_on_check_session(): void
    {
        $user = User::factory()->create();

        // Simulamos sesión autenticada con última actividad hace 6 minutos
        $this->actingAs($user);
        session(['last_user_activity' => now()->subMinutes(6)->timestamp]);

        $response = $this->getJson('/check-session');

        // Debe retornar 401 indicando expiración por inactividad
        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'session_timeout',
            'message' => 'Tu sesión ha expirado tras 5 minutos de inactividad por motivos de seguridad.'
        ]);
        $this->assertGuest();
    }
}

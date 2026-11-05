<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_name_with_numbers_fails_registration_with_custom_message(): void
    {
        $response = $this->post('/register', [
            'name' => 'Juan123',
            'email' => 'juan@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'El nombre solo debe contener letras, no se permiten números.',
        ]);
        $this->assertGuest();
    }

    public function test_name_with_letters_and_spaces_succeeds(): void
    {
        $response = $this->post('/register', [
            'name' => 'María José López',
            'email' => 'maria@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}

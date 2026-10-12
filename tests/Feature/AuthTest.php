<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_user_can_login_with_login_username(): void
    {
        $response = $this->post('/login', [
            'email'    => 'admin',
            'password' => 'admin123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_user_can_login_with_email_address(): void
    {
        $response = $this->post('/login', [
            'email'    => 'admin@diarratransport.ml',
            'password' => 'admin123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email'    => 'admin',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}

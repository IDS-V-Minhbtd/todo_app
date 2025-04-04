<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_email_max_length()
    {
        $longEmail = str_repeat('a', 201) . '@example.com';

        $response = $this->post('/login', [
            'email' => $longEmail,
            'password' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_email_must_be_valid()
    {
        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_email_must_exist_in_database()
    {
        $response = $this->post('/login', [
            'email' => 'notfound@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_min_length()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'Short1!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password_must_contain_uppercase_number_special_character()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123', // Thiếu in hoa và ký tự đặc biệt
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123!'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect('/home'); // Cập nhật theo đường dẫn chính xác
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_password()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123!'),
        ]);
    
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'WrongPassword!',
        ]);
    
        $response->assertSessionHasErrors('password'); // Sửa lại từ 'email' thành 'password'
        $this->assertGuest();
    }
    
}

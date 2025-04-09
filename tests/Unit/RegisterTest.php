<?php 
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
   /** @test */
public function email_is_required()
{
    $response = $this->post('/register', [
        'name' => 'Test',
        'email' => '',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
    ]);

    $response->assertSessionHasErrors('email');
}

/** @test */
public function email_cannot_exceed_200_characters()
{
    $email = str_repeat('a', 201) . '@example.com';
    $response = $this->post('/register', [
        'name' => 'Test',
        'email' => $email,
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
    ]);

    $response->assertSessionHasErrors('email');
}

/** @test */
public function email_must_be_unique()
{
    User::factory()->create(['email' => 'test@example.com']);

    $response = $this->post('/register', [
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
    ]);

    $response->assertSessionHasErrors('email');
}

/** @test */
public function email_must_be_valid_format()
{
    $response = $this->post('/register', [
        'name' => 'Test',
        'email' => 'invalid-email',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
    ]);

    $response->assertSessionHasErrors('email');
}

/** @test */
public function password_is_required()
{
    $response = $this->post('/register', [
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => '',
        'password_confirmation' => '',
    ]);

    $response->assertSessionHasErrors('password');
}

/** @test */
public function password_must_be_at_least_8_characters()
{
    $response = $this->post('/register', [
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => '1234567',
        'password_confirmation' => '1234567',
    ]);

    $response->assertSessionHasErrors('password');
}

/** @test */
public function password_must_include_uppercase_number_special_character()
{
    $response = $this->post('/register', [
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'password123', // thiếu ký tự hoa & đặc biệt
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('password');
}
}
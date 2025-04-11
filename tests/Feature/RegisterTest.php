<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void //setup du lieu
    {
        parent::setUp();
    }

    
    /** @test email là bắt buộc*/
    public function email_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => '',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);
        $response->assertStatus(302); // Kiểm tra mã trạng thái HTTP
        $response->assertSessionHasErrors(['email' => 'Email là bắt buộc.']);
    }

    /** @test email không quá 200 kí tự*/
    public function email_cannot_exceed_200_characters()
    {
        $email = str_repeat('a', 201) . '@example.com';
        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => $email,
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email' => 'Email không được vượt quá 200 ký tự.']);
    }

    /** @test email bị trùng*/
    public function email_must_be_unique()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email' => 'Email đã tồn tại.']);
    }

    /** @test email không hợp lệ*/
    public function email_must_be_valid_format()
    {
        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => 'invalid-email',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);
        $response->assertStatus(302); // Kiểm tra mã trạng thái HTTP
        $response->assertSessionHasErrors(['email' => 'Email không đúng định dạng.']);
    }

    /** @test password là bắt buộc*/
    public function password_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'Mật khẩu là bắt buộc.']);
    }

    /** @test password ít nhất 8 kí tự*/
    public function password_must_be_at_least_8_characters()
    {
        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'abc@123', // 7 ký tự
            'password_confirmation' => 'abc@123',
        ]);

        $response->assertSessionHasErrors(['password' => 'Mật khẩu phải có ít nhất 8 ký tự.']);
    }

    /** @test password phải có kí tự đặc biệt*/
    public function password_must_include_uppercase_number_special_character()
    {
        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123', 
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'Mật khẩu phải bao gồm ít nhất một chữ cái in hoa, một số và một ký tự đặc biệt.',
        ]);
    }


}
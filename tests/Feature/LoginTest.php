<?php 
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void 
    {
        parent::setUp();
        
    }


    /** @test với thông tin đăng nhập đúng */
    public function user_can_login_with_correct_credentials()
    {
        // Tạo user trước
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password@123'),
        ]);

        // Gửi request đăng nhập
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'Password@123',
        ]);
        $response->assertStatus(302); // Kiểm tra mã trạng thái HTTP
        $response->assertRedirect('/home'); 
        $this->assertAuthenticatedAs($user);
    }

    /** @test  với thông tin đăng nhập sai */
    public function user_cannot_login_with_invalid_credentials()
    {
        // Có user
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password@123'),
        ]);

        // Đăng nhập sai password
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
        $response->assertStatus(302); // Kiểm tra mã trạng thái HTTP
        $response->assertSessionHasErrors(); 
        $this->assertGuest();
    }

    /** @test  với không có thông tin đăng nhập */
    public function unauthenticated_user_redirected_to_login_when_accessing_protected_route()
    {
        $response = $this->get('/home');
        $response->assertStatus(302); // Kiểm tra mã trạng thái HTTP
        $response->assertRedirect('/login');
    }
}

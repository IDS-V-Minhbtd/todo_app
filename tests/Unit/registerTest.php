<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\RegisterRequest;

class RegisterRequestTest extends TestCase
{
    protected function getValidator(array $data)
    {
        $request = new RegisterRequest();
        $rules = $request->rules();
        return Validator::make($data, $rules);
    }

    /** @test data hợp lệ */
    public function test_valid_data_passes_validation()
{
    $data = [
        'email' => 'user@example.com',
        'name' => 'testuser',
        'password' => 'Password@123', // ✅ có chữ hoa, số, đặc biệt
        'password_confirmation' => 'Password@123',
    ];

    $validator = $this->getValidator($data);

    $this->assertTrue($validator->passes());
}

    /** @test email là bắt buộc */
    public function test_email_is_required()
    {
        $data = [
            'email' => '',
            'name' => 'testuser',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ];

        $validator = $this->getValidator($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /** @test password ít nhất 8 kí tự  */
    public function test_password_must_be_at_least_8_characters()
    {
        $data = [
            'email' => 'user@example.com',
            'name' => 'testuser',
            'password' => 'Pass1',
            'password_confirmation' => 'Pass1',
        ];

        $validator = $this->getValidator($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /** @test confirm password */
    public function test_password_must_match_confirmation()
    {
        $data = [
            'email' => 'user@example.com',
            'name' => 'testuser',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@456',
        ];

        $validator = $this->getValidator($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }
    /** @test password phải có ký tự đặc biệt */
    public function test_password_must_have_special_character()
    {
    $data = [
        'email' => 'user@example.com',
        'name' => 'testuser',
        'password' => 'Password123', // Không có ký tự đặc biệt
        'password_confirmation' => 'Password123',
    ];

    $validator = $this->getValidator($data);

    $this->assertFalse($validator->passes());
    $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }
}

<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function name_is_required()
    {
        $data = ['name' => ''];
        $rules = ['name' => 'required'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function email_is_required()
    {
        $data = ['email' => ''];
        $rules = ['email' => 'required'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /** @test */
    public function email_must_be_valid_format()
    {
        $data = ['email' => 'invalid-email'];
        $rules = ['email' => 'email'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /** @test */
    public function email_must_be_unique()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $data = ['email' => 'test@example.com'];
        $rules = ['email' => 'unique:users,email'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /** @test */
    public function password_is_required()
    {
        $data = ['password' => ''];
        $rules = ['password' => 'required'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /** @test */
    public function password_must_be_at_least_8_characters()
    {
        $data = ['password' => '1234567'];
        $rules = ['password' => 'min:8'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /** @test */
    public function password_must_include_uppercase_number_and_special_character()
    {
        $data = ['password' => 'password1'];
        $rules = ['password' => 'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /** @test */
    public function password_must_be_confirmed()
    {
        $data = [
            'password' => 'Password@123',
            'password_confirmation' => 'WrongPassword',
        ];
        $rules = ['password' => 'confirmed'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /** @test */
    public function user_can_register_successfully()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'testee1@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/home');
        $this->assertDatabaseHas('users', ['email' => 'testee1@example.com']);
    }
}

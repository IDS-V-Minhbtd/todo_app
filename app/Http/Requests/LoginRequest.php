<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|max:200|exists:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/', // Ít nhất một chữ in hoa
                'regex:/[0-9]/', // Ít nhất một số
                'regex:/[@$!%*?&]/', // Ít nhất một ký tự đặc biệt
            ],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không hợp lệ.',
            'email.max' => 'Email không được dài quá 200 ký tự.',
            'email.exists' => 'Email này chưa đăng ký.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ in hoa, 1 số và 1 ký tự đặc biệt.',
        ];
    }
}

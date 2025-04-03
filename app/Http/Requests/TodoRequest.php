<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoRequest extends FormRequest
{
    /**
     * Xác nhận xem user có quyền gửi request không.
     */
    public function authorize(): bool
    {
        return true; // Cho phép tất cả user đã đăng nhập
    }

    /**
     * Định nghĩa rules validation.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => $userId
                ? ['nullable', 'string', 'min:8', 'confirmed']
                : ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(['warehouse_manager', 'driver', 'resident'])],
            'phone'    => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Vui lòng nhập họ tên.',
            'email.required'     => 'Vui lòng nhập email.',
            'email.email'        => 'Email không hợp lệ.',
            'email.unique'       => 'Email này đã được sử dụng.',
            'password.required'  => 'Vui lòng nhập mật khẩu.',
            'password.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'role.required'      => 'Vui lòng chọn vai trò.',
            'role.in'            => 'Vai trò không hợp lệ.',
        ];
    }
}

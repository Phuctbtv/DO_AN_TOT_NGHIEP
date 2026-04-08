<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HouseholdRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public route – ai cũng được phép đăng ký
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:100'],
            'identity_card' => ['required', 'string', 'size:12', 'regex:/^[0-9]{12}$/'],
            'phone'         => ['required', 'string', 'max:20'],
            'address'       => ['required', 'string', 'max:500'],
            'lat'           => ['nullable', 'numeric', 'between:-90,90'],
            'lng'           => ['nullable', 'numeric', 'between:-180,180'],
            'member_count'  => ['nullable', 'integer', 'min:1', 'max:50'],
            'scene_image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5MB
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Vui lòng nhập họ tên.',
            'identity_card.required' => 'Vui lòng nhập số CCCD.',
            'identity_card.size'     => 'Số CCCD phải đủ 12 chữ số.',
            'identity_card.regex'    => 'Số CCCD chỉ được chứa chữ số.',
            'phone.required'         => 'Vui lòng nhập số điện thoại.',
            'address.required'       => 'Vui lòng nhập địa chỉ.',
            'scene_image.max'        => 'Ảnh không được vượt quá 5MB.',
        ];
    }
}

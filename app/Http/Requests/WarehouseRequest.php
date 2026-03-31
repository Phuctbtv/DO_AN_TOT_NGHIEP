<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'address'    => ['required', 'string', 'max:500'],
            'lat'        => ['nullable', 'numeric', 'between:-90,90'],
            'lng'        => ['nullable', 'numeric', 'between:-180,180'],
            'manager_id' => ['nullable', 'exists:users,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'       => 'tên kho',
            'address'    => 'địa chỉ',
            'lat'        => 'vĩ độ',
            'lng'        => 'kinh độ',
            'manager_id' => 'quản lý kho',
        ];
    }
}

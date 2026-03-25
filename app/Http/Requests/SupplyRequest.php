<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'      => ['required', 'integer', 'exists:categories,id'],
            'name'             => ['required', 'string', 'max:255'],
            'unit'             => ['required', 'string', 'max:50'],
            'min_stock_alert'  => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required'     => 'Vui lòng chọn danh mục.',
            'category_id.exists'       => 'Danh mục không hợp lệ.',
            'name.required'            => 'Vui lòng nhập tên nhu yếu phẩm.',
            'name.max'                 => 'Tên không được vượt quá 255 ký tự.',
            'unit.required'            => 'Vui lòng nhập đơn vị tính.',
            'unit.max'                 => 'Đơn vị không được vượt quá 50 ký tự.',
            'min_stock_alert.required' => 'Vui lòng nhập mức cảnh báo tồn kho.',
            'min_stock_alert.integer'  => 'Mức cảnh báo phải là số nguyên.',
            'min_stock_alert.min'      => 'Mức cảnh báo phải >= 0.',
        ];
    }
}

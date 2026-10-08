<?php

namespace App\Http\Requests;

use App\Support\ContactFormat;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Guest checkout is intentionally supported.
    }

    protected function prepareForValidation(): void
    {
        foreach (['customer_name', 'customer_address', 'notes'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }
        if (is_string($this->input('customer_phone'))) {
            $this->merge(['customer_phone' => ContactFormat::phone($this->input('customer_phone'))]);
        }
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'min:2', 'max:100', 'regex:/\p{L}/u'],
            'customer_phone' => ['required', 'string', 'regex:'.ContactFormat::PHONE_PATTERN],
            'customer_address' => ['required', 'string', 'min:5', 'max:255', 'regex:/\p{L}/u'],
            'payment_method' => ['required', 'in:cod,vietqr'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1', 'max:200'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.unit_id' => ['nullable', 'integer', 'exists:product_units,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Vui lòng nhập :attribute.',
            'string' => ':attribute phải là văn bản.',
            'customer_name.min' => 'Họ tên cần ít nhất 2 ký tự.',
            'customer_name.regex' => 'Họ tên cần có chữ cái.',
            'customer_phone.regex' => 'Số điện thoại chưa đúng định dạng Việt Nam (ví dụ: 0912 345 678 hoặc +84 912 345 678).',
            'customer_address.min' => 'Địa chỉ quá ngắn. Vui lòng ghi rõ địa chỉ nhận hàng.',
            'customer_address.regex' => 'Địa chỉ cần có tên đường, khu vực hoặc địa danh bằng chữ.',
            'max' => ':attribute vượt quá giới hạn cho phép.',
            'items.required' => 'Giỏ hàng đang trống. Vui lòng chọn sản phẩm.',
            'items.min' => 'Giỏ hàng đang trống. Vui lòng chọn sản phẩm.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return ['customer_name' => 'họ tên', 'customer_phone' => 'số điện thoại', 'customer_address' => 'địa chỉ nhận hàng', 'notes' => 'ghi chú', 'items' => 'giỏ hàng'];
    }
}

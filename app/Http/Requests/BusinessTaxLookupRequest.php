<?php

namespace App\Http\Requests;

use App\Services\BusinessTaxLookup;
use Illuminate\Foundation\Http\FormRequest;

class BusinessTaxLookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public company data only; throttled, no private account lookup.
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('tax_code'))) {
            $this->merge(['tax_code' => BusinessTaxLookup::normalize($this->input('tax_code'))]);
        }
    }

    public function rules(): array
    {
        return ['tax_code' => ['required', 'string', 'regex:/^(?:[0-9]{10}(?:-[0-9]{3})?|[0-9]{12})$/D']];
    }

    public function messages(): array
    {
        return [
            'tax_code.required' => 'Vui lòng nhập mã số thuế.',
            'tax_code.string' => 'Mã số thuế phải là chuỗi ký tự.',
            'tax_code.regex' => 'Nhập MST doanh nghiệp 10 số hoặc MST chi nhánh 13 số.',
        ];
    }
}

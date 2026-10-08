<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class InvoiceDetails
{
    public function validate(array $input): array
    {
        $flag = Validator::make($input, ['is_vat_invoice' => ['sometimes', 'boolean']])->validate();
        $enabled = (bool) ($flag['is_vat_invoice'] ?? false);
        $data = ['is_vat_invoice' => $enabled];
        foreach (['company_tax_id', 'company_name', 'company_address', 'invoice_email'] as $key) {
            $data[$key] = $enabled ? ($input[$key] ?? null) : null;
            if (is_string($data[$key])) {
                $data[$key] = trim($data[$key]);
            }
        }
        if (is_string($data['company_tax_id'])) {
            $data['company_tax_id'] = BusinessTaxLookup::normalize($data['company_tax_id']);
        }

        return Validator::make($data, [
            'is_vat_invoice' => ['boolean'],
            'company_tax_id' => [Rule::requiredIf($enabled), 'nullable', 'string', 'regex:/^(?:[0-9]{10}(?:-[0-9]{3})?|[0-9]{12})$/D'],
            'company_name' => [Rule::requiredIf($enabled), 'nullable', 'string', 'min:2', 'max:255', 'regex:/\p{L}/u'],
            'company_address' => [Rule::requiredIf($enabled), 'nullable', 'string', 'min:5', 'max:255', 'regex:/\p{L}/u'],
            'invoice_email' => [Rule::requiredIf($enabled), 'nullable', 'email:rfc', 'max:255', 'regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/D'],
        ], [
            'required' => 'Vui lòng nhập :attribute để yêu cầu hóa đơn.',
            'string' => ':attribute phải là văn bản.',
            'max' => ':attribute không được dài quá :max ký tự.',
            'min' => ':attribute quá ngắn. Vui lòng nhập đầy đủ.',
            'company_tax_id.regex' => 'MST cần 10 số, 12 số hoặc MST chi nhánh gồm 10 số và 3 số cuối.',
            'company_name.regex' => 'Tên người mua hoặc doanh nghiệp cần có chữ cái.',
            'company_address.regex' => 'Địa chỉ xuất hóa đơn cần có tên đường hoặc địa danh bằng chữ.',
            'invoice_email.email' => 'Email nhận hóa đơn chưa đúng định dạng.',
            'invoice_email.regex' => 'Email nhận hóa đơn cần có tên miền đầy đủ, ví dụ ketoan@congty.vn.',
        ], [
            'company_tax_id' => 'mã số thuế', 'company_name' => 'tên người mua / doanh nghiệp',
            'company_address' => 'địa chỉ xuất hóa đơn', 'invoice_email' => 'email nhận hóa đơn',
        ])->validate();
    }
}

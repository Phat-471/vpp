<?php

namespace App\Http\Requests;

use App\Support\ChatContent;
use App\Support\ContactFormat;
use Illuminate\Foundation\Http\FormRequest;

class LiveChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ownership comes exclusively from the server-side browser session.
    }

    protected function prepareForValidation(): void
    {
        $values = [];
        foreach (['message', 'customer_name', 'customer_phone'] as $key) {
            if (is_string($this->input($key))) {
                $values[$key] = trim($this->input($key));
            }
        }
        if (isset($values['customer_phone'])) {
            $values['customer_phone'] = ContactFormat::phone($values['customer_phone']);
        }
        $this->merge($values);
    }

    public function rules(): array
    {
        $rules = [
            'customer_name' => ['nullable', 'string', 'min:2', 'max:100', 'not_regex:/[\x00-\x1F\x7F]/'],
            'customer_phone' => ['nullable', 'string', 'regex:'.ContactFormat::PHONE_PATTERN],
            'after_id' => ['nullable', 'integer', 'min:0'],
            'before_id' => ['nullable', 'integer', 'min:1'],
        ];
        if ($this->routeIs('chat.send')) {
            $rules['message'] = ChatContent::rules();
            $rules['request_id'] = ['required', 'uuid'];
        }
        if ($this->routeIs('chat.read')) {
            $rules['through_id'] = ['required', 'integer', 'min:1'];
            $rules['from_id'] = ['required', 'integer', 'min:1', 'lte:through_id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            ...ChatContent::messages('message'),
            'customer_name.min' => 'Tên cần có ít nhất 2 ký tự.',
            'customer_name.max' => 'Tên không được vượt quá 100 ký tự.',
            'customer_name.not_regex' => 'Tên có ký tự không hợp lệ. Hãy kiểm tra lại.',
            'customer_phone.regex' => 'Số điện thoại chưa đúng định dạng Việt Nam. Ví dụ: 0901234567.',
            'request_id.uuid' => 'Không thể xác nhận lần gửi này. Hãy tải lại trang và thử lại.',
        ];
    }
}

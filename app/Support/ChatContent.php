<?php

namespace App\Support;

final class ChatContent
{
    public static function rules(): array
    {
        return ['required', 'string', 'max:2000', 'not_regex:/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/'];
    }

    public static function messages(string $field): array
    {
        return [
            $field.'.required' => 'Nhập nội dung tin nhắn trước khi gửi.',
            $field.'.max' => 'Tin nhắn tối đa 2.000 ký tự. Hãy chia thành các tin ngắn hơn.',
            $field.'.not_regex' => 'Tin nhắn có ký tự không hợp lệ. Hãy kiểm tra lại nội dung.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PosLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return ['identifier' => 'email hoặc số điện thoại', 'password' => 'mật khẩu'];
    }
}

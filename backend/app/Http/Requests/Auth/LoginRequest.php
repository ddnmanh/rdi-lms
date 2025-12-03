<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép request này được sử dụng
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email:rfc'],
            'password' => ['required', 'string', 'min:8']
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email là bắt buộc.',
            'email.email'    => 'Email không đúng định dạng.',
            'email.unique'   => 'Email đã được sử dụng.',

            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min'      => 'Mật khẩu phải có ít nhất :min ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email'    => 'Email',
            'password' => 'Mật khẩu'
        ];
    }

    /**
     * Custom JSON trả về khi validate fail
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Dữ liệu gửi lên không hợp lệ.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}

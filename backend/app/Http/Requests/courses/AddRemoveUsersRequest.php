<?php

namespace App\Http\Requests\courses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddRemoveUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép request này được sử dụng
    }

    public function rules(): array
    {
        return [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'user_ids.required' => 'Danh sách sinh viên là bắt buộc.',
            'user_ids.array' => 'Danh sách sinh viên phải là một mảng.',
            'user_ids.*.exists' => 'Sinh viên không tồn tại.',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_ids' => 'Danh sách sinh viên',
            'user_ids.*' => 'Sinh viên',
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

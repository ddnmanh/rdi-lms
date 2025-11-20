<?php

namespace App\Http\Requests\courses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép request này được sử dụng
    }

    public function rules(): array
    {
        return [
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ];
    }

    public function messages(): array
    {
        return [
            'course_ids.required' => 'Danh sách khóa học là bắt buộc.',
            'course_ids.array' => 'Danh sách khóa học phải là một mảng.',
            'course_ids.*.exists' => 'Khóa học không tồn tại.',
        ];
    }

    public function attributes(): array
    {
        return [
            'course_ids' => 'Danh sách khóa học',
            'course_ids.*' => 'Khóa học',
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

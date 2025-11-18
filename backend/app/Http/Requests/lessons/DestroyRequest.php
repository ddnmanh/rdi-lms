<?php

namespace App\Http\Requests\lessons;

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
            'lesson_ids' => 'required|array',
            'lesson_ids.*' => 'exists:lessons,id',
        ];
    }

    public function messages(): array
    {
        return [
            'lesson_ids.required' => 'Danh sách bài học là bắt buộc.',
            'lesson_ids.array' => 'Danh sách bài học phải là một mảng.',
            'lesson_ids.*.exists' => 'Khóa học không tồn tại.',
        ];
    }

    public function attributes(): array
    {
        return [
            'lesson_ids' => 'Danh sách bài học',
            'lesson_ids.*' => 'Khóa học',
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

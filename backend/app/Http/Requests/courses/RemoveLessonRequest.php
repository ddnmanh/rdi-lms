<?php

namespace App\Http\Requests\courses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RemoveLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép request này được sử dụng
    }

    public function rules(): array
    {
        return [
            'lesson_ids' => 'required|exists:lessons,id',
        ];
    }

    public function messages(): array
    {
        return [
            'lesson_ids.required' => 'ID bài học là bắt buộc.',
            'lesson_ids.exists' => 'Bài học không tồn tại trong hệ thống.',
        ];
    }

    public function attributes(): array
    {
        return [
            'lesson_ids' => 'ID bài học',
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

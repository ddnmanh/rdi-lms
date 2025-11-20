<?php

namespace App\Http\Requests\courses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép request này được sử dụng
    }

    public function rules(): array
    {
        return [
            'lessons'                 => 'required|array',
            'lessons.*.id'            => 'required|integer|exists:lessons,id|distinct',
            'lessons.*.display_order' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'lessons.required' => 'Danh sách bài học là bắt buộc.',
            'lessons.array' => 'Danh sách bài học phải là một mảng.',
            'lessons.*.id.required' => 'ID bài học là bắt buộc.',
            'lessons.*.id.integer' => 'ID bài học phải là một số nguyên.',
            'lessons.*.id.exists' => 'Bài học không tồn tại.',
            'lessons.*.id.distinct' => 'ID bài học phải là duy nhất.',
            'lessons.*.display_order.required' => 'Thứ tự hiển thị là bắt buộc.',
            'lessons.*.display_order.integer' => 'Thứ tự hiển thị phải là một số nguyên.',
        ];
    }

    public function attributes(): array
    {
        return [
            'lessons' => 'Danh sách bài học',
            'lessons.*.id' => 'ID bài học',
            'lessons.*.display_order' => 'Thứ tự hiển thị',
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

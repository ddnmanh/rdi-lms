<?php

namespace App\Http\Requests\lessonQuizzes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'passing_percent_score' => 'nullable|integer|min:0|max:100',
            'is_required' => 'nullable|boolean',
            'start_at_seconds' => 'nullable|integer|min:0',
            'max_questions' => 'nullable|integer|min:1|max:10',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'Tiêu đề không được vượt quá :max ký tự.',
            'description.max' => 'Mô tả không được vượt quá :max ký tự.',
            'passing_percent_score.min' => 'Điểm đạt tối thiểu phải từ 0.',
            'passing_percent_score.max' => 'Điểm đạt tối thiểu không được vượt quá 100.',
            'start_at_seconds.min' => 'Thời điểm dừng video phải từ 0 giây.',
            'max_questions.min' => 'Số câu hỏi tối đa phải ít nhất 1.',
            'max_questions.max' => 'Số câu hỏi tối đa không được vượt quá 10.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Dữ liệu gửi lên không hợp lệ.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}


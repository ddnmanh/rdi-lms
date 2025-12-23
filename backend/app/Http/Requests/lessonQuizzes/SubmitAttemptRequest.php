<?php

namespace App\Http\Requests\lessonQuizzes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubmitAttemptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|integer|exists:lesson_quiz_questions,id',
            'answers.*.selected_option_ids' => 'required|array|min:1',
            'answers.*.selected_option_ids.*' => 'required|integer|exists:lesson_quiz_options,id',
        ];
    }

    public function messages(): array
    {
        return [
            'answers.required' => 'Phải có ít nhất một câu trả lời.',
            'answers.min' => 'Phải có ít nhất một câu trả lời.',

            'answers.*.question_id.required' => 'ID câu hỏi là bắt buộc.',
            'answers.*.question_id.exists' => 'Câu hỏi không tồn tại.',

            'answers.*.selected_option_ids.required' => 'Phải chọn ít nhất một đáp án.',
            'answers.*.selected_option_ids.min' => 'Phải chọn ít nhất một đáp án.',

            'answers.*.selected_option_ids.*.exists' => 'Đáp án không tồn tại.',
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


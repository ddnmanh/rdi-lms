<?php

namespace App\Http\Requests\lessonQuizzes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_text' => 'nullable|string|max:1000',
            'question_type' => 'nullable|in:single_choice,multiple_choice',
            'points' => 'nullable|integer|min:1|max:100',
            'display_order' => 'nullable|integer|min:0',
            'explanation' => 'nullable|string|max:1000',

            // Options (nếu có thì cập nhật toàn bộ options)
            'options' => 'nullable|array|min:2|max:6',
            'options.*.id' => 'nullable|integer|exists:lesson_quiz_options,id',
            'options.*.option_text' => 'required_with:options|string|max:500',
            'options.*.is_correct' => 'required_with:options|boolean',
            'options.*.display_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'question_text.max' => 'Nội dung câu hỏi không được vượt quá :max ký tự.',
            'question_type.in' => 'Loại câu hỏi phải là single_choice hoặc multiple_choice.',
            'points.min' => 'Điểm phải ít nhất 1.',
            'points.max' => 'Điểm không được vượt quá 100.',

            'options.min' => 'Phải có ít nhất 2 đáp án.',
            'options.max' => 'Chỉ được tối đa 6 đáp án.',

            'options.*.option_text.required_with' => 'Nội dung đáp án là bắt buộc.',
            'options.*.option_text.max' => 'Nội dung đáp án không được vượt quá :max ký tự.',

            'options.*.is_correct.required_with' => 'Phải chỉ định đáp án đúng hay sai.',
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


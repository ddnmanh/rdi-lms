<?php

namespace App\Http\Requests\lessonQuizzes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lesson_id' => 'required|integer|exists:lessons,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'passing_percent_score' => 'nullable|integer|min:0|max:100',
            'is_required' => 'nullable|boolean',
            'start_at_seconds' => 'nullable|integer|min:0',
            'max_questions' => 'nullable|integer|min:1|max:10',
            'is_active' => 'nullable|boolean',

            // Questions array (optional - có thể tạo quiz rồi thêm câu hỏi sau)
            'questions' => 'nullable|array|max:10',
            'questions.*.question_text' => 'required_with:questions|string|max:1000',
            'questions.*.question_type' => 'required_with:questions|in:single_choice,multiple_choice',
            'questions.*.points' => 'nullable|integer|min:1|max:100',
            'questions.*.display_order' => 'nullable|integer|min:0',
            'questions.*.explanation' => 'nullable|string|max:1000',

            // Options for each question
            'questions.*.options' => 'required_with:questions|array|min:2|max:6',
            'questions.*.options.*.option_text' => 'required|string|max:500',
            'questions.*.options.*.is_correct' => 'required|boolean',
            'questions.*.options.*.display_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'lesson_id.required' => 'Bài học là bắt buộc.',
            'lesson_id.exists' => 'Bài học không tồn tại.',

            'title.max' => 'Tiêu đề không được vượt quá :max ký tự.',
            'description.max' => 'Mô tả không được vượt quá :max ký tự.',

            'passing_percent_score.min' => 'Điểm đạt tối thiểu phải từ 0.',
            'passing_percent_score.max' => 'Điểm đạt tối thiểu không được vượt quá 100.',

            'start_at_seconds.min' => 'Thời điểm dừng video phải từ 0 giây.',

            'max_questions.min' => 'Số câu hỏi tối đa phải ít nhất 1.',
            'max_questions.max' => 'Số câu hỏi tối đa không được vượt quá 10.',

            'questions.max' => 'Quiz chỉ được tối đa 10 câu hỏi.',

            'questions.*.question_text.required_with' => 'Nội dung câu hỏi là bắt buộc.',
            'questions.*.question_text.max' => 'Nội dung câu hỏi không được vượt quá :max ký tự.',

            'questions.*.question_type.required_with' => 'Loại câu hỏi là bắt buộc.',
            'questions.*.question_type.in' => 'Loại câu hỏi phải là single_choice hoặc multiple_choice.',

            'questions.*.options.required_with' => 'Mỗi câu hỏi phải có ít nhất 2 đáp án.',
            'questions.*.options.min' => 'Mỗi câu hỏi phải có ít nhất 2 đáp án.',
            'questions.*.options.max' => 'Mỗi câu hỏi chỉ được tối đa 6 đáp án.',

            'questions.*.options.*.option_text.required' => 'Nội dung đáp án là bắt buộc.',
            'questions.*.options.*.option_text.max' => 'Nội dung đáp án không được vượt quá :max ký tự.',

            'questions.*.options.*.is_correct.required' => 'Phải chỉ định đáp án đúng hay sai.',
        ];
    }

    public function attributes(): array
    {
        return [
            'lesson_id' => 'Bài học',
            'title' => 'Tiêu đề',
            'description' => 'Mô tả',
            'passing_percent_score' => 'Điểm đạt tối thiểu',
            'is_required' => 'Bắt buộc',
            'start_at_seconds' => 'Thời điểm dừng video',
            'max_questions' => 'Số câu hỏi tối đa',
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


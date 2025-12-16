<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'lesson_id' => 'required|integer|exists:lessons,id',
            'duration_at' => 'required|integer|min:0',
            'content' => 'required|string|max:10000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'lesson_id.required' => 'Vui lòng chọn bài học',
            'lesson_id.integer' => 'ID bài học không hợp lệ',
            'lesson_id.exists' => 'Bài học không tồn tại',
            'duration_at.required' => 'Vui lòng nhập thời điểm trong video',
            'duration_at.integer' => 'Thời điểm phải là số nguyên',
            'duration_at.min' => 'Thời điểm không thể nhỏ hơn 0',
            'content.required' => 'Vui lòng nhập nội dung ghi chú',
            'content.string' => 'Nội dung ghi chú không hợp lệ',
            'content.max' => 'Nội dung ghi chú không được vượt quá 10000 ký tự',
        ];
    }
}


<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
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
            'duration_at' => 'sometimes|integer|min:0',
            'content' => 'sometimes|string|max:10000',
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
            'duration_at.integer' => 'Thời điểm phải là số nguyên',
            'duration_at.min' => 'Thời điểm không thể nhỏ hơn 0',
            'content.string' => 'Nội dung ghi chú không hợp lệ',
            'content.max' => 'Nội dung ghi chú không được vượt quá 10000 ký tự',
        ];
    }
}


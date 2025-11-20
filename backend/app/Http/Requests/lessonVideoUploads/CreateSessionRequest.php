<?php

namespace App\Http\Requests\lessonVideoUploads;

use Illuminate\Foundation\Http\FormRequest;

class CreateSessionRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'lesson_id' => 'nullable|exists:lessons,id',
            'file_name' => 'required|string|max:255',
            'file_size' => 'required|integer|min:1|max:10995116277760', // 10GB
            'mime_type' => 'nullable|string|max:255',
            'chunk_size' => 'nullable|integer|min:262144|max:524288000', // 256KB - 500MB
        ];
    }
}

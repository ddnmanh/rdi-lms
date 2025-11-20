<?php

namespace App\Http\Requests\lessonVideoUploads;

use Illuminate\Foundation\Http\FormRequest;

class UploadChunkRequest extends FormRequest
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
            'chunk_index' => 'required|integer|min:1',
            'chunk' => 'required|file',
        ];
    }
}

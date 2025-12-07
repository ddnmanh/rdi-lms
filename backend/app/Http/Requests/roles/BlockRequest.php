<?php

namespace App\Http\Requests\roles;

use Illuminate\Foundation\Http\FormRequest;

class BlockRequest extends FormRequest
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
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'required|integer|exists:roles,id',
            'is_block' => 'required|boolean',
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
            'role_ids.required' => 'Danh sách ID role là bắt buộc',
            'role_ids.array' => 'Danh sách ID role phải là một mảng',
            'role_ids.min' => 'Phải có ít nhất 1 role',
            'role_ids.*.required' => 'ID role không được để trống',
            'role_ids.*.integer' => 'ID role phải là số nguyên',
            'role_ids.*.exists' => 'Role với ID này không tồn tại',
            'is_block.required' => 'Trạng thái khóa là bắt buộc',
            'is_block.boolean' => 'Trạng thái khóa phải là true hoặc false',
        ];
    }
}


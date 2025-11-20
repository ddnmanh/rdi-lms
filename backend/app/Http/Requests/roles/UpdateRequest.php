<?php

namespace App\Http\Requests\roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép request này được sử dụng
    }

    public function rules(): array
    {

        return [
            'name' => 'required|string|max:50|unique:roles,name,' . $this->route('id'),
            'description' => 'nullable|string|max:255',
            'level' => 'required|integer|min:1|max:255',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên vai trò không được để trống.',
            'name.string' => 'Tên vai trò phải là chuỗi ký tự.',
            'name.max' => 'Tên vai trò không được vượt quá 50 ký tự.',
            'name.unique' => 'Tên vai trò đã tồn tại.',

            'description.string' => 'Mô tả vai trò phải là chuỗi ký tự.',
            'description.max' => 'Mô tả vai trò không được vượt quá 255 ký tự.',

            'level.required' => 'Cấp độ vai trò không được để trống.',
            'level.integer' => 'Cấp độ vai trò phải là số nguyên.',
            'level.min' => 'Cấp độ vai trò phải lớn hơn hoặc bằng 1.',
            'level.max' => 'Cấp độ vai trò không được vượt quá 255.',

            'permission_ids.array' => 'Danh sách quyền phải là một mảng.',
            'permission_ids.*.exists' => 'Quyền không tồn tại trong hệ thống.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên vai trò',
            'description' => 'Mô tả vai trò',
            'level' => 'Cấp độ vai trò',
            'permission_ids' => 'Danh sách quyền',
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

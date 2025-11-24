<?php

namespace App\Http\Requests\roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép request này được sử dụng
    }

    /**
     * Get the validated data from the request.
     */
    public function validationData()
    {
        // Ensure we get data from JSON body if content-type is application/json
        return $this->all();
    }

    /**
     * Prepare data for validation
     */
    protected function prepareForValidation()
    {
        // Trim whitespace from name if it exists
        if ($this->has('name') && is_string($this->input('name'))) {
            $this->merge([
                'name' => trim($this->input('name'))
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'level' => 'required|integer|min:1|max:255',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên không được để trống.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá :max ký tự.',
            'name.unique' => 'Tên đã tồn tại.',

            'description.string' => 'Mô tả phải là chuỗi ký tự.',
            'description.max' => 'Mô tả không được vượt quá :max ký tự.',

            'level.required' => 'Cấp độ không được để trống.',
            'level.integer' => 'Cấp độ phải là số nguyên.',
            'level.min' => 'Cấp độ phải lớn hơn hoặc bằng :min.',
            'level.max' => 'Cấp độ phải nhỏ hơn hoặc bằng :max.',

            'permission_ids.array' => 'Danh sách quyền phải là một mảng.',
            'permission_ids.*.exists' => 'Quyền không tồn tại trong hệ thống.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên',
            'description' => 'Mô tả',
            'level' => 'Cấp độ',
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

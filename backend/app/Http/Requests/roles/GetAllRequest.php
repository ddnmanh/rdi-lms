<?php

namespace App\Http\Requests\roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetAllRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép request này được sử dụng
    }

    public function rules(): array
    {
        return [
            'search'        => ['nullable', 'string', 'max:255'],
            'level_from'    => ['nullable', 'integer', 'min:1', 'max:255'],
            'level_to'      => ['nullable', 'integer', 'min:1', 'max:255'],
            'user_id'       => ['nullable', 'integer', 'exists:users,id'],
            'permission_id' => ['nullable', 'integer', 'exists:permissions,id'],
            'is_block'      => ['nullable', 'boolean'],
            'sort_by'       => ['nullable', 'string', 'in:id,name,level,created_at,updated_at'],
            'order_by'      => ['nullable', 'string', 'in:asc,desc'],
            'per_page'      => ['nullable', 'integer', 'min:1', 'max:1000'],
            'page'          => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'search.string'        => 'Từ khóa tìm kiếm phải là chuỗi ký tự.',
            'search.max'           => 'Từ khóa tìm kiếm không được vượt quá :max ký tự.',

            'level_from.integer'   => 'Level từ phải là số nguyên.',
            'level_from.min'       => 'Level từ phải lớn hơn hoặc bằng :min.',
            'level_from.max'       => 'Level từ phải nhỏ hơn hoặc bằng :max.',
            'level_to.integer'     => 'Level đến phải là số nguyên.',
            'level_to.min'         => 'Level đến phải lớn hơn hoặc bằng :min.',
            'level_to.max'         => 'Level đến phải nhỏ hơn hoặc bằng :max.',

            'user_id.integer'      => 'User ID phải là số nguyên.',
            'user_id.exists'       => 'User ID không tồn tại.',

            'permission_id.integer'=> 'Permission ID phải là số nguyên.',
            'permission_id.exists' => 'Permission ID không tồn tại.',

            'is_block.boolean'     => 'Trạng thái khóa phải là true hoặc false.',

            'sort_by.string'       => 'Trường sắp xếp phải là chuỗi ký tự.',
            'sort_by.in'           => 'Trường sắp xếp không hợp lệ.',

            'order_by.string'      => 'Thứ tự sắp xếp phải là chuỗi ký tự.',
            'order_by.in'          => 'Thứ tự sắp xếp không hợp lệ.',

            'per_page.integer'     => 'Số lượng mỗi trang phải là số nguyên.',
            'per_page.min'         => 'Số lượng mỗi trang phải lớn hơn hoặc bằng :min.',
            'per_page.max'         => 'Số lượng mỗi trang phải nhỏ hơn hoặc bằng :max.',

            'page.integer'         => 'Số trang phải là số nguyên.',
            'page.min'             => 'Số trang phải lớn hơn hoặc bằng :min.',
        ];
    }

    public function attributes(): array
    {
        return [
            'search'        => 'từ khóa tìm kiếm',
            'level_from'    => 'level từ',
            'level_to'      => 'level đến',
            'user_id'       => 'user ID',
            'permission_id' => 'permission ID',
            'is_block'      => 'trạng thái khóa',
            'sort_by'       => 'trường sắp xếp',
            'order_by'      => 'thứ tự sắp xếp',
            'per_page'      => 'số lượng mỗi trang',
            'page'          => 'số trang',
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

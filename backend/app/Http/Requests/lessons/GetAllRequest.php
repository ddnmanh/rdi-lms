<?php

namespace App\Http\Requests\lessons;

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
            'orphaned'       => ['nullable', 'boolean'],
            'course_id'      => ['nullable', 'integer', 'exists:courses,id'],
            'search'         => ['nullable', 'string', 'max:255'],
            'duration_min'   => ['nullable', 'integer', 'min:0'],
            'duration_max'   => ['nullable', 'integer', 'min:0'],
            'sort_by'        => ['nullable', 'string', 'in:id,title,duration,course_id,created_at,updated_at,display_order'],
            'order_by'       => ['nullable', 'string', 'in:asc,desc'],
            'per_page'       => ['nullable', 'integer', 'min:1', 'max:100'],
            'page'           => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'orphaned.boolean'           => 'Trạng thái lọc bài học không thuộc về khóa học phải là boolean.',
            'course_id.integer'          => 'ID khóa học phải là số nguyên.',
            'course_id.exists'           => 'Khóa học không tồn tại.',
            'search.string'              => 'Từ khóa tìm kiếm phải là chuỗi.',
            'search.max'                 => 'Từ khóa tìm kiếm không được vượt quá :max ký tự.',
            'duration_min.integer'       => 'Thời lượng tối thiểu phải là số nguyên.',
            'duration_min.min'           => 'Thời lượng tối thiểu phải lớn hơn hoặc bằng 0.',
            'duration_max.integer'       => 'Thời lượng tối đa phải là số nguyên.',
            'duration_max.min'           => 'Thời lượng tối đa phải lớn hơn hoặc bằng 0.',
            'sort_by.in'                 => 'Trường sắp xếp không hợp lệ.',
            'order_by.in'                => 'Thứ tự sắp xếp phải là asc hoặc desc.',
            'per_page.integer'           => 'Số lượng mỗi trang phải là số nguyên.',
            'per_page.min'               => 'Số lượng mỗi trang phải lớn hơn hoặc bằng 1.',
            'per_page.max'               => 'Số lượng mỗi trang không được vượt quá 100.',
            'page.integer'               => 'Số trang phải là số nguyên.',
            'page.min'                   => 'Số trang phải lớn hơn hoặc bằng 1.',
        ];
    }

    public function attributes(): array
    {
        return [
            'orphaned'       => 'Trạng thái lọc bài học không thuộc về khóa học',
            'course_id'      => 'ID khóa học',
            'search'         => 'Từ khóa tìm kiếm',
            'duration_min'   => 'Thời lượng tối thiểu',
            'duration_max'   => 'Thời lượng tối đa',
            'sort_by'         => 'Trường sắp xếp',
            'order_by'        => 'Thứ tự sắp xếp',
            'per_page'        => 'Số lượng mỗi trang',
            'page'            => 'Số trang',
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

    /**
     * Chuẩn bị dữ liệu cho validation
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('orphaned')) {
            $this->merge([
                'orphaned' => filter_var($this->orphaned, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}

<?php

namespace App\Http\Requests\courses;

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
            'search'         => ['nullable', 'string', 'max:255'],
            'user_id'        => ['nullable', 'integer', 'exists:users,id'],
            'start_date_from' => ['nullable', 'date', 'date_format:Y-m-d'],
            'start_date_to'   => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date_from'],
            'end_date_from'   => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_date_to'     => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:end_date_from'],
            'date_range'      => ['nullable', 'string', 'in:active,upcoming,past,all'],
            'min_users'       => ['nullable', 'integer', 'min:0'],
            'max_users'       => ['nullable', 'integer', 'min:0', 'gte:min_users'],
            'min_lessons'     => ['nullable', 'integer', 'min:0'],
            'max_lessons'     => ['nullable', 'integer', 'min:0', 'gte:min_lessons'],
            'sort_by'         => ['nullable', 'string', 'in:id,title,start_date,end_date,created_at,updated_at,users_count,lessons_count'],
            'order_by'        => ['nullable', 'string', 'in:asc,desc'],
            'per_page'        => ['nullable', 'integer', 'min:1', 'max:100'],
            'page'            => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'search.string'              => 'Từ khóa tìm kiếm phải là chuỗi.',
            'search.max'                 => 'Từ khóa tìm kiếm không được vượt quá :max ký tự.',

            'user_id.integer'            => 'ID người dùng phải là số nguyên.',
            'user_id.exists'             => 'Người dùng không tồn tại.',

            'start_date_from.date'       => 'Ngày bắt đầu (từ) không đúng định dạng.',
            'start_date_from.date_format' => 'Ngày bắt đầu (từ) phải có định dạng Y-m-d.',
            'start_date_to.date'         => 'Ngày bắt đầu (đến) không đúng định dạng.',
            'start_date_to.date_format'  => 'Ngày bắt đầu (đến) phải có định dạng Y-m-d.',
            'start_date_to.after_or_equal' => 'Ngày bắt đầu (đến) phải lớn hơn hoặc bằng ngày bắt đầu (từ).',

            'end_date_from.date'         => 'Ngày kết thúc (từ) không đúng định dạng.',
            'end_date_from.date_format'  => 'Ngày kết thúc (từ) phải có định dạng Y-m-d.',
            'end_date_to.date'           => 'Ngày kết thúc (đến) không đúng định dạng.',
            'end_date_to.date_format'    => 'Ngày kết thúc (đến) phải có định dạng Y-m-d.',
            'end_date_to.after_or_equal' => 'Ngày kết thúc (đến) phải lớn hơn hoặc bằng ngày kết thúc (từ).',

            'date_range.in'              => 'Khoảng thời gian phải là một trong các giá trị: active, upcoming, past, all.',

            'min_users.integer'          => 'Số lượng người dùng tối thiểu phải là số nguyên.',
            'min_users.min'              => 'Số lượng người dùng tối thiểu phải lớn hơn hoặc bằng 0.',
            'max_users.integer'          => 'Số lượng người dùng tối đa phải là số nguyên.',
            'max_users.min'              => 'Số lượng người dùng tối đa phải lớn hơn hoặc bằng 0.',
            'max_users.gte'              => 'Số lượng người dùng tối đa phải lớn hơn hoặc bằng số lượng tối thiểu.',

            'min_lessons.integer'        => 'Số lượng bài học tối thiểu phải là số nguyên.',
            'min_lessons.min'            => 'Số lượng bài học tối thiểu phải lớn hơn hoặc bằng 0.',
            'max_lessons.integer'        => 'Số lượng bài học tối đa phải là số nguyên.',
            'max_lessons.min'            => 'Số lượng bài học tối đa phải lớn hơn hoặc bằng 0.',
            'max_lessons.gte'            => 'Số lượng bài học tối đa phải lớn hơn hoặc bằng số lượng tối thiểu.',

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
            'search'         => 'Từ khóa tìm kiếm',
            'user_id'        => 'ID người dùng',
            'start_date_from' => 'Ngày bắt đầu (từ)',
            'start_date_to'   => 'Ngày bắt đầu (đến)',
            'end_date_from'   => 'Ngày kết thúc (từ)',
            'end_date_to'     => 'Ngày kết thúc (đến)',
            'date_range'      => 'Khoảng thời gian',
            'min_users'       => 'Số lượng người dùng tối thiểu',
            'max_users'       => 'Số lượng người dùng tối đa',
            'min_lessons'     => 'Số lượng bài học tối thiểu',
            'max_lessons'     => 'Số lượng bài học tối đa',
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
}

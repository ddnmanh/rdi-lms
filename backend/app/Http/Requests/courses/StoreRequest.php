<?php

namespace App\Http\Requests\courses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép request này được sử dụng
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'timezone' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'            => 'Tiêu đề là bắt buộc.',
            'title.string'              => 'Tiêu đề phải là chuỗi.',
            'title.max'                 => 'Tiêu đề không được vượt quá :max ký tự',

            'description.string'      => 'Mô tả phải là chuỗi.',

            'start_date.date'         => 'Ngày bắt đầu không đúng định dạng.',

            'end_date.date'           => 'Ngày kết thúc không đúng định dạng.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',

            'timezone.string'         => 'Múi giờ phải là chuỗi.',

            'thumbnail.image'         => 'Ảnh đại diện phải là một tệp hình ảnh.',
            'thumbnail.mimes'         => 'Ảnh đại diện phải có định dạng: :values.',
            'thumbnail.max'           => 'Ảnh đại diện không được vượt quá :max kilobytes.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title'       => 'Tiêu đề',
            'description' => 'Mô tả',
            'start_date'  => 'Ngày bắt đầu',
            'end_date'    => 'Ngày kết thúc',
            'timezone'    => 'Múi giờ',
            'thumbnail'   => 'Ảnh đại diện',
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

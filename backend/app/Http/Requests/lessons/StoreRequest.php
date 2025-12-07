<?php

namespace App\Http\Requests\lessons;

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
        $rules = [
            'title' => 'required|string|max:255',
            // 'course_id' => 'nullable|exists:courses,id',
            'description' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
            'video_path' => 'nullable|string', // Đường dẫn ackground-upload://{videoUploadId}, lưu tạm chờ video upload ở route khác xong sẽ tự động cập nhật lại
            // 'duration' => 'nullable|integer|min:0',
            // 'display_order' => 'nullable|integer|min:0', 
        ];



        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required'            => 'Tiêu đề là bắt buộc.',
            'title.string'              => 'Tiêu đề phải là chuỗi.',
            'title.max'                 => 'Tiêu đề không được vượt quá :max ký tự',

            // 'course_id.exists'        => 'Khóa học không tồn tại.',

            'description.string'      => 'Mô tả phải là chuỗi.',
            'description.max'         => 'Mô tả không được vượt quá :max ký tự',

            // 'duration.required'       => 'Thời lượng là bắt buộc.',
            // 'duration.integer'        => 'Thời lượng phải là số nguyên.',
            // 'duration.min'            => 'Thời lượng phải lớn hơn hoặc bằng 0.',

            'video_path.string'        => 'Đường dẫn video phải là chuỗi.', 

            'thumbnail.image'         => 'Ảnh bìa phải là một tệp hình ảnh.',
            'thumbnail.mimes'         => 'Ảnh bìa phải có định dạng: :values.',
            'thumbnail.max'           => 'Ảnh bìa không được vượt quá :max kilobytes.',

            // 'display_order.integer'   => 'Thứ tự hiển thị phải là số nguyên.',
            // 'display_order.min'       => 'Thứ tự hiển thị phải lớn hơn hoặc bằng 0.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title'       => 'Tiêu đề',
            'description' => 'Mô tả',
            // 'course_id'   => 'Thuộc khóa học',
            // 'duration'    => 'Thời lượng',
            'video_path'   => 'Đường dẫn video',
            'thumbnail'   => 'Ảnh bìa',
            // 'display_order' => 'Thứ tự hiển thị',
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

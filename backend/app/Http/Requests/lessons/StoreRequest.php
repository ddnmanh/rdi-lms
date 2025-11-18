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
        return [
            'title' => 'required|string|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'description' => 'nullable|string|max:255',
            'thumbnail_path' => 'nullable|string',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'video_path' => 'nullable|string',
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,webm|max:460800', // 450MB
            'duration' => 'nullable|integer|min:0',
            'display_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'            => 'Tiêu đề là bắt buộc.',
            'title.string'              => 'Tiêu đề phải là chuỗi.',
            'title.max'                 => 'Tiêu đề không được vượt quá :max ký tự',

            'course_id.exists'        => 'Khóa học không tồn tại.',

            'description.string'      => 'Mô tả phải là chuỗi.',
            'description.max'         => 'Mô tả không được vượt quá :max ký tự',

            'duration.required'       => 'Thời lượng là bắt buộc.',
            'duration.integer'        => 'Thời lượng phải là số nguyên.',
            'duration.min'            => 'Thời lượng phải lớn hơn hoặc bằng 0.',

            'video_path.string'        => 'Đường dẫn video phải là chuỗi.',

            'video.file'              => 'Video phải là một tệp hình ảnh.',
            'video.mimes'             => 'Video phải có định dạng: :values.',
            'video.max'               => 'Video không được vượt quá :max kilobytes.',

            'thumbnail.image'         => 'Ảnh đại diện phải là một tệp hình ảnh.',
            'thumbnail.mimes'         => 'Ảnh đại diện phải có định dạng: :values.',
            'thumbnail.max'           => 'Ảnh đại diện không được vượt quá :max kilobytes.',

            'display_order.integer'   => 'Thứ tự hiển thị phải là số nguyên.',
            'display_order.min'       => 'Thứ tự hiển thị phải lớn hơn hoặc bằng 0.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title'       => 'Tiêu đề',
            'description' => 'Mô tả',
            'course_id'   => 'Thuộc khóa học',
            'duration'    => 'Thời lượng',
            'video_path'   => 'Đường dẫn video',
            'video_file'       => 'Video file',
            'thumbnail_file'   => 'Ảnh đại diện',
            'display_order' => 'Thứ tự hiển thị',
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

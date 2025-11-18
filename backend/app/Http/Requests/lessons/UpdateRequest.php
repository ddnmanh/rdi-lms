<?php

namespace App\Http\Requests\lessons;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép request này được sử dụng
    }

    public function rules(): array
    {

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'thumbnail_path' => 'nullable|string',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'video_path' => 'nullable|string',
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,webm|max:460800', // 450MB
            'duration' => 'required|integer|min:0',
            'display_order' => 'nullable|integer|min:0',
        ];

        // Kiểm tra xem có file thumbnail trong request không
        // Có thể được set từ middleware HandlePutFormData
        $hasThumbnail = $this->hasFile('thumbnail_file') || ($this->files->has('thumbnail_file') && $this->files->get('thumbnail_file') !== null);

        // Chỉ validate thumbnail nếu có file được upload
        if ($hasThumbnail) {
            // Validate file với custom rule (không dùng rule 'required' vì nó sẽ check isValid())
            $rules['thumbnail_file'] = [
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        $fail('Ảnh đại diện là bắt buộc.');
                        return;
                    }

                    // Kiểm tra xem có phải là UploadedFile không
                    // Có thể là Illuminate\Http\UploadedFile hoặc Symfony\Component\HttpFoundation\File\UploadedFile
                    if (!($value instanceof \Illuminate\Http\UploadedFile) &&
                        !($value instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
                        $fail('Ảnh đại diện không hợp lệ.');
                        return;
                    }

                    // Kiểm tra file có tồn tại không
                    if (!file_exists($value->getPathname())) {
                        $fail('Ảnh đại diện không hợp lệ.');
                        return;
                    }

                    // Kiểm tra MIME type
                    $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                    $mimeType = $value->getMimeType();
                    if (!in_array($mimeType, $allowedMimes)) {
                        $fail('Ảnh đại diện phải có định dạng: jpeg, png, jpg, gif, webp.');
                        return;
                    }

                    // Kiểm tra kích thước (max 2048 KB = 2MB)
                    $sizeInKB = $value->getSize() / 1024;
                    if ($sizeInKB > 2048) {
                        $fail('Ảnh đại diện không được vượt quá 2048 kilobytes.');
                        return;
                    }
                },
            ];
        } else {
            $rules['thumbnail_file'] = 'nullable';
        }


        // Kiểm tra xem có file video trong request không
        // Có thể được set từ middleware HandlePutFormData
        $hasVideo = $this->hasFile('video_file') || ($this->files->has('video_file') && $this->files->get('video_file') !== null);

        if ($hasVideo) {
            // Validate file với custom rule (không dùng rule 'required' vì nó sẽ check isValid())
            $rules['video_file'] = [
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        $fail('Video là bắt buộc.');
                        return;
                    }

                    // Kiểm tra xem có phải là UploadedFile không
                    // Có thể là Illuminate\Http\UploadedFile hoặc Symfony\Component\HttpFoundation\File\UploadedFile
                    if (!($value instanceof \Illuminate\Http\UploadedFile) &&
                        !($value instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
                        $fail('Video không hợp lệ.');
                        return;
                    }

                    // Kiểm tra file có tồn tại không
                    if (!file_exists($value->getPathname())) {
                        $fail('Video không hợp lệ.');
                        return;
                    }

                    // Kiểm tra MIME type
                    $allowedMimes = ['video/mp4', 'video/avi', 'video/mov', 'video/webm'];
                    $mimeType = $value->getMimeType();
                    if (!in_array($mimeType, $allowedMimes)) {
                        $fail('Video phải có định dạng: mp4, avi, mov, webm.');
                        return;
                    }

                    // Kiểm tra kích thước (max 460800 KB = 450MB)
                    $sizeInKB = $value->getSize() / 1024;
                    if ($sizeInKB > 460800) {
                        $fail('Video không được vượt quá 460800 kilobytes (450MB).');
                        return;
                    }
                },
            ];
        } else {
            $rules['video_file'] = 'nullable';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'course_id.exists'        => 'Khóa học không tồn tại.',

            'title.required'            => 'Tiêu đề là bắt buộc.',
            'title.string'              => 'Tiêu đề phải là chuỗi.',
            'title.max'                 => 'Tiêu đề không được vượt quá :max ký tự',

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
            'video_file'       => 'Video_file',
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

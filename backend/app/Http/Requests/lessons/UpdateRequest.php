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
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
            'video_path' => 'nullable|string',
        ];

        // Kiểm tra xem có file thumbnail trong request không
        // Có thể được set từ middleware HandlePutFormData
        $hasThumbnail = $this->hasFile('thumbnail') || ($this->files->has('thumbnail') && $this->files->get('thumbnail') !== null);

        // Chỉ validate thumbnail nếu có file được upload
        if ($hasThumbnail) {
            // Validate file với custom rule (không dùng rule 'required' vì nó sẽ check isValid())
            $rules['thumbnail'] = [
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        $fail('Ảnh bìa là bắt buộc.');
                        return;
                    }

                    // Kiểm tra xem có phải là UploadedFile không
                    // Có thể là Illuminate\Http\UploadedFile hoặc Symfony\Component\HttpFoundation\File\UploadedFile
                    if (!($value instanceof \Illuminate\Http\UploadedFile) &&
                        !($value instanceof \Symfony\Component\HttpFoundation\File\UploadedFile)) {
                        $fail('Ảnh bìa không hợp lệ.');
                        return;
                    }

                    // Kiểm tra file có tồn tại không
                    if (!file_exists($value->getPathname())) {
                        $fail('Ảnh bìa không hợp lệ.');
                        return;
                    }

                    // Kiểm tra MIME type
                    $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                    $mimeType = $value->getMimeType();
                    if (!in_array($mimeType, $allowedMimes)) {
                        $fail('Ảnh bìa phải có định dạng: jpeg, png, jpg, webp.');
                        return;
                    }

                    // Kiểm tra kích thước (max 10240 KB = 10MB)
                    $sizeInBytes = $value->getSize();
                    $maxSizeInBytes = 10 * 1024 * 1024; // 10MB
                    if ($sizeInBytes > $maxSizeInBytes) {
                        $fail('Ảnh bìa không được vượt quá 10MB.');
                        return;
                    }
                },
            ];
        } else {
            $rules['thumbnail'] = 'nullable';
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

            'thumbnail.image'         => 'Ảnh bìa phải là một tệp hình ảnh.',
            'thumbnail.mimes'         => 'Ảnh bìa phải có định dạng: :values.',
            'thumbnail.max'           => 'Ảnh bìa không được vượt quá :max kilobytes.',

            'video_path.string'        => 'Đường dẫn video phải là chuỗi.'
        ];
    }

    public function attributes(): array
    {
        return [
            'title'       => 'Tiêu đề',
            'description' => 'Mô tả',
            'thumbnail'   => 'Ảnh bìa',
            'video_path'   => 'Đường dẫn video',
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

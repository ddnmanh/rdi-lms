<?php

namespace App\Http\Requests\courses;

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

        $rules = [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'timezone' => 'nullable|string',
        ];

        // Kiểm tra xem có file thumbnail trong request không
        // Có thể được set từ middleware HandlePutFormData
        $hasThumbnail = $this->hasFile('thumbnail') ||
                       ($this->files->has('thumbnail') && $this->files->get('thumbnail') !== null);

        // Chỉ validate thumbnail nếu có file được upload
        if ($hasThumbnail) {
            // Validate file với custom rule (không dùng rule 'required' vì nó sẽ check isValid())
            $rules['thumbnail'] = [
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

                    // Kiểm tra kích thước (max 10240 KB = 10MB)
                    $sizeInKB = $value->getSize() / 1024;
                    if ($sizeInKB > 10240) {
                        $fail('Ảnh đại diện không được vượt quá 10240 kilobytes.');
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

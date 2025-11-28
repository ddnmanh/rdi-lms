<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'fullname' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
        ];

        // Kiểm tra xem có file avatar trong request không
        // Có thể được set từ middleware HandlePutFormData
        $hasAvatar = $this->hasFile('avatar') ||
                       ($this->files->has('avatar') && $this->files->get('avatar') !== null);

        // Chỉ validate avatar nếu có file được upload
        if ($hasAvatar) {
            // Validate file với custom rule (không dùng rule 'required' vì nó sẽ check isValid())
            $rules['avatar'] = [
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
                    if ($sizeInKB > 4096) {
                        $fail('Ảnh đại diện không được vượt quá 4096 kilobytes.');
                        return;
                    }
                },
            ];
        } else {
            $rules['avatar'] = 'nullable';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'fullname.string' => 'Họ và tên phải là chuỗi ký tự.',
            'fullname.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'birthday.date' => 'Ngày sinh không đúng định dạng ngày tháng.',
            'avatar.image' => 'Ảnh đại diện phải là một file ảnh hợp lệ.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 4096 kilobytes.',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng: jpeg, png, jpg, gif, webp.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'fullname' => 'Họ và tên',
            'birthday' => 'Ngày sinh',
            'avatar'   => 'Ảnh đại diện',
        ];
    }
}

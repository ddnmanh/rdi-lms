<?php

namespace App\Http\Requests\users;

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
            'email' => 'required|email:rfc|unique:users,email',
            'password' => 'required|string|min:6',
            'fullname' => 'required|string|max:150',
            'birthday' => 'nullable|date',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
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

                    // Kiểm tra kích thước (max 10240 KB = 10MB)
                    $sizeInBytes = $value->getSize();
                    $maxSizeInBytes = 10 * 1024 * 1024; // 10MB
                    if ($sizeInBytes > $maxSizeInBytes) {
                        $fail('Ảnh đại diện không được vượt quá 10MB.');
                        return;
                    }
                },
            ];
        } else {
            $rules['avatar'] = 'nullable';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được sử dụng.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.string' => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'fullname.required' => 'Họ và tên là bắt buộc.',
            'fullname.string' => 'Họ và tên phải là chuỗi ký tự.',
            'fullname.max' => 'Họ và tên không được vượt quá :max ký tự.',
            'birthday.date'         => 'Ngày sinh không hợp lệ.',
            'avatar.image'          => 'Ảnh đại diện phải là một tệp hình ảnh.',
            'avatar.mimes'          => 'Ảnh đại diện phải có định dạng: :values.',
            'avatar.max'            => 'Ảnh đại diện không được vượt quá :max kilobytes.',
            'role_ids.array' => 'Danh sách vai trò không hợp lệ.',
            'role_ids.*.exists' => 'Vai trò không tồn tại.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'fullname' => 'Họ và tên',
            'birthday' => 'Ngày sinh',
            'avatar_path' => 'Đường dẫn ảnh đại diện',
            'role_ids' => 'Danh sách vai trò',
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

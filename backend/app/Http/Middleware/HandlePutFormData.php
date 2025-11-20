<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Middleware để xử lý PUT/PATCH request với multipart/form-data
 *
 * Laravel/Symfony không parse multipart/form-data cho PUT/PATCH requests.
 * Middleware này sẽ parse thủ công multipart/form-data từ raw body
 * và merge vào request để controller có thể nhận được dữ liệu
 */
class HandlePutFormData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\JsonResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Chỉ xử lý PUT/PATCH requests
        $method = $request->method();
        if (!in_array($method, ['PUT', 'PATCH'])) {
            return $next($request);
        }

        // Kiểm tra nếu request có Content-Type là multipart/form-data
        $contentType = $request->header('Content-Type', '');
        if (strpos($contentType, 'multipart/form-data') !== false) {
            // Parse multipart/form-data từ raw body
            $parsed = $this->parseMultipartFormData($request);

            // Merge dữ liệu đã parse vào request
            if (!empty($parsed['inputs'])) {
                $request->merge($parsed['inputs']);
            }

            // Thêm files vào request
            if (!empty($parsed['files'])) {
                foreach ($parsed['files'] as $key => $file) {
                    $request->files->set($key, $file);
                }
            }
        }

        return $next($request);
    }

    /**
     * Parse multipart/form-data từ raw body
     *
     * @param Request $request
     * @return array
     */
    private function parseMultipartFormData(Request $request): array
    {
        $files = [];
        $data = [];

        // Lấy raw body
        $rawData = file_get_contents('php://input');

        if (empty($rawData)) {
            return ['inputs' => $data, 'files' => $files];
        }

        // Lấy boundary từ Content-Type header
        $contentType = $request->header('Content-Type', '');
        preg_match('/boundary=(.*)$/i', $contentType, $matches);

        if (empty($matches[1])) {
            return ['inputs' => $data, 'files' => $files];
        }

        $boundary = '--' . trim($matches[1]);

        // Tách các parts
        $parts = explode($boundary, $rawData);

        foreach ($parts as $part) {
            // Bỏ qua phần đầu và phần cuối
            if (empty($part) || $part === "\r\n" || $part === "--\r\n") {
                continue;
            }

            // Tách headers và content
            $part = ltrim($part, "\r\n");
            if (strpos($part, "\r\n\r\n") === false) {
                continue;
            }

            list($rawHeaders, $content) = explode("\r\n\r\n", $part, 2);
            $content = rtrim($content, "\r\n");

            // Parse headers
            $rawHeaders = explode("\r\n", $rawHeaders);
            $headers = [];
            foreach ($rawHeaders as $header) {
                if (strpos($header, ':') === false) {
                    continue;
                }
                list($name, $value) = explode(':', $header, 2);
                $headers[strtolower(trim($name))] = trim($value);
            }

            // Parse content-disposition để lấy field name và filename
            if (isset($headers['content-disposition'])) {
                $fieldName = null;
                $fileName = null;

                preg_match('/name="([^"]+)"/', $headers['content-disposition'], $nameMatches);
                if (isset($nameMatches[1])) {
                    $fieldName = $nameMatches[1];
                }

                preg_match('/filename="([^"]+)"/', $headers['content-disposition'], $fileMatches);
                if (isset($fileMatches[1])) {
                    $fileName = $fileMatches[1];
                }

                if ($fieldName) {
                    if ($fileName !== null) {
                        // Đây là file upload
                        $tmpFile = tempnam(sys_get_temp_dir(), 'laravel_upload_');
                        file_put_contents($tmpFile, $content);

                        // Lấy MIME type từ header hoặc detect từ file content
                        $mimeType = $headers['content-type'] ?? null;
                        if (empty($mimeType) && function_exists('mime_content_type')) {
                            $mimeType = mime_content_type($tmpFile);
                        }
                        if (empty($mimeType) && function_exists('finfo_file')) {
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mimeType = finfo_file($finfo, $tmpFile);
                            finfo_close($finfo);
                        }
                        if (empty($mimeType)) {
                            $mimeType = 'application/octet-stream';
                        }

                        // Tạo UploadedFile với error code = UPLOAD_ERR_OK (0) để Laravel validation nhận diện
                        // Set test mode = false để validation rule 'image' hoạt động đúng
                        $uploadedFile = new UploadedFile(
                            $tmpFile,
                            $fileName,
                            $mimeType,
                            UPLOAD_ERR_OK, // Error code = 0 (no error)
                            false // test mode = false để validation hoạt động đúng
                        );

                        $files[$fieldName] = $uploadedFile;
                    } else {
                        // Đây là field thường
                        $data[$fieldName] = $content;
                    }
                }
            }
        }

        return ['inputs' => $data, 'files' => $files];
    }
}


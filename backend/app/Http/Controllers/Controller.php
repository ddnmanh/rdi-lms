<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Chuyển đổi datetime từ client timezone sang UTC
     *
     * @param string|null $dateTimeString Giá trị datetime từ client (local time)
     * @param string|null $timezone Timezone của client (ví dụ: 'Asia/Ho_Chi_Minh', 'America/New_York')
     * @return string|null Datetime string ở UTC format hoặc null nếu không có giá trị
     */
    protected function convertDateTimeToUTC($dateTimeString, $timezone = null)
    {
        if (!$dateTimeString) {
            return null;
        }

        if (!$timezone) {
            // Nếu không có timezone, giả định là UTC
            try {
                return Carbon::parse($dateTimeString)->utc()->toDateTimeString();
            } catch (\Exception $e) {
                return $dateTimeString; // Giữ nguyên nếu không parse được
            }
        }

        try {
            // Parse datetime với timezone của client, sau đó chuyển sang UTC
            return Carbon::parse($dateTimeString, $timezone)->utc()->toDateTimeString();
        } catch (\Exception $e) {
            // Nếu timezone không hợp lệ hoặc parse lỗi, giữ nguyên giá trị
            return $dateTimeString;
        }
    }

    /**
     * Chuyển đổi nhiều datetime fields từ client timezone sang UTC
     *
     * @param array $data Mảng dữ liệu chứa các datetime fields
     * @param array $dateTimeFields Danh sách các field cần chuyển đổi
     * @param string|null $timezone Timezone của client
     * @return array Mảng dữ liệu đã được chuyển đổi
     */
    protected function convertDateTimesToUTC(array $data, array $dateTimeFields, $timezone = null)
    {
        foreach ($dateTimeFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->convertDateTimeToUTC($data[$field], $timezone);
            }
        }
        return $data;
    }

    /**
    * Tạo slug từ chuỗi tiếng Việt hoặc tiếng Anh
    *
    * @param string $string Chuỗi cần tạo slug
    * @param string $separator Ký tự phân cách (mặc định: '-')
    * @return string Slug đã được tạo
    */
    protected function createSlug($string = '', $separator = '-')
    {
        if (empty($string)) {
            return '';
        }

        // Chuyển về chữ thường
        $string = mb_strtolower($string, 'UTF-8');

        // Bảng chuyển đổi ký tự tiếng Việt có dấu sang không dấu
        $vietnameseMap = [
            'à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
            'đ' => 'd'
        ];

        // Thay thế ký tự tiếng Việt
        $string = strtr($string, $vietnameseMap);

        // Loại bỏ các ký tự đặc biệt, chỉ giữ lại chữ cái, số và khoảng trắng
        $string = preg_replace('/[^a-z0-9\s-]/', '', $string);

        // Thay thế nhiều khoảng trắng hoặc dấu gạch ngang liên tiếp bằng một dấu phân cách
        $string = preg_replace('/[\s-]+/', $separator, $string);

        // Loại bỏ dấu phân cách ở đầu và cuối
        $string = trim($string, $separator);

        return $string;
    }

    /**
     * Hàm xóa file tĩnh khỏi storage
     */
    protected function deleteFileFromStorage($filePath)
    {
        if ($filePath) {
            // Loại bỏ phần '/storage/' để lấy đường dẫn thực tế trong storage/app/public
            $storagePath = str_replace('/storage/', '', $filePath);
            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }
        }
    }
}
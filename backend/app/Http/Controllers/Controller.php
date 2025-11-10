<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;

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
    protected function convertToUTC($dateTimeString, $timezone = null)
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
                $data[$field] = $this->convertToUTC($data[$field], $timezone);
            }
        }
        return $data;
    }
}

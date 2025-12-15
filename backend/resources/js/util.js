function formatSecondsToHHMMSS_Global(totalSeconds, displayUnit = false) {
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    const paddedHours = String(hours).padStart(2, '0');
    const paddedMinutes = String(minutes).padStart(2, '0');
    const paddedSeconds = String(seconds).padStart(2, '0');

    if (displayUnit) {
        return `${paddedHours} giờ ${paddedMinutes} phút ${paddedSeconds} giây`;
    }

    return `${paddedHours}:${paddedMinutes}:${paddedSeconds}`;
}

function removeVietnameseAccentsInString_Global(str) {
    str = str.toLowerCase();
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    str = str.replace(/đ/g, "d");

    // Loại bỏ các ký tự đặc biệt khác nếu cần
    // str = str.replace(/[^0-9a-z ]/g, "");

    return str;
}

function escapeHtml_Global(str) {
    return String(str)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

// ===== Timezone Utilities =====
// Tự động nhận biết timezone của client
function getClientTimezone_Global() {
    try {
        return Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch (e) {
        // Fallback: tính toán offset
        const offset = -new Date().getTimezoneOffset();
        const hours = Math.floor(Math.abs(offset) / 60);
        const minutes = Math.abs(offset) % 60;
        const sign = offset >= 0 ? '+' : '-';
        return `UTC${sign}${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
    }
}

// Chuyển đổi UTC datetime từ Laravel sang local time format cho datetime-local input
// Tự động sử dụng timezone của client
function formatDateTimeLocal(utcDateTimeString) {
    if (!utcDateTimeString) return '';

    // Parse UTC datetime từ Laravel (ISO 8601 với Z hoặc +00:00)
    const date = new Date(utcDateTimeString);
    if (isNaN(date.getTime())) return '';

    // JavaScript Date tự động chuyển UTC sang local time của client
    // Format: YYYY-MM-DDTHH:mm (local time, không có timezone)
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${year}-${month}-${day}T${hours}:${minutes}`;
}

// Format date - hiển thị theo timezone của client
// Laravel lưu và trả về datetime ở UTC, JavaScript tự động chuyển sang local time
function formatDate_Global(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';

    // Laravel trả về datetime ở UTC (có timezone Z hoặc +00:00)
    // JavaScript Date tự động parse và chuyển đổi sang local time của client
    // Sử dụng local time methods để hiển thị đúng theo timezone của client
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${hours}:${minutes} ${day}/${month}/${year}`;
}

// expose to Blade inline scripts
window.formatSecondsToHHMMSS_Global = formatSecondsToHHMMSS_Global;
window.removeVietnameseAccentsInString_Global = removeVietnameseAccentsInString_Global;
window.escapeHtml_Global = escapeHtml_Global;
window.getClientTimezone_Global = getClientTimezone_Global;
window.formatDateTimeLocal = formatDateTimeLocal;
window.formatDate_Global = formatDate_Global;

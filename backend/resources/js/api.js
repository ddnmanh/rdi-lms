
// Hàm lõi: Chỉ lo việc gửi, check lỗi và refresh token
async function baseApiRequest(url, options = {}) {
    // Merge headers mặc định (nếu cần thêm Authorization header chung thì thêm ở đây)
    const headers = {
        'Accept': 'application/json', // Luôn muốn nhận về JSON
        ...options.headers
    };

    try {
        const response = await fetch(`/api`+url, {
            ...options,
            headers,
            credentials: 'include'
        });

        // --- LOGIC REFRESH TOKEN (Giữ nguyên logic của bạn) ---
        if (response.status === 401) {
            try {
                // Gọi API refresh (Lưu ý: API này luôn là JSON)
                const refreshResponse = await fetch(`/api/auth/refresh`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    credentials: 'include'
                });

                if (refreshResponse.ok) {
                    // Token đã sống lại, gọi lại request ban đầu với options cũ
                    return await baseApiRequest(url, options);
                }
            } catch (e) {
                // Nếu không có refresh token hoặc refresh thất bại
                window.location.href = '/admin/login';
                throw new Error('Phiên đăng nhập hết hạn');
            }
        }
        // ------------------------------------------------------

        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || 'Có lỗi xảy ra');
        }
        return data;

    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

/**
 * Dùng cho dữ liệu JSON thuần túy (Object, Array...)
 * @param {string} url - URL endpoint
 * @param {string} method - HTTP method (GET, POST, PUT, DELETE...)
 * @param {Object|null} body - Request body (sẽ được stringify)
 * @param {Object|null} queryParams - Query parameters (sẽ được append vào URL)
 */
async function apiJsonRequest(url, method = 'GET', body = null, queryParams = null) {
    // Xử lý query params
    if (queryParams && typeof queryParams === 'object') {
        const params = new URLSearchParams();
        for (const [key, value] of Object.entries(queryParams)) {
            if (value !== null && value !== undefined && value !== '') {
                params.append(key, value);
            }
        }
        const queryString = params.toString();
        if (queryString) {
            url += (url.includes('?') ? '&' : '?') + queryString;
        }
    }

    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json', // Bắt buộc phải có
        }
    };

    if (body) {
        options.body = JSON.stringify(body); // Tự động stringify
    }

    // Gọi hàm lõi
    return await baseApiRequest(url, options);
}

/**
 * Dùng cho Upload File hoặc FormData
 */
async function apiFormDataRequest(url, method = 'POST', formData) {
    const options = {
        method: method,
        headers: {
            // QUAN TRỌNG: Không được set 'Content-Type' ở đây.
            // Để trình duyệt tự động set thành 'multipart/form-data; boundary=...'
        },
        body: formData // Truyền trực tiếp object FormData
    };

    // Gọi hàm lõi
    return await baseApiRequest(url, options);
}


window.apiJsonRequest = apiJsonRequest;
window.apiFormDataRequest = apiFormDataRequest;

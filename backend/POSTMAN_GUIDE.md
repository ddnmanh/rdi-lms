# Hướng dẫn sử dụng Postman Collection

## Cách import vào Postman

1. Mở Postman
2. Click vào **Import** (góc trên bên trái)
3. Chọn tab **File** hoặc **Link**
4. Chọn file `postman.json` hoặc paste URL
5. Click **Import**

## Cấu hình Environment Variables

Sau khi import, bạn cần cấu hình các biến môi trường:

### Tạo Environment mới

1. Click vào **Environments** (bên trái)
2. Click **+** để tạo environment mới
3. Đặt tên: `LMS Local` hoặc `LMS Production`

### Thêm các biến

Thêm các biến sau:

| Variable | Initial Value | Current Value |
|----------|---------------|---------------|
| `base_url` | `http://localhost:8000` | `http://localhost:8000` |
| `access_token` | (để trống) | (sẽ được tự động điền sau khi login) |
| `refresh_token` | (để trống) | (sẽ được tự động điền sau khi login) |

### Chọn Environment

1. Click vào dropdown **Environments** (góc trên bên phải)
2. Chọn environment vừa tạo

## Cách sử dụng

### 1. Đăng nhập

1. Mở folder **Authentication (Public)**
2. Chọn request **Login**
3. Điền email và password trong body
4. Click **Send**
5. Access token và refresh token sẽ tự động được lưu vào environment variables

### 2. Sử dụng các API khác

Sau khi đăng nhập, tất cả các API protected sẽ tự động sử dụng `access_token` từ environment.

### 3. Refresh Token

Khi access token hết hạn (sau 15 phút):
1. Mở folder **Authentication (Public)**
2. Chọn request **Refresh Token**
3. Click **Send**
4. Access token mới sẽ được trả về (cần cập nhật thủ công vào environment)

## Cấu trúc Collection

Collection được tổ chức thành các folders:

### 1. Authentication (Public)
- **Register**: Đăng ký tài khoản mới
- **Login**: Đăng nhập (tự động lưu tokens)
- **Refresh Token**: Refresh access token mới

### 2. Authentication (Protected)
- **Logout**: Đăng xuất
- **Get Current User**: Lấy thông tin user hiện tại

### 3. User Management
- **Get All Users**: Danh sách người dùng (có pagination, search, filter)
- **Create User**: Tạo người dùng mới
- **Get User by ID**: Chi tiết người dùng
- **Update User**: Cập nhật người dùng
- **Delete User**: Xóa người dùng (soft delete)
- **Assign Roles to User**: Phân quyền cho người dùng

### 4. Course Management
- **Get All Courses**: Danh sách khóa học (có pagination, search)
- **Create Course**: Tạo khóa học mới
- **Get Course by ID**: Chi tiết khóa học
- **Update Course**: Cập nhật khóa học
- **Delete Course**: Xóa khóa học (soft delete)
- **Assign Users to Course**: Phân quyền sinh viên vào khóa học (sync)
- **Add User to Course**: Thêm sinh viên vào khóa học
- **Remove User from Course**: Xóa sinh viên khỏi khóa học

### 5. Lesson Management
- **Get All Lessons**: Danh sách bài học (có pagination, search, filter by course)
- **Create Lesson**: Tạo bài học mới
- **Get Lesson by ID**: Chi tiết bài học
- **Update Lesson**: Cập nhật bài học
- **Delete Lesson**: Xóa bài học (soft delete)

### 6. Student
- **Get Student Courses**: Danh sách khóa học của sinh viên
- **Get Student Course Detail**: Chi tiết khóa học (bao gồm tiến độ học)
- **Get Student Lesson Detail**: Chi tiết bài học (bao gồm tiến độ học)
- **Update Progress**: Cập nhật tiến độ xem bài học
- **Get Course Progress**: Tiến độ học của sinh viên trong một khóa học

### 7. Reports
- **Get Overview**: Tổng quan thống kê
- **Get Course Statistics**: Thống kê khóa học
- **Get Course Students**: Danh sách sinh viên trong khóa học với tiến độ
- **Get Student Course Progress**: Chi tiết tiến độ học của một sinh viên
- **Get Student Login History**: Lịch sử đăng nhập của sinh viên

## Lưu ý

1. **Base URL**: Mặc định là `http://localhost:8000`. Nếu bạn chạy server ở port khác hoặc domain khác, cần cập nhật biến `base_url` trong environment.

2. **Access Token**: 
   - Tự động được lưu sau khi login thành công
   - Hết hạn sau 15 phút (mặc định)
   - Cần refresh token để lấy access token mới

3. **Refresh Token**:
   - Tự động được lưu sau khi login thành công
   - Hết hạn sau 2 tuần (mặc định)
   - Có thể sử dụng nhiều lần cho đến khi hết hạn

4. **Query Parameters**: 
   - Các request có query parameters (như `search`, `per_page`, `page`) có thể chỉnh sửa trực tiếp trong URL
   - Hoặc sử dụng tab **Params** trong Postman

5. **Path Variables**:
   - Các request có path variables (như `:id`, `:courseId`, `:userId`) có thể chỉnh sửa trong tab **Params**
   - Hoặc chỉnh sửa trực tiếp trong URL

6. **Body Parameters**:
   - Tất cả các request POST/PUT đều có body mẫu
   - Có thể chỉnh sửa theo nhu cầu

## Troubleshooting

### Lỗi 401 Unauthorized
- Kiểm tra xem đã đăng nhập chưa
- Kiểm tra access_token trong environment có đúng không
- Thử refresh token hoặc đăng nhập lại

### Lỗi 404 Not Found
- Kiểm tra base_url có đúng không
- Kiểm tra route có tồn tại không
- Kiểm tra ID trong path variable có đúng không

### Lỗi 422 Validation Error
- Kiểm tra body request có đúng format không
- Kiểm tra các trường required có đầy đủ không
- Xem response để biết lỗi cụ thể

### Token không tự động lưu
- Kiểm tra xem đã chọn đúng environment chưa
- Kiểm tra script trong request Login có chạy không
- Thử chạy lại request Login

## Tips

1. **Sử dụng Pre-request Script**: Có thể thêm script để tự động refresh token trước khi gọi API
2. **Sử dụng Tests**: Có thể thêm tests để kiểm tra response
3. **Sử dụng Collection Runner**: Có thể chạy nhiều requests cùng lúc
4. **Export/Import**: Có thể export collection để chia sẻ với team


Xây dựng một hệ thống quản lý khoa học trực tuyến. Đặc tả yêu cầu như sau:
- Về phần quản trị: Hệ thống bao gồm hai chức năng chính: Quản lý thông tin người dùng và Quản lý khóa học:
+ Quản lý thông tin người dùng: Thực hiện các chức năng cơ bản như
thêm mới, sửa, xóá, phân quyền người dùng.
+ Quản lý khóa học: Khóa học bao gồm thông tin cơ bản như tên khoá học, mô tả về khóa học, thời gian bắt đầu, thời gian kết thúc, danh sách các bài học (video). Các bài học sẽ có các thông tin như: tên bài học, thời lượng, thứ tự hiển thị bài học trong khóa học. Sau khi có danh sách khóa học, người quản lý sẽ phân quyền cho sinh viên được phép tham gia vào những khóá học nào. Người quản lý sẽ thống kê được khóa học có bao nhiêu sinh viên, xem được quá trình tham gia học của sinh viên thông qua báo cáo về thời gian đăng nhập khoa học, thời lượng xem bài học của sinh viên. Từ đó người quản lý sẽ đánh giá sinh viên đạt yêu cầu hay không. Nếu sinh viên có thời lượng xem video đạt trên 80% được xem là đạt yêu cầu. Ngược lại chấm không đạt yêu cầu.
- Về phần sinh viên: Sinh viên có thể đăng nhập trên ứng dụng điện thoại đế xem danh sách khóa học của mình được phép tham gia. Sau khi đăng nhập
thành công, sinh viên chọn một khóa học thì ứng dụng sẽ hiển thị thông tin về khóa học và bài học tương ứng. Sinh viên chọn bài học để học tập. Lưu ý sinh viên có thể xem tiếp bài học đã được xem trước đây. Ứng dụng dành cho sinh viên đảm bảo về tối ưu tốc độ và hiệu suất truyền tải đối với những bài học có
thời lượng dài.
* Yêu cầu về ngôn ngữ lập trình để xây dựng hệ thống theo yêu cầu:
- Phần quản trị: xây dựng bằng Laravel 8 kết hợp với MySql
- Phần ứng dụng dành cho sinh viên: xây dựng bằng Flutter, kết nối cơ sở dữ liệu thông qua API
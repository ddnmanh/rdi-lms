# Video Signed URL FLOW

## Tổng quan

Hệ thống sử dụng cơ chế **Signed URL** để bảo vệ video khỏi truy cập trái phép. Laravel chịu trách nhiệm **cấp chữ ký**, còn **Nginx** sẽ xác thực chữ ký và phục vụ file video.

## Quy trình hoạt động

```
┌─────────────┐     1. Request signature     ┌─────────────┐
│             │ ───────────────────────────► │             │
│   Client    │                              │   Laravel   │
│  (Flutter)  │ ◄─────────────────────────── │   Backend   │
│             │     2. Return signed URL     │             │
└─────────────┘                              └─────────────┘
       │
       │ 3. Request video with token
       ▼
┌─────────────┐
│             │
│    Nginx    │ ──► 4. Validate token
│   (Static)  │ ──► 5. Serve static files (.m3u8, .ts, .mp4)
│             │
└─────────────┘
```
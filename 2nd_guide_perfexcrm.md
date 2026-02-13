### Hướng dẫn Phát triển Module Tích hợp SePay cho Perfex CRM

Tài liệu này hướng dẫn từng bước xây dựng module thanh toán SePay. Các thành phần kỹ thuật được chuẩn hóa để tương thích tốt nhất với kiến trúc của Perfex CRM (dựa trên CodeIgniter).

#### 1. Cấu trúc thư mục (Module Structure)
Truy cập thư mục `modules/` trong mã nguồn Perfex CRM và tạo cấu trúc sau:

*   **Thư mục gốc:** `modules/sepay/`
*   **File khởi tạo:** `modules/sepay/sepay.php` (Bắt buộc trùng tên với thư mục).
*   **Các thư mục thành phần:**
    *   `controllers/`: Chứa file xử lý Webhook (nhận dữ liệu từ SePay).
    *   `libraries/`: Chứa Class chính của cổng thanh toán.
    *   `language/english/`: Chứa file ngôn ngữ (lưu ý: thư mục là `language` số ít trong Perfex, không phải `languages`).
    *   `views/`: Chứa giao diện hiển thị QR Code (nếu không redirect trực tiếp).

#### 2. Thiết lập File khởi tạo (sepay.php)
File này dùng để đăng ký module và cổng thanh toán với hệ thống.

**Lưu ý sửa đổi:** Tôi đã sửa lại cú pháp comment block (bị lỗi ở bản gốc) và tham số hàm đăng ký.

```php
<?php
/**
 * Module Name: SePay Payment Gateway
 * Description: Tích hợp cổng thanh toán SePay (Chuyển khoản ngân hàng tự động) cho Perfex CRM.
 * Version: 1.0.0
 * Requires at least: 2.3.5
 */

// Định nghĩa tên module
define('SEPAY_MODULE_NAME', 'sepay');

// Đăng ký Activation Hook
register_activation_hook(SEPAY_MODULE_NAME, 'sepay_activation_hook');

function sepay_activation_hook() {
    require_once(__DIR__ . '/install.php'); // Tạo bảng database nếu cần (thường gateway ít dùng)
}

// Đăng ký file ngôn ngữ
register_language_files(SEPAY_MODULE_NAME, [SEPAY_MODULE_NAME]);

// Đăng ký Gateway
// Tham số 1: Tên Class của Gateway (sẽ tạo ở bước 3)
// Tham số 2: Tên Module
register_payment_gateway('Sepay_gateway', SEPAY_MODULE_NAME);
```

#### 3. Tạo Library Gateway (libraries/Sepay_gateway.php)
Đây là "trái tim" của module. Class này phải kế thừa từ `App_gateway`.

*   **Tên file:** `Sepay_gateway.php` (Chữ cái đầu viết hoa).
*   **Tên Class:** `Sepay_gateway`.

**Nhiệm vụ chính:**
1.  **`__construct()`:** Khai báo `id`, `name` và các `settings` (API Key, Số tài khoản...).
2.  **`process_payment($data)`:** Hàm này nhận dữ liệu hóa đơn (`$data`). Bạn cần dùng dữ liệu này tạo URL thanh toán SePay hoặc tạo mã QR.
    *   *Return:* Phải trả về mảng có key `redirect_url` để chuyển hướng khách hàng sang trang thanh toán SePay.

#### 4. Xử lý Webhook (Quan trọng)
SePay cần một đường dẫn công khai (Public URL) để báo cho Perfex biết khi khách thanh toán thành công.

1.  **Tạo Controller:** `modules/sepay/controllers/Sepay_webhook.php`
2.  **Nội dung Controller:** Class này kế thừa `App_Controller`.
3.  **Xử lý CSRF:** Vì Webhook từ bên ngoài gọi vào, bạn có thể cần tắt kiểm tra CSRF cho riêng controller này hoặc khai báo trong `config/config.php` (tùy phiên bản Perfex, nhưng thường Controller trong module cần xử lý khéo léo việc này).

**Logic nhận tiền:**
*   Nhận JSON từ SePay.
*   Kiểm tra API Key/Signature để bảo mật.
*   Tìm hóa đơn (Invoice) tương ứng với nội dung chuyển khoản.
*   Sử dụng hàm: `$this->sepay_gateway->addPayment(...)` (gọi lại hàm xử lý thanh toán của Perfex để cập nhật trạng thái hóa đơn thành "Paid").

#### 5. Ngôn ngữ (Language)
Tạo file: `modules/sepay/language/english/sepay_lang.php` (Lưu ý đường dẫn `language/english/`).

Nội dung mẫu:
```php
<?php
$lang['sepay'] = 'Chuyển khoản ngân hàng (SePay)';
$lang['sepay_api_key'] = 'SePay API Key';
$lang['sepay_account_number'] = 'Số tài khoản nhận tiền';
```

#### 6. Kích hoạt và Cấu hình
Quy trình chuẩn sau khi code xong:
1.  Vào **Setup -> Modules**, tìm **SePay Payment Gateway** và nhấn **Activate**.
2.  Vào **Setup -> Settings -> Payment Gateways**.
3.  Tab **SePay** sẽ xuất hiện. Bạn nhập API Key, Số tài khoản ngân hàng và tích chọn "Active".

---

### Một số lưu ý kỹ thuật bổ sung:
*   **Hook `module_{module_name}_action`:** Nếu bạn muốn tùy biến thêm logic khi module được kích hoạt/hủy kích hoạt.
*   **Security:** Luôn kiểm tra kỹ dữ liệu đầu vào từ Webhook để tránh giả mạo giao dịch. So sánh số tiền nhận được với số tiền trên hóa đơn (Invoice Total).
*   **Testing:** Sử dụng môi trường Sandbox của SePay (nếu có) hoặc thử chuyển khoản số tiền nhỏ (1.000đ) để test luồng hoạt động trước khi đưa vào thực tế.

---
**Bạn có muốn tôi viết chi tiết code mẫu cho file `libraries/Sepay_gateway.php` (phần xử lý hàm `process_payment`) không?**
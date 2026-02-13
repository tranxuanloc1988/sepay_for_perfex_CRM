# Hướng dẫn Phát triển Module cho Perfex CRM (Comprehensive Guide)

Tài liệu này tổng hợp kiến thức và quy chuẩn để phát triển module cho Perfex CRM (dựa trên CodeIgniter 3), bao gồm cả nghiên cứu điển hình (Case Study) về tích hợp cổng thanh toán SePay.

## 1. Cấu trúc Thư mục Module Chuẩn

Mọi module nằm trong thư mục `modules/`. Tên thư mục nên viết thường, không dấu, dùng gạch dưới (ví dụ: `sepay`).

```text
sepay/
├── controllers/            # Xử lý Webhook (IPN) & Redirect
├── language/               # Đa ngôn ngữ (Lưu ý: "language" số ít)
│   ├── english/
│   └── vietnamese/
├── libraries/              # File Sepay_gateway.php kế thừa App_gateway
├── views/                  # Giao diện (thường dùng cho redirect/QR)
├── sepay.php               # File khởi tạo chính (Đăng ký gateway tại đây)
└── install.php             # Chạy khi kích hoạt (Tạo bảng DB nếu cần)
```

---

## 2. File Khởi tạo (`sepay.php`)

Đây là nơi đăng ký module và cổng thanh toán với Perfex.

```php
// Đăng ký Gateway
// Tham số 1: Tên Class (viết đúng hoa thường nếu chạy Linux)
// Tham số 2: ID Module (viết thường)
register_payment_gateway('Sepay_gateway', 'sepay');
```

---

## 3. Tích hợp Cổng thanh toán (Case Study: SePay)

Dưới đây là các quy tắc "sống còn" khi tích hợp các cổng thanh toán yêu cầu bảo mật cao như SePay.

### A. Quy tắc Chữ ký (Signature)
SePay sử dụng thuật toán `HMAC-SHA256`. Lỗi "Không xác định" thường do chữ ký không khớp.

**Quy tắc 1: Thứ tự trường là tuyệt đối.**
Chuỗi ký phải được nối theo đúng thứ tự mà tài liệu kỹ thuật yêu cầu:
1. `merchant`
2. `operation`
3. `payment_method`
4. `order_amount`
5. `currency`
6. `order_invoice_number`
7. `order_description`
8. `customer_id`
9. `success_url`
10. `error_url`
11. `cancel_url`

**Quy tắc 2: Định dạng dữ liệu.**
- **Số tiền:** Với VND, phải làm tròn thành số nguyên (`round($amount)`), không được có phần thập phân (`.00`).
- **Làm sạch:** Luôn sử dụng `trim()` cho Merchant ID và Secret Key.

### B. Logic xử lý trong `Sepay_gateway.php`
Hàm `process_payment` nên chuẩn bị dữ liệu và lưu vào session, sau đó redirect sang một controller trung gian để thực hiện POST sang cổng thanh toán.

```php
public function process_payment($data) {
    // 1. Chuẩn bị $fields theo đúng thứ tự
    // 2. Tạo signature: base64_encode(hash_hmac('sha256', $signedString, $secretKey, true))
    // 3. Lưu vào session và redirect tới controller redirect
}
```

### C. Xử lý Webhook (IPN)
Webhook cần một đường dẫn public (Public URL).
- **Endpoint:** `yoursite.com/sepay/sepay_webhook/ipn`
- **Logic:** Nhận JSON -> Xác thực -> Tìm hóa đơn -> Gọi `$this->sepay_gateway->addPayment()`.
- **Lưu ý:** ID cổng thanh toán dùng trong `addPayment` phải khớp hoàn toàn với ID đã đăng ký.

---

## 4. Database & Cài đặt

Luôn sử dụng `db_prefix()` và kiểm tra sự tồn tại của bảng trước khi tạo.

```php
if (!$CI->db->table_exists(db_prefix() . 'sepay_logs')) {
    // SQL CREATE TABLE
}
```

---

## 5. Các Mẹo nhỏ (Tips)

1.  **Gỡ lỗi:** Luôn log lại dữ liệu nhận được từ Webhook bằng `log_activity()`.
2.  **Môi trường:** Phân biệt rõ URL Sandbox (`pay-sandbox.sepay.vn`) và Production (`pay.sepay.vn`).
3.  **Lấy Cài đặt:** Sử dụng `$this->getSetting('key')` hoặc `$this->decryptSetting('key')` cho các trường bảo mật.
4.  **Tương thích Linux:** Chú ý hoa thường trong tên file và tên class (ví dụ: `Sepay_gateway.php` phải chứa class `Sepay_gateway`).

## 6. Danh sách Hooks Quan Trọng

*   `admin_init`: Thêm menu, cài đặt.
*   `app_admin_footer`: Chèn JS tùy chỉnh.
*   `before_render_payment_gateway_settings`: Tùy biến trang cài đặt cổng thanh toán.


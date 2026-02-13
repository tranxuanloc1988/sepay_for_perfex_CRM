# Perfex CRM Payment Gateway Architecture

Tài liệu này giải thích cấu trúc và cách hoạt động của hệ thống cổng thanh toán (payment gateways) trong Perfex CRM, tập trung vào cách xử lý và xác nhận thanh toán thành công.

## 1. Thành phần chính (Core Components)

Hệ thống thanh toán của Perfex CRM được xây dựng dựa trên kiến trúc Model-View-Controller (MVC) của CodeIgniter, bao gồm các thành phần chính:

### A. Base Library: `App_gateway.php`
- Nằm tại: [`libraries/App_gateway.php`](libraries/App_gateway.php)
- Là lớp cha (parent class) cho tất cả các cổng thanh toán.
- Định nghĩa các thuộc tính chung (id, name, settings, processingFees).
- Cung cấp phương thức quan trọng: `addPayment($data)`. Phương thức này gọi `payments_model->add()` để ghi nhận thanh toán vào cơ sở dữ liệu, gửi email thông báo và cập nhật trạng thái hóa đơn.

### B. Gateway Libraries (Concrete Implementations)
- Nằm tại: `libraries/` (Ví dụ: [`libraries/Paypal_gateway.php`](libraries/Paypal_gateway.php), [`libraries/Stripe_gateway.php`](libraries/Stripe_gateway.php))
- Kế thừa từ `App_gateway`.
- Triển khai phương thức `process_payment($data)` để khởi tạo giao dịch với nhà cung cấp (API gọi đi).
- Chứa các cấu hình riêng cho từng cổng (API Keys, Secret, v.v.).

### C. Gateway Controllers (Handlers)
- Nằm tại: `controller/` (Ví dụ: [`controller/Paypal.php`](controller/Paypal.php), [`controller/Stripe.php`](controller/Stripe.php))
- Tiếp nhận phản hồi (callback/webhook) từ phía nhà cung cấp thanh toán.
- Xác thực dữ liệu và gọi phương thức `addPayment()` của library tương ứng để hoàn tất quy trình.

---

## 2. Quy trình xác nhận thanh toán (Payment Confirmation Flow)

Quy trình xác nhận thanh toán thường diễn ra theo hai cách chính tùy thuộc vào cổng thanh toán: **Redirect (Callback)** hoặc **Webhook**.

### Cách 1: Redirect/Callback (Ví dụ: PayPal)
Thường dùng khi khách hàng được chuyển hướng sang trang của nhà cung cấp và quay lại sau khi thanh toán.

1.  **Khởi tạo**: Khách hàng nhấn "Pay Now", hệ thống gọi `process_payment()`.
2.  **Chuyển hướng**: Khách hàng thanh toán trên PayPal.
3.  **Phản hồi**: PayPal chuyển hướng khách hàng về URL `gateways/paypal/complete_purchase`.
4.  **Xử lý (Controller)**:
    - [`controller/Paypal.php::complete_purchase()`](controller/Paypal.php:7) tiếp nhận các tham số (`invoiceid`, `hash`, `token`, `reference`).
    - Gọi `$this->paypal_gateway->complete_purchase()` để kiểm tra trạng thái thanh toán cuối cùng với PayPal API.
5.  **Ghi nhận**: Nếu thành công, gọi `$this->paypal_gateway->addPayment()`.

### Cách 2: Webhook (Ví dụ: Stripe)
Đây là cách an toàn và tin cậy nhất, vì nó hoạt động Server-to-Server ngay cả khi khách hàng đóng trình duyệt.

1.  **Sự kiện**: Khách hàng thanh toán thành công, Stripe gửi một POST request (Webhook) đến CRM.
2.  **Tiếp nhận**: [`controller/Stripe.php::webhook_endpoint()`](controller/Stripe.php:69) nhận payload.
3.  **Xác thực**: Kiểm tra chữ ký (`HTTP_STRIPE_SIGNATURE`) để đảm bảo request thực sự từ Stripe.
4.  **Phân loại**: Tìm sự kiện `checkout.session.completed` hoặc `invoice.payment_succeeded`.
5.  **Ghi nhận**:
    - Lấy `InvoiceId` từ metadata của giao dịch.
    - Kiểm tra xem giao dịch đã tồn tại chưa (`transaction_exists`).
    - Gọi `$this->stripe_gateway->addPayment()` để xác nhận hóa đơn đã thanh toán.

---

## 3. Cách triển khai một Cổng thanh toán mới

Để thêm một cổng thanh toán, bạn cần:

1.  **Tạo Library**: Tạo file `Your_gateway_gateway.php` trong `libraries/`. Kế thừa `App_gateway` và implement `process_payment($data)`.
2.  **Tạo Controller**: Tạo file `Your_gateway.php` trong `controller/` để xử lý callback/webhook.
3.  **Xác nhận thanh toán**: Trong controller, sau khi xác thực thanh toán thành công từ nhà cung cấp, hãy luôn gọi:
    ```php
    $success = $this->your_gateway_gateway->addPayment([
        'amount'        => $amount,
        'invoiceid'     => $invoiceid,
        'transactionid' => $transaction_id, // ID từ phía cổng thanh toán
    ]);
    ```
4.  **Tự động hóa**: Perfex CRM sẽ tự động nhận diện và hiển thị cổng thanh toán trong trang Settings nếu Library được đặt đúng thư mục và kế thừa đúng lớp.

## 4. Các hàm quan trọng cần lưu ý

- `addPayment($data)`: Hàm "chốt" việc thanh toán. Nó sẽ lo việc cập nhật bảng `tblinvoicepaymentrecords`, thay đổi trạng thái hóa đơn thành "Paid" hoặc "Partially Paid", và kích hoạt các hooks liên quan.
- `check_invoice_restrictions($id, $hash)`: Luôn dùng hàm này trong controller để bảo mật, đảm bảo request đang thao tác đúng hóa đơn.
- `log_activity($message)`: Sử dụng để ghi lại vết nếu có lỗi xảy ra trong quá trình callback/webhook.

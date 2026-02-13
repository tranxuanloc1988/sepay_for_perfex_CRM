# SePay Payment Gateway for Perfex CRM

Module tích hợp cổng thanh toán SePay vào Perfex CRM, cho phép khách hàng thanh toán hóa đơn thông qua quét mã QR chuyển khoản ngân hàng.

## 1. Cài đặt

1. Copy thư mục `sepay` vào thư mục `modules` của Perfex CRM: `/modules/sepay/`.
2. Đăng nhập vào trang quản trị (Admin).
3. Đi tới **Thiết lập (Setup)** -> **Modules**.
4. Tìm module **SePay Payment Gateway** và nhấn **Kích hoạt (Activate)**.

## 2. Cấu hình

Sau khi kích hoạt, bạn cần cấu hình thông tin kết nối với SePay:

1. Đi tới **Thiết lập (Setup)** -> **Tài chính (Finance)** -> **Cổng thanh toán (Payment Gateways)**.
2. Chọn tab **SePay**.
3. Điền các thông tin sau:
    *   **API Key (Merchant ID)**: Lấy từ trang quản trị SePay.
    *   **Secret Key**: Lấy từ trang quản trị SePay.
    *   **Số tài khoản (Account Number)**: Số tài khoản ngân hàng nhận tiền.
    *   **Mô tả**: Nội dung hiển thị cho khách hàng khi chọn phương thức thanh toán này.
    *   **Đơn vị tiền tệ (Currencies)**: Nhập `VND`.
    *   **Chế độ kiểm thử (Test Mode)**: Chọn `Có` (Yes) nếu đang test trên môi trường Sandbox của SePay, chọn `Không` (No) nếu chạy thật.

## 3. Sử dụng

*   Khi khách hàng xem hóa đơn (Invoice) và nhấn nút **Thanh toán ngay (Pay Now)**.
*   Chọn phương thức **SePay**.
*   Hệ thống sẽ chuyển hướng khách hàng sang trang thanh toán của SePay (có mã QR).
*   Khách hàng thực hiện quét mã QR để thanh toán.

## ⚠️ LƯU Ý QUAN TRỌNG

**Hiện tại chức năng tự động gạch nợ (ghi nhận thanh toán vào hóa đơn) khi khách hàng thanh toán thành công CHƯA ĐƯỢC XỬ LÝ HOÀN THIỆN.**

*   Sau khi khách hàng thanh toán thành công trên SePay, hệ thống sẽ chuyển hướng về trang thông báo thành công.
*   Tuy nhiên, **Hóa đơn trên Perfex CRM có thể sẽ chưa được cập nhật trạng thái "Đã thanh toán" tự động**.
*   Bạn cần kiểm tra tài khoản ngân hàng và xác nhận thanh toán thủ công cho hóa đơn trên CRM nếu cần thiết.

## Hỗ trợ

Nếu gặp vấn đề trong quá trình cài đặt, vui lòng kiểm tra file log tại `application/logs/`.

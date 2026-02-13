Chào bạn, dưới đây là file hướng dẫn (Markdown) chi tiết cách tạo module cho Perfex CRM dựa trên nội dung bạn đã cung cấp.

---

# Hướng dẫn Phát triển Module cho Perfex CRM

Tài liệu này hướng dẫn các bước cơ bản để tạo một module mới trong Perfex CRM, từ cấu trúc thư mục đến các hook quan trọng và quản lý cơ sở dữ liệu.

## 1. Cấu trúc thư mục Module

Mọi module phải nằm trong thư mục `modules/` của Perfex CRM. Tên thư mục module nên viết thường và dùng dấu gạch dưới (ví dụ: `template_module`).

**Cấu trúc tệp tiêu chuẩn:**
```text
template_module/
├── controllers/          # Chứa các tệp Controller
├── languages/            # Chứa các tệp ngôn ngữ (English, Vietnamese, v.v.)
│   └── english/
│       └── template_module_lang.php
├── libraries/            # Chứa các thư viện bổ trợ
├── views/                # Chứa các tệp giao diện (HTML/PHP)
├── install.php           # Tệp thực thi khi kích hoạt module (tạo bảng DB)
└── template_module.php   # Tệp gốc (Root file) của module - Quan trọng nhất
```

---

## 2. Thiết lập Tệp gốc (Root File)

Tệp gốc phải có **cùng tên** với tên thư mục module. Đây là nơi khai báo thông tin module và đăng ký các hook.

**Ví dụ: `template_module.php`**

```php
<?php
/**
 * Tên Module: Template Module
 * Mô tả: Mô tả ngắn gọn về chức năng của module.
 * Phiên bản: 1.0.0
 * Yêu cầu phiên bản Perfex: 2.3.4
 */

// Định nghĩa hằng số tên module (Sử dụng xuyên suốt trong code)
define('TEMPLATE_MODULE_NAME', 'template_module');

// Đăng ký hook kích hoạt (Activation Hook)
register_activation_hook(TEMPLATE_MODULE_NAME, 'template_module_activation_hook');

function template_module_activation_hook() {
    require_once(__DIR__ . '/install.php');
}

// Đăng ký tệp ngôn ngữ
register_language_files(TEMPLATE_MODULE_NAME, [TEMPLATE_MODULE_NAME]);

// Hook để thêm menu vào Admin
hooks()->add_action('admin_init', 'template_module_init_menu_items');

function template_module_init_menu_items() {
    $CI = &get_instance();
    $CI->app_menu->add_sidebar_menu_item('template-module-id', [
        'name'     => 'Template Module', // Tên hiển thị trên menu
        'href'     => admin_url('template_module'), // Đường dẫn
        'icon'     => 'fa-solid fa-code', // Biểu tượng font-awesome
        'position' => 10, // Vị trí hiển thị
    ]);
}
```

---

## 3. Quản lý Cơ sở dữ liệu (`install.php`)

Tệp này sẽ được gọi khi bạn nhấn nút **Kích hoạt (Activate)** module trong quản trị. Bạn có thể dùng nó để tạo bảng mới hoặc chỉnh sửa bảng hiện có.

```php
<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Kiểm tra nếu bảng chưa tồn tại thì tạo mới
if (!$CI->db->table_exists(db_prefix() . 'template_table')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'template_table` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `description` text,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
}
```

---

## 4. Ngôn ngữ (Languages)

Để hỗ trợ đa ngôn ngữ, hãy tạo thư mục theo tên ngôn ngữ (ví dụ: `english`). Tệp ngôn ngữ phải tuân theo quy tắc: `{tên_module}_lang.php`.

**Ví dụ: `languages/english/template_module_lang.php`**
```php
<?php
$lang['template_module_name'] = 'My Awesome Module';
$lang['template_module_hello'] = 'Hello World!';
```

---

## 5. Controller và View

### Controller
Tạo file trong thư mục `controllers/`. Tên Controller nên trùng với tên module để dễ truy cập qua URL `admin/tên_module`.

```php
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Template_module extends AdminController {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['title'] = 'Trang quản trị Module';
        $this->load->view('template_module_view', $data);
    }
}
```

### View
Tạo file trong thư mục `views/`.

**Ví dụ: `views/template_module_view.php`**
```php
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>Chào mừng bạn đến với Module Template</h4>
                        <p>Đây là nội dung hiển thị của module.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
```

---

## 6. Các Hook quan trọng khác

*   **`register_deactivation_hook`**: Chạy khi module bị hủy kích hoạt (thường dùng để dọn dẹp tạm thời).
*   **`register_uninstall_hook`**: Chạy khi module bị xóa hoàn toàn (dùng để xóa bảng trong cơ sở dữ liệu).
*   **Filters**: Dùng để thay đổi dữ liệu trước hoặc sau khi gửi/nhận (ví dụ: `before_ticket_created`).

## 7. Cách cài đặt và kiểm tra

1. Copy thư mục module vào đường dẫn `modules/` của dự án Perfex CRM.
2. Đăng nhập Admin -> Vào mục **Setup** -> **Modules**.
3. Tìm module của bạn trong danh sách và nhấn **Activate**.
4. Kiểm tra menu bên trái xem module đã xuất hiện chưa.

---
*Chúc bạn phát triển module thành công!*
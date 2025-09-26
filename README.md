# Phân tích kỹ thuật phần mềm_N05_Nhom6
Dự án về web bán sheet và quản lý sheet nhạc trong game Sky Children

Thành viên nhóm:
1. Lê Ngọc Khánh 23010546
2. Nguyễn Khắc Long 23010418
3. Nguyễn Anh Tài 23010584

# 🎵 Sky Music Store

**Sky Music Store** là một ứng dụng web đa chức năng được xây dựng bằng Laravel, tích hợp Firebase Authentication với hệ thống phân quyền chi tiết cho admin, business và user.

## ✨ Tính năng chính

### 🔐 Xác thực & Phân quyền
- **Firebase Authentication** - Đăng ký, đăng nhập, quên mật khẩu
- **Role-based Access Control** - 3 cấp quyền: Admin, Business, User
- **Session Management** - Quản lý phiên đăng nhập an toàn

### 👤 Hệ thống User
- **User Dashboard** - Trang cá nhân với thông tin tài khoản
- **Coin System** - Hệ thống điểm/coin tích hợp
- **Profile Management** - Quản lý hồ sơ cá nhân

### 🛡️ Admin Panel
- **User Management** - Quản lý người dùng và phân quyền
- **Product Management** - Quản lý sản phẩm
- **Order Management** - Quản lý đơn hàng
- **Posts Management** - Quản lý bài viết
- **Analytics Dashboard** - Thống kê và báo cáo
- **System Settings** - Cấu hình hệ thống

### 🏢 Business Dashboard
- **Business Panel** - Giao diện dành cho đối tác kinh doanh
- **Sales Management** - Quản lý bán hàng

### 🌐 Public Features
- **Homepage** - Trang chủ với giao diện đẹp mắt
- **Community** - Cộng đồng và bài viết
- **Shop** - Cửa hàng với giỏ hàng
- **Support** - Hỗ trợ khách hàng

### 🎨 UI/UX
- **Responsive Design** - Tương thích đa thiết bị
- **Tailwind CSS** - Thiết kế hiện đại
- **Animated Background** - Hiệu ứng nền với mây và sao
- **Sky Theme** - Chủ đề bầu trời độc đáo

## 🛠️ Công nghệ sử dụng

- **Backend:** Laravel 12.0 (PHP 8.2+)
- **Authentication:** Firebase PHP SDK
- **Frontend:** Tailwind CSS 4.0, Vite
- **Database:** MySQL/SQLite (Laravel Eloquent ORM)
- **Session:** Database-based sessions

## 📋 Yêu cầu hệ thống

- PHP >= 8.2
- Composer
- Node.js & NPM
- Web server (Apache/Nginx) hoặc `php artisan serve`
- Firebase Project với Authentication enabled

## 🚀 Cài đặt

### 1. Clone repository
```bash
git clone https://github.com/longcodengu24/PTTKPM25-26_ClassN05_Nhom6.git
cd PTTKPM25-26_ClassN05_Nhom6/SRC/SM_Store
```

### 2. Cài đặt dependencies
```bash
# Cài đặt PHP packages
composer install

# Cài đặt Node.js packages
npm install
```

### 3. Cấu hình môi trường
```bash
# Tạo file .env từ template
cp .env.example .env

# Generate Laravel application key
php artisan key:generate
```

### 4. Cấu hình Firebase
1. Tạo Firebase project tại [Firebase Console](https://console.firebase.google.com/)
2. Enable Authentication với Email/Password
3. Tạo Service Account và download JSON key file
4. Đặt file JSON vào thư mục project
5. Cập nhật file `.env`:
```env
FIREBASE_CREDENTIALS="path/to/your/firebase-service-account.json"
```

### 5. Cấu hình Database
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Chạy migrations
```bash
php artisan migrate
```

### 7. Build assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Khởi động server
```bash
php artisan serve
```

Ứng dụng sẽ chạy tại: http://localhost:8000

## 📱 Cách sử dụng

### Đăng ký tài khoản mới
1. Truy cập `/register`
2. Điền thông tin: Họ tên, Email, Mật khẩu
3. Tài khoản mới sẽ có role `user` mặc định

### Đăng nhập
1. Truy cập `/login`
2. Nhập email và mật khẩu
3. Hệ thống sẽ chuyển hướng dựa trên role:
   - **Admin**: `/admin/dashboard`
   - **Business**: `/business/dashboard`  
   - **User**: Trang chủ `/`

### Admin Functions
- Truy cập Admin Panel: `/admin`
- Quản lý phân quyền user: `/admin/roles`
- Quản lý sản phẩm, đơn hàng, bài viết
- Xem thống kê và analytics

### User Features
- Duyệt sản phẩm tại `/shop`
- Tham gia cộng đồng tại `/community`
- Quản lý tài khoản cá nhân
- Nạp/rút coin

## 🗂️ Cấu trúc thư mục

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   └── Auth/           # Authentication controllers
│   └── Middleware/         # Custom middleware
├── Models/                 # Eloquent models
└── Providers/             # Service providers

resources/
├── views/
│   ├── admin/             # Admin panel views
│   ├── auth/              # Auth pages
│   ├── business/          # Business dashboard
│   ├── page/              # Public pages
│   ├── layouts/           # Layout templates
│   └── components/        # Reusable components
├── css/                   # Stylesheets
└── js/                    # JavaScript files

routes/
├── web.php                # Web routes
└── api.php                # API routes
```

## 🔐 Phân quyền

### Admin
- Toàn quyền truy cập hệ thống
- Quản lý users và phân quyền
- Quản lý sản phẩm, đơn hàng
- Xem analytics và reports

### Business
- Truy cập business dashboard
- Quản lý sản phẩm của mình
- Xem thống kê bán hàng

### User
- Truy cập các tính năng công khai
- Mua sắm và tương tác cộng đồng
- Quản lý tài khoản cá nhân

## 📊 Tài liệu Biểu đồ PlantUML

Dự án bao gồm các biểu đồ PlantUML chi tiết để mô tả kiến trúc và luồng hoạt động của hệ thống. Tất cả file biểu đồ được lưu trữ trong thư mục `docs/diagrams/`.

### 📁 Cấu Trúc Thư Mục Biểu Đồ

```
docs/diagrams/
├── README.md                 # Hướng dẫn chi tiết
├── sequence/                 # Biểu đồ tuần tự (Sequence Diagrams)
├── usecase/                  # Biểu đồ ca sử dụng (Use Case Diagrams)  
├── class/                    # Biểu đồ lớp (Class Diagrams)
├── component/                # Biểu đồ thành phần (Component Diagrams)
└── erd/                      # Biểu đồ thực thể quan hệ (ERD)
```

### 🎯 Các Loại Biểu Đồ Có Sẵn

#### ✅ Sequence Diagrams (Biểu đồ tuần tự)
- User Authentication Flow
- Admin Role Management Process  
- Shopping Cart Checkout Process
- Dashboard Analytics Loading
- Community Post Management
- Firebase Session Management
- Business Dashboard Access Flow

#### 📋 Biểu Đồ Cần Bổ Sung
- **Use Case Diagrams:** Hệ thống xác thực, Panel quản trị, Dashboard kinh doanh
- **Class Diagrams:** Controllers, Models, Middleware structure
- **Component Diagrams:** System architecture, Firebase integration
- **ERD:** Database schema và quan hệ giữa các bảng

### 🚀 Cách Render Biểu Đồ

#### 1. Sử dụng VS Code Extension
```bash
# Cài đặt PlantUML extension trong VS Code
# Mở file .puml và nhấn Alt+D để preview
```

#### 2. Sử dụng PlantUML JAR
```bash
# Tải PlantUML JAR
wget http://sourceforge.net/projects/plantuml/files/plantuml.jar/download -O plantuml.jar

# Render biểu đồ thành PNG
java -jar plantuml.jar -tpng docs/diagrams/sequence/seq_user_login.puml

# Render biểu đồ thành SVG
java -jar plantuml.jar -tsvg docs/diagrams/sequence/seq_user_login.puml
```

#### 3. Render Online
- Truy cập: https://www.plantuml.com/plantuml/uml/
- Copy/paste code PlantUML để render trực tuyến

### 📝 Quy Tắc Đặt Tên File

- **Sequence Diagrams:** `seq_[tên_chức_năng].puml`
- **Use Case Diagrams:** `uc_[tên_module].puml`  
- **Class Diagrams:** `class_[tên_module].puml`
- **Component Diagrams:** `comp_[tên_hệ_thống].puml`
- **ERD:** `erd_[tên_database].puml`

### 🎨 Styling Guidelines

Tất cả biểu đồ sử dụng theme màu nhất quán:
- **Nền:** `#FEFEFE` (Trắng nhẹ)
- **Participant:** `#E1F5FE` (Xanh nhạt)
- **Border:** `#01579B` (Xanh đậm)
- **Arrow:** `#1976D2` (Xanh chủ đạo)

### 🔗 Tham Khảo PlantUML

- [PlantUML Language Reference](https://plantuml.com/guide)
- [Sequence Diagram Syntax](https://plantuml.com/sequence-diagram)
- [Use Case Diagram Syntax](https://plantuml.com/use-case-diagram)
- [Class Diagram Syntax](https://plantuml.com/class-diagram)

## 🤝 Đóng góp

1. Fork project
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📧 Liên hệ

**Nhóm 6 - Lớp N05**
- **Môn:** Phân tích thiết kế phần mềm (PTTKPM25-26)
- **Repository:** [GitHub](https://github.com/longcodengu24/PTTKPM25-26_ClassN05_Nhom6)

## 📄 License

Dự án được phát hành dưới [MIT License](https://opensource.org/licenses/MIT).

# Work Order Management System

Work Order Management System adalah aplikasi berbasis Laravel untuk mengelola proses produksi dengan fitur work order tracking.

## 🚀 Instalasi dan Konfigurasi

Anda dapat menginstal aplikasi ini dengan dua metode berikut:

### **🔹 Cara 1: Menggunakan Database yang Sudah Ada**
1. **Clone Repository**
   ```sh
   git clone https://github.com/YusufJ12/WorkOrder.git
   ```
2. **Masuk ke Direktori Proyek**
   ```sh
   cd WorkOrder
   ```
3. **Instal Dependensi dengan Composer**
   ```sh
   composer install
   ```
4. **Salin File Konfigurasi .env**
   ```sh
   cp .env.example .env
   ```
5. **Buka XAMPP dan Aktifkan Apache & MySQL**
6. **Buat Database Baru di MySQL**
7. **Import Database dari Folder `DB`**
8. **Konfigurasi Database di `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```
9. **Generate Application Key**
   ```sh
   php artisan key:generate
   ```
10. **Jalankan Aplikasi**
    ```sh
    php artisan serve
    ```

---

### **🔹 Cara 2: Migrasi dan Seeder Database Baru**
1. **Clone Repository**
   ```sh
   git clone https://github.com/YusufJ12/WorkOrder.git
   ```
2. **Masuk ke Direktori Proyek**
   ```sh
   cd WorkOrder
   ```
3. **Instal Dependensi dengan Composer**
   ```sh
   composer install
   ```
4. **Salin File Konfigurasi .env**
   ```sh
   cp .env.example .env
   ```
5. **Konfigurasi Database di `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```
6. **Jalankan Migrasi Database**
   ```sh
   php artisan migrate
   ```
7. **Jalankan Seeder untuk Data Awal**
   ```sh
   php artisan db:seed --class=RolesTableSeeder
   php artisan db:seed --class=CreateUsersSeeder
   php artisan db:seed --class=ProductSeeder
   ```
8. **Generate Application Key**
   ```sh
   php artisan key:generate
   ```
9. **Jalankan Aplikasi**
   ```sh
   php artisan serve
   ```

---

## 🔑 **Akun Login Default**
Berikut adalah akun login default yang tersedia dalam sistem:

| **Role**     | **Email**                   | **Password** |
|-------------|----------------------------|-------------|
| Super Admin | `superadmin@example.com`   | `1234`      |
| Manager     | `manager@example.com`      | `1234`      |
| Operator    | `operator@example.com`     | `1234`      |

⚠️ **Pastikan untuk mengubah password setelah login pertama kali untuk keamanan sistem.**

---

## 🎯 **Fitur Utama**
✅ Manajemen Work Order (Tambah, Edit, Hapus, Tracking Status)  
✅ Pemantauan Proses Produksi  
✅ Hak Akses Berdasarkan Peran (Super Admin, Manager, Operator)  
✅ Export Data ke Excel  
✅ Sistem Notifikasi Status Work Order  

---

## 🛠 **Teknologi yang Digunakan**
- **Laravel** - Framework PHP
- **Blade** - Template Engine Laravel
- **Bootstrap** - Framework UI
- **jQuery & AJAX** - Interaksi Dinamis
- **SweetAlert2** - Notifikasi dan Alert
- **MySQL** - Database Management

---

## 📌 **Catatan**
- Pastikan Anda sudah menginstal **PHP 8+**, **Composer**, dan **MySQL** sebelum menjalankan proyek ini.
- Untuk pertanyaan atau kontribusi, silakan buat **issue** atau **pull request** di repository ini.

---

💡 **Selamat Menggunakan Work Order Management System! 🚀**

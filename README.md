Cara 1
1. git clone https://github.com/YusufJ12/WorkOrder.git
2. cd WorkOrder
3. composer install
4. cp .env.example .env
5. Open Xampp
6. Aktifkan Apache dan MySql
7. Buat Nama Database
8. Import Database dalam Folder DB
9. DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
10. php artisan key:generate
11. php artisan serve
   
Cara 2
1. git clone https://github.com/YusufJ12/WorkOrder.git
2. cd WorkOrder
3. composer install
4. cp .env.example .env
5. Masuk ENV
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
6. php artisan migrate
7. php artisan db:seed --class=RolesTableSeeder
8. php artisan db:seed --class=CreateUsersSeeder
9. php artisan db:seed --class=ProductSeeder
10. php artisan key:generate
11. php artisan serve
    

User                                      Password
superadmin@example.com                    1234
manager@example.com                       1234
operator@example.com                      1234

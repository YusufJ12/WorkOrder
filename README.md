1. git clone https://github.com/YusufJ12/WorkOrder.git
2. cd WorkOrder
3. composer install
4. cp .env.example .env
5. DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
6. php artisan key:generate
7. php artisan serve
   

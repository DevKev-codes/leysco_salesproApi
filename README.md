# 🧾 Leysco SalesPro API

This is a Laravel-based RESTful API for managing orders, products, customers, warehouses, inventory, analytics, and notifications for the SalesPro platform.

---

## 🚀 Features

- User authentication via Laravel Sanctum
- Sales order management
- Inventory tracking & reservations
- Customer categorization & credit limit management
- Multi-warehouse support & stock transfers
- Dashboard analytics (with Redis caching)
- Notification system (order, stock, credit)
- Modular and versioned API structure (v1)

---

## 🛠️ Tech Stack

- PHP 8.1+
- Laravel 10+
- MySQL
- Redis (for caching analytics)
- Sanctum (auth)

---

## ⚙️ Installation Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/leysco_salesproapi.git
cd leysco_salesproapi
2. Install Dependencies

composer install
3. Environment Setup
Copy .env.example to .env:


cp .env.example .env
Generate the application key:


php artisan key:generate
4. Configure .env
Update the following in .env:

env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=leysco_db
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
QUEUE_CONNECTION=sync
Make sure Redis is installed and running if you're using caching.

🧱 Database Setup
Run Migrations

php artisan migrate
Seed the Database

php artisan db:seed

▶️ Running the Server
Start the Laravel development server:

php artisan serve
The API will be accessible at:
http://127.0.0.1:8000/api/v1

🔐 Authentication
Auth endpoints: /api/v1/auth/login, /logout, etc.

Use Sanctum tokens for secured access.


app/Http/Controllers/Api/V1/ – All versioned API controllers

app/Models/ – Eloquent models

app/Helpers/ – Custom helpers like LeyscoHelpers.php

app/Http/Middleware/ – Custom middleware like LogApiActivity and CheckCreditLimit

🧪 Testing
To run tests:

php artisan test


📄 License
This project is licensed under the MIT License.

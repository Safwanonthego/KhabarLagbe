# KhabarLagbe

KhabarLagbe is a PHP food ordering platform with four user roles: Admin, Restaurant, Customer, and Rider. The project is structured in a lightweight MVC-style pattern and uses procedural PHP rather than object-oriented classes.

## Features
- Customer sign-up, login, and browsing
- Restaurant profiles with custom delivery fee
- Food menu management per restaurant
- Order validation and rider assignment
- Rider delivery updates and customer messaging
- Rider earnings summary based on:
  - customer tips
  - restaurant delivery fee
- Admin dashboard for user and payment oversight

## Setup
1. Put the project in your web root, for example:
   - Linux / XAMPP: `/opt/lampp/htdocs/KhabarLagbe`
2. Start Apache and MySQL.
3. Import `database.sql` into MySQL.
4. Open `http://localhost/KhabarLagbe/seed.php` once to create demo data.
5. Open `http://localhost/KhabarLagbe/` and log in.

## Demo accounts
- Admin: `admin@food.com` / `123456`
- Restaurant 1: `pizza@food.com` / `123456`
- Restaurant 2: `bengal@food.com` / `123456`
- Restaurant 3: `greenbowl@food.com` / `123456`
- Customer 1: `mehedi@food.com` / `123456`
- Customer 2: `nusrat@food.com` / `123456`
- Customer 3: `sami@food.com` / `123456`
- Rider 1: `rider1@food.com` / `123456`
- Rider 2: `rider2@food.com` / `123456`
- Rider 3: `rider3@food.com` / `123456`

## Database config
Edit `config/db.php` if your MySQL username/password differs.

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'food_ordering_system');
```

## Project structure

```text
KhabarLagbe/
├── ajax/
│   ├── assign_rider.php
│   ├── search_food.php
│   ├── send_message.php
│   └── update_delivery_status.php
├── assets/
│   ├── css/
│   └── js/
├── config/
│   └── db.php
├── controllers/
│   ├── AdminController.php
│   ├── AuthController.php
│   ├── BaseController.php
│   ├── CustomerController.php
│   ├── RestaurantController.php
│   └── RiderController.php
├── includes/
│   ├── auth_check.php
│   └── functions.php
├── models/
│   ├── feedback_model.php
│   ├── food_model.php
│   ├── message_model.php
│   ├── order_model.php
│   ├── payment_model.php
│   ├── restaurant_model.php
│   └── user_model.php
├── views/
│   ├── admin/
│   ├── auth/
│   ├── customer/
│   ├── includes/
│   ├── restaurant/
│   ├── rider/
│   └── landing.php
├── database.sql
├── index.php
├── README.md
├── seed.php
└── .gitignore
```

## Notes
- The project is procedural PHP and avoids class-based OOP.
- Each restaurant has its own delivery fee which contributes to rider earnings.
- Delete `seed.php` after the first run for production use.
- If your local setup uses a different Apache document root, adjust the URL accordingly.

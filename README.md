# KhabarLagbe

A PHP food ordering system with 4 roles: Admin, Restaurant, Customer, and Rider.

## Setup
1. Put the project in your web root, for example:
   - Linux / XAMPP: `/opt/lampp/htdocs/foodapp`
2. Start Apache and MySQL.
3. Import `database.sql` into MySQL.
4. Open `http://localhost/foodapp/seed.php` once to create demo users.
5. Open `http://localhost/foodapp/` and log in.

## Demo accounts
- Admin: `admin@food.com` / `123456`
- Restaurant: `restaurant@food.com` / `123456`
- Customer: `customer@food.com` / `123456`
- Rider: `rider@food.com` / `123456`

## Database config
Edit `config/db.php` if your MySQL username/password is different.

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'food_ordering_system');
```

## MVC folder structure

```text
foodapp/
├── config/
│   └── db.php
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
├── controllers/
│   ├── AdminController.php
│   ├── AuthController.php
│   ├── BaseController.php
│   ├── CustomerController.php
│   ├── RestaurantController.php
│   └── RiderController.php
├── views/
│   ├── admin/
│   ├── auth/
│   ├── customer/
│   ├── includes/
│   ├── restaurant/
│   ├── rider/
│   └── landing.php
├── ajax/
│   ├── assign_rider.php
│   ├── search_food.php
│   ├── send_message.php
│   └── update_delivery_status.php
├── assets/
│   ├── css/
│   └── js/
├── database.sql
├── index.php
├── README.md
├── seed.php
└── .gitignore
```

## Notes
- Delete `seed.php` after the first run.
- If you have a different Apache root, use that path instead of `/opt/lampp/htdocs/foodapp`.

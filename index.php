<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/controllers/BaseController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/RestaurantController.php';
require_once __DIR__ . '/controllers/CustomerController.php';
require_once __DIR__ . '/controllers/RiderController.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'login':
        login_controller($conn);
        break;

    case 'signup':
        signup_controller($conn);
        break;

    case 'logout':
        logout_controller($conn);
        break;

    case 'admin':
        require_role('admin');
        admin_controller($conn);
        break;

    case 'restaurant':
        require_role('restaurant');
        restaurant_controller($conn);
        break;

    case 'customer':
        require_role('customer');
        customer_controller($conn);
        break;

    case 'rider':
        require_role('rider');
        rider_controller($conn);
        break;

    case 'home':
    default:
        home_controller($conn);
        break;
}

mysqli_close($conn);

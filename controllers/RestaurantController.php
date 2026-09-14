<?php
require_once __DIR__ . '/../models/restaurant_model.php';
require_once __DIR__ . '/../models/food_model.php';
require_once __DIR__ . '/../models/order_model.php';
require_once __DIR__ . '/../models/payment_model.php';

function restaurant_controller($conn)
{
    require_role('restaurant');

    $action = $_GET['action'] ?? 'dashboard';

    switch ($action) {
        case 'foods':
            restaurant_foods_controller($conn);
            return;
        case 'orders':
            restaurant_orders_controller($conn);
            return;
        case 'assign_rider':
            restaurant_assign_rider_controller($conn);
            return;
        case 'dashboard':
        default:
            restaurant_dashboard_controller($conn);
            return;
    }
}

function restaurant_dashboard_controller($conn)
{
    $restaurant = get_restaurant_by_user_id($conn, current_user_id());
    $foods = $restaurant ? get_foods_by_restaurant($conn, $restaurant['id']) : [];
    $orders = $restaurant ? get_orders_by_restaurant($conn, $restaurant['id']) : [];
    $pending_count = count(array_filter($orders, fn($o) => $o['status'] === 'pending'));

    render_view('views/restaurant/dashboard.php', [
        'page_title' => 'Restaurant Dashboard',
        'asset_path' => '',
        'restaurant' => $restaurant,
        'foods' => $foods,
        'orders' => $orders,
        'pending_count' => $pending_count
    ]);
}

function restaurant_foods_controller($conn)
{
    $restaurant = get_restaurant_by_user_id($conn, current_user_id());
    if (!$restaurant) {
        set_flash('error', 'Restaurant profile missing.');
        redirect('index.php?page=restaurant');
    }

    $errors = [];
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($requestMethod === 'POST' && isset($_POST['action'])) {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errors[] = 'Invalid request.';
        } else {
            $name = sanitize($_POST['name'] ?? '');
            $description = sanitize($_POST['description'] ?? '');
            $price = $_POST['price'] ?? '';
            $category = sanitize($_POST['category'] ?? '');
            $status = sanitize($_POST['status'] ?? 'available');

            if ($name === '') $errors[] = 'Food name is required.';
            if (!is_numeric($price) || $price <= 0) $errors[] = 'Price must be a positive number.';
            if ($category === '') $errors[] = 'Category is required.';

            if (empty($errors)) {
                if ($_POST['action'] === 'add') {
                    add_food($conn, $restaurant['id'], $name, $description, (float)$price, $category, null);
                    set_flash('success', 'Food item added.');
                } elseif ($_POST['action'] === 'edit') {
                    update_food($conn, (int)$_POST['id'], $name, $description, (float)$price, $category, $status);
                    set_flash('success', 'Food item updated.');
                }
                redirect('index.php?page=restaurant&action=foods');
            }
        }
    }

    if (isset($_GET['delete'])) {
        delete_food($conn, (int)$_GET['delete']);
        set_flash('success', 'Food item deleted.');
        redirect('index.php?page=restaurant&action=foods');
    }

    $keyword = sanitize($_GET['keyword'] ?? '');
    $foods = get_foods_by_restaurant($conn, $restaurant['id'], $keyword);

    render_view('views/restaurant/foods.php', [
        'page_title' => 'Manage Foods',
        'asset_path' => '',
        'restaurant' => $restaurant,
        'foods' => $foods,
        'keyword' => $keyword,
        'errors' => $errors
    ]);
}

function restaurant_orders_controller($conn)
{
    $restaurant = get_restaurant_by_user_id($conn, current_user_id());
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($requestMethod === 'POST' && isset($_POST['action']) && $_POST['action'] === 'validate') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash('error', 'Invalid request.');
            redirect('index.php?page=restaurant&action=orders');
        }

        $order_id = (int)($_POST['order_id'] ?? 0);
        $decision = sanitize($_POST['decision'] ?? '');

        if (!in_array($decision, ['accepted', 'rejected', 'preparing'], true)) {
            set_flash('error', 'Invalid decision.');
            redirect('index.php?page=restaurant&action=orders');
        }

        update_order_validation($conn, $order_id, $decision);
        if ($decision === 'accepted') {
            $order = get_order_by_id($conn, $order_id);
            create_payment($conn, $order_id, $order['total_amount'] + $order['tip_amount'], 'cash');
        }
        set_flash('success', 'Order marked as ' . $decision . '.');
        redirect('index.php?page=restaurant&action=orders');
    }

    $keyword = sanitize($_GET['keyword'] ?? '');
    $orders = $restaurant ? get_orders_by_restaurant($conn, $restaurant['id'], $keyword) : [];

    render_view('views/restaurant/orders.php', [
        'page_title' => 'Order Validation',
        'asset_path' => '',
        'restaurant' => $restaurant,
        'orders' => $orders,
        'keyword' => $keyword
    ]);
}

function restaurant_assign_rider_controller($conn)
{
    $restaurant = get_restaurant_by_user_id($conn, current_user_id());
    $keyword = sanitize($_GET['keyword'] ?? '');
    $orders = $restaurant ? get_orders_by_restaurant($conn, $restaurant['id'], $keyword) : [];
    $riders = get_all_riders($conn);
    $assignable_orders = array_filter($orders, fn($o) => in_array($o['status'], ['accepted','preparing']) && $o['delivery_type'] === 'delivery');

    render_view('views/restaurant/assign_rider.php', [
        'page_title' => 'Assign Rider',
        'asset_path' => '',
        'restaurant' => $restaurant,
        'keyword' => $keyword,
        'orders' => $orders,
        'riders' => $riders,
        'assignable_orders' => $assignable_orders
    ]);
}

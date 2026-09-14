<?php
require_once __DIR__ . '/../models/order_model.php';
require_once __DIR__ . '/../models/message_model.php';
require_once __DIR__ . '/../models/feedback_model.php';

function customer_controller($conn)
{
    require_role('customer');

    $action = $_GET['action'] ?? 'dashboard';

    switch ($action) {
        case 'browse':
            customer_browse_controller($conn);
            return;
        case 'orders':
            customer_orders_controller($conn);
            return;
        case 'feedback':
            customer_feedback_controller($conn);
            return;
        case 'messages':
            customer_messages_controller($conn);
            return;
        case 'dashboard':
        default:
            customer_dashboard_controller($conn);
            return;
    }
}

function customer_dashboard_controller($conn)
{
    $orders = get_orders_by_customer($conn, current_user_id());
    $active_orders = count(array_filter($orders, fn($o) => !in_array($o['status'], ['delivered','picked_up','cancelled','rejected'])));

    render_view('views/customer/dashboard.php', [
        'page_title' => 'Customer Dashboard',
        'asset_path' => '',
        'orders' => $orders,
        'active_orders' => $active_orders
    ]);
}

function customer_browse_controller($conn)
{
    $errors = [];
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($requestMethod === 'POST' && isset($_POST['action']) && $_POST['action'] === 'order') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errors[] = 'Invalid request.';
        } else {
            $food_id = (int)($_POST['food_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 0);
            $delivery_type = sanitize($_POST['delivery_type'] ?? 'delivery');

            if ($quantity < 1) $errors[] = 'Quantity must be at least 1.';
            if (!in_array($delivery_type, ['pickup', 'delivery'], true)) $errors[] = 'Invalid delivery type.';

            $food = get_food_by_id($conn, $food_id);
            if (!$food || $food['status'] !== 'available') $errors[] = 'This food item is not available.';

            if (empty($errors)) {
                $total = $food['price'] * $quantity;
                $order_id = create_order($conn, current_user_id(), $food['restaurant_id'], $delivery_type, $total);
                add_order_item($conn, $order_id, $food_id, $quantity, $food['price']);
                set_flash('success', 'Order placed successfully! Track it in "My Orders".');
                redirect('index.php?page=customer&action=orders');
            }
        }
    }

    $keyword = sanitize($_GET['keyword'] ?? '');
    $foods = search_foods($conn, $keyword);

    render_view('views/customer/browse.php', [
        'page_title' => 'Browse Food',
        'asset_path' => '',
        'errors' => $errors,
        'keyword' => $keyword,
        'foods' => $foods
    ]);
}

function customer_orders_controller($conn)
{
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($requestMethod === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tip') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash('error', 'Invalid request.');
        } else {
            $order_id = (int)($_POST['order_id'] ?? 0);
            $tip = $_POST['tip_amount'] ?? '';
            if (!is_numeric($tip) || $tip < 0) {
                set_flash('error', 'Tip must be a valid non-negative number.');
            } else {
                set_order_tip($conn, $order_id, (float)$tip);
                set_flash('success', 'Thank you! Tip added.');
            }
        }
        redirect('index.php?page=customer&action=orders');
    }

    if ($requestMethod === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_pickup') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash('error', 'Invalid request.');
        } else {
            $order_id = (int)($_POST['order_id'] ?? 0);
            $order = get_order_by_id($conn, $order_id);

            if (!$order || $order['customer_id'] != current_user_id()) {
                set_flash('error', 'Order not found.');
            } elseif ($order['delivery_type'] !== 'pickup') {
                set_flash('error', 'This order is not a self-pickup order.');
            } elseif (in_array($order['status'], ['delivered', 'picked_up', 'cancelled', 'rejected'], true)) {
                set_flash('error', 'This order is already completed or cancelled.');
            } else {
                update_order_status($conn, $order_id, 'picked_up');
                set_flash('success', 'Pickup confirmed. Order completed.');
            }
        }
        redirect('index.php?page=customer&action=orders');
    }

    if (isset($_GET['delete'])) {
        delete_order($conn, (int)$_GET['delete']);
        set_flash('success', 'Order cancelled/deleted.');
        redirect('index.php?page=customer&action=orders');
    }

    $keyword = sanitize($_GET['keyword'] ?? '');
    $orders = get_orders_by_customer($conn, current_user_id(), $keyword);

    render_view('views/customer/orders.php', [
        'page_title' => 'My Orders',
        'asset_path' => '',
        'orders' => $orders,
        'keyword' => $keyword
    ]);
}

function customer_feedback_controller($conn)
{
    $errors = [];
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($requestMethod === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_feedback') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errors[] = 'Invalid request.';
        } else {
            $order_id = (int)($_POST['order_id'] ?? 0);
            $rating = (int)($_POST['rating'] ?? 0);
            $comment = sanitize($_POST['comment'] ?? '');

            if ($rating < 1 || $rating > 5) $errors[] = 'Rating must be between 1 and 5.';
            if ($comment === '') $errors[] = 'Comment is required.';

            $order = get_order_by_id($conn, $order_id);
            if (!$order || $order['customer_id'] != current_user_id()) $errors[] = 'Invalid order.';

            if (empty($errors)) {
                add_feedback($conn, current_user_id(), $order_id, $order['restaurant_id'], $rating, $comment);
                set_flash('success', 'Thank you for your feedback!');
                redirect('index.php?page=customer&action=feedback');
            }
        }
    }

    $delivered_orders = array_filter(
        get_orders_by_customer($conn, current_user_id()),
        fn($o) => in_array($o['status'], ['delivered', 'picked_up'])
    );
    $my_feedback = get_feedback_by_customer($conn, current_user_id());

    render_view('views/customer/feedback.php', [
        'page_title' => 'Rate & Review',
        'asset_path' => '',
        'errors' => $errors,
        'delivered_orders' => $delivered_orders,
        'my_feedback' => $my_feedback
    ]);
}

function customer_messages_controller($conn)
{
    $order_id = (int)($_GET['order_id'] ?? 0);
    $rider_id = (int)($_GET['rider_id'] ?? 0);
    $order = get_order_by_id($conn, $order_id);

    if (!$order || $order['customer_id'] != current_user_id()) {
        set_flash('error', 'Order not found.');
        redirect('index.php?page=customer&action=orders');
    }

    $messages = get_messages_for_order($conn, $order_id);

    render_view('views/customer/messages.php', [
        'page_title' => 'Message Rider',
        'asset_path' => '',
        'order_id' => $order_id,
        'rider_id' => $rider_id,
        'order' => $order,
        'messages' => $messages
    ]);
}

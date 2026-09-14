<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../models/order_model.php';
require_once __DIR__ . '/../models/restaurant_model.php';

require_role('restaurant');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method.');
}
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    json_response(false, 'Invalid CSRF token.');
}

$order_id = (int)($_POST['order_id'] ?? 0);
$rider_id = (int)($_POST['rider_id'] ?? 0);

if (!$order_id || !$rider_id) {
    json_response(false, 'Please select a valid rider.');
}

$restaurant = get_restaurant_by_user_id($conn, current_user_id());
$order = get_order_by_id($conn, $order_id);

if (!$order || !$restaurant || $order['restaurant_id'] != $restaurant['id']) {
    json_response(false, 'You are not authorized to modify this order.');
}

$ok = assign_rider_to_order($conn, $order_id, $rider_id);

if ($ok) {
    json_response(true, 'Rider assigned successfully.');
} else {
    json_response(false, 'Failed to assign rider.');
}

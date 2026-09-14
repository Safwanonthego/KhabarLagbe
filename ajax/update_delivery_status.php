<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../models/order_model.php';

require_role('rider');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method.');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    json_response(false, 'Invalid CSRF token.');
}

$order_id = (int)($_POST['order_id'] ?? 0);
$status = sanitize($_POST['status'] ?? '');

$allowed = ['assigned', 'on_the_way', 'delivered', 'picked_up'];
if (!in_array($status, $allowed, true)) {
    json_response(false, 'Invalid status value.');
}

$order = get_order_by_id($conn, $order_id);
if (!$order || $order['rider_id'] != current_user_id()) {
    json_response(false, 'This order is not assigned to you.');
}

if (in_array($order['status'], ['delivered', 'picked_up'], true) && $status !== $order['status']) {
    json_response(false, 'This order is already completed and cannot be reopened.');
}

$transitions = [
    'assigned' => ['assigned', 'on_the_way'],
    'on_the_way' => ['on_the_way', 'delivered', 'picked_up'],
    'delivered' => ['delivered'],
    'picked_up' => ['picked_up'],
];
$current = $order['status'];
if (!isset($transitions[$current]) || !in_array($status, $transitions[$current], true)) {
    json_response(false, 'Status transition is not allowed.');
}

$ok = update_delivery_status($conn, $order_id, current_user_id(), $status);

if ($ok) {
    json_response(true, 'Delivery status updated to ' . $status . '.');
} else {
    json_response(false, 'Failed to update status.');
}

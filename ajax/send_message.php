<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../models/message_model.php';
require_once __DIR__ . '/../models/order_model.php';

require_role(['customer', 'rider']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method.');
}
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    json_response(false, 'Invalid CSRF token.');
}

$order_id = (int)($_POST['order_id'] ?? 0);
$receiver_id = (int)($_POST['receiver_id'] ?? 0);
$message = sanitize($_POST['message'] ?? '');

if ($message === '') {
    json_response(false, 'Message cannot be empty.');
}

$order = get_order_by_id($conn, $order_id);
$user_id = current_user_id();
$role = current_role();

$authorized = $order && (
    ($role === 'customer' && $order['customer_id'] == $user_id) ||
    ($role === 'rider' && $order['rider_id'] == $user_id)
);

if (!$authorized) {
    json_response(false, 'You are not part of this order conversation.');
}

$ok = send_message($conn, $order_id, $user_id, $receiver_id, $message);

if ($ok) {
    json_response(true, 'Message sent.');
} else {
    json_response(false, 'Failed to send message.');
}

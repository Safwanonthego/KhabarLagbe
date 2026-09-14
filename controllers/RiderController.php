<?php
require_once __DIR__ . '/../models/order_model.php';
require_once __DIR__ . '/../models/message_model.php';

function rider_controller($conn)
{
    require_role('rider');

    $action = $_GET['action'] ?? 'dashboard';

    switch ($action) {
        case 'deliveries':
            rider_deliveries_controller($conn);
            return;
        case 'messages':
            rider_messages_controller($conn);
            return;
        case 'earnings':
            rider_earnings_controller($conn);
            return;
        case 'dashboard':
        default:
            rider_dashboard_controller($conn);
            return;
    }
}

function rider_dashboard_controller($conn)
{
    $orders = get_orders_by_rider($conn, current_user_id());
    $assigned_count = count(array_filter($orders, fn($o) => $o['status'] === 'assigned'));
    $active = count(array_filter($orders, fn($o) => in_array($o['status'], ['assigned', 'on_the_way'])));
    $completed = count(array_filter($orders, fn($o) => in_array($o['status'], ['delivered', 'picked_up'])));

    render_view('views/rider/dashboard.php', [
        'page_title' => 'Rider Dashboard',
        'asset_path' => '',
        'orders' => $orders,
        'assigned_count' => $assigned_count,
        'active' => $active,
        'completed' => $completed
    ]);
}

function rider_deliveries_controller($conn)
{
    $keyword = sanitize($_GET['keyword'] ?? '');
    $orders = get_orders_by_rider($conn, current_user_id(), $keyword);

    render_view('views/rider/deliveries.php', [
        'page_title' => 'Assigned Deliveries',
        'asset_path' => '',
        'orders' => $orders,
        'keyword' => $keyword
    ]);
}

function rider_messages_controller($conn)
{
    $order_id = (int)($_GET['order_id'] ?? 0);
    $customer_id = (int)($_GET['customer_id'] ?? 0);
    $orders = get_orders_by_rider($conn, current_user_id());
    $messages = $order_id ? get_messages_for_order($conn, $order_id) : [];

    render_view('views/rider/messages.php', [
        'page_title' => 'Message Customers',
        'asset_path' => '',
        'order_id' => $order_id,
        'customer_id' => $customer_id,
        'orders' => $orders,
        'messages' => $messages
    ]);
}

function rider_earnings_controller($conn)
{
    $summary = get_rider_earnings_summary($conn, current_user_id());
    $orders = $summary['orders'];

    render_view('views/rider/earnings.php', [
        'page_title' => 'Earnings Summary',
        'asset_path' => '',
        'orders' => $orders,
        'summary' => $summary,
        'total_earnings' => $summary['total_earnings'],
        'completed_orders' => $summary['completed_orders'],
        'active_orders' => $summary['active_orders'],
    ]);
}

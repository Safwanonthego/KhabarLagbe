<?php
require_once __DIR__ . '/../models/user_model.php';
require_once __DIR__ . '/../models/order_model.php';
require_once __DIR__ . '/../models/feedback_model.php';
require_once __DIR__ . '/../models/payment_model.php';

function admin_controller($conn)
{
    require_role('admin');

    $action = $_GET['action'] ?? 'dashboard';

    switch ($action) {
        case 'users':
            admin_users_controller($conn);
            return;
        case 'revenue':
            admin_revenue_controller($conn);
            return;
        case 'feedback':
            admin_feedback_controller($conn);
            return;
        case 'payments':
            admin_payments_controller($conn);
            return;
        case 'orders':
            admin_orders_controller($conn);
            return;
        case 'dashboard':
        default:
            admin_dashboard_controller($conn);
            return;
    }
}

function admin_dashboard_controller($conn)
{
    $revenue = get_total_revenue($conn);
    $total_users = count(get_all_users($conn));
    $total_orders = count(get_all_orders($conn));
    $total_feedback = count(get_all_feedback($conn));

    render_view('views/admin/dashboard.php', [
        'page_title' => 'Admin Dashboard',
        'asset_path' => '',
        'revenue' => $revenue,
        'total_users' => $total_users,
        'total_orders' => $total_orders,
        'total_feedback' => $total_feedback
    ]);
}

function admin_users_controller($conn)
{
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($requestMethod === 'POST' && isset($_POST['action'])) {
        admin_handle_user_action($conn);
    }

    if (isset($_GET['delete'])) {
        admin_delete_user($conn, (int)$_GET['delete']);
    }

    if (isset($_GET['toggle_status'])) {
        admin_toggle_user_status($conn, (int)$_GET['toggle_status']);
    }

    $keyword = sanitize($_GET['keyword'] ?? '');
    $role_filter = sanitize($_GET['role'] ?? '');
    $users = get_all_users($conn, $role_filter ?: null, $keyword);

    render_view('views/admin/users.php', [
        'page_title' => 'Manage Users',
        'asset_path' => '',
        'keyword' => $keyword,
        'role_filter' => $role_filter,
        'users' => $users
    ]);
}

function admin_revenue_controller($conn)
{
    $total = get_total_revenue($conn);
    $by_restaurant = get_revenue_by_restaurant($conn);

    render_view('views/admin/revenue.php', [
        'page_title' => 'Revenue Report',
        'asset_path' => '',
        'total' => $total,
        'by_restaurant' => $by_restaurant
    ]);
}

function admin_feedback_controller($conn)
{
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($requestMethod === 'POST' && isset($_POST['action']) && $_POST['action'] === 'act') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash('error', 'Invalid request.');
        } else {
            $feedback_id = (int)($_POST['feedback_id'] ?? 0);
            $decision = sanitize($_POST['decision'] ?? '');
            $restaurant_id = (int)($_POST['restaurant_id'] ?? 0);
            $restaurant = get_restaurant_by_id($conn, $restaurant_id);
            $restaurant_user_id = $restaurant['user_id'] ?? null;

            if (in_array($decision, ['warned', 'suspended', 'resolved'], true)) {
                update_feedback_action($conn, $feedback_id, $decision, $decision === 'suspended' ? $restaurant_user_id : null);
                set_flash('success', 'Action recorded: ' . $decision);
            } else {
                set_flash('error', 'Invalid decision.');
            }
        }
        redirect('index.php?page=admin&action=feedback');
    }

    if (isset($_GET['delete'])) {
        delete_feedback($conn, (int)$_GET['delete']);
        set_flash('success', 'Feedback removed.');
        redirect('index.php?page=admin&action=feedback');
    }

    $keyword = sanitize($_GET['keyword'] ?? '');
    $feedback_list = get_all_feedback($conn, $keyword);

    render_view('views/admin/feedback_actions.php', [
        'page_title' => 'Feedback Actions',
        'asset_path' => '',
        'keyword' => $keyword,
        'feedback_list' => $feedback_list
    ]);
}

function admin_payments_controller($conn)
{
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($requestMethod === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash('error', 'Invalid request.');
        } else {
            $id = (int)($_POST['id'] ?? 0);
            $status = sanitize($_POST['status'] ?? '');
            $txn = sanitize($_POST['transaction_id'] ?? '');

            if (in_array($status, ['pending', 'paid', 'refunded'], true)) {
                update_payment_status($conn, $id, $status, $txn ?: null);
                set_flash('success', 'Payment status updated.');
            } else {
                set_flash('error', 'Invalid status.');
            }
        }
        redirect('index.php?page=admin&action=payments');
    }

    if (isset($_GET['delete'])) {
        delete_payment($conn, (int)$_GET['delete']);
        set_flash('success', 'Payment record deleted.');
        redirect('index.php?page=admin&action=payments');
    }

    $keyword = sanitize($_GET['keyword'] ?? '');
    $payments = get_all_payments($conn, $keyword);

    render_view('views/admin/payments.php', [
        'page_title' => 'Payment Management',
        'asset_path' => '',
        'keyword' => $keyword,
        'payments' => $payments
    ]);
}

function admin_orders_controller($conn)
{
    if (isset($_GET['delete'])) {
        delete_order($conn, (int)$_GET['delete']);
        set_flash('success', 'Order deleted.');
        redirect('index.php?page=admin&action=orders');
    }

    $keyword = sanitize($_GET['keyword'] ?? '');
    $orders = get_all_orders($conn, $keyword);

    render_view('views/admin/orders.php', [
        'page_title' => 'All Orders',
        'asset_path' => '',
        'keyword' => $keyword,
        'orders' => $orders
    ]);
}

function admin_handle_user_action($conn)
{
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Invalid request.');
        redirect('index.php?page=admin&action=users');
    }

    $action = $_POST['action'] ?? '';
    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');

        if ($id <= 0 || $name === '') {
            set_flash('error', 'Name is required.');
            redirect('index.php?page=admin&action=users');
        }

        if ((int)($_SESSION['user_id'] ?? 0) === $id) {
            set_flash('error', 'You cannot edit your own account here.');
            redirect('index.php?page=admin&action=users');
        }

        update_user($conn, $id, $name, $phone, $address);
        set_flash('success', 'User updated successfully.');
        redirect('index.php?page=admin&action=users');
    }
}

function admin_delete_user($conn, $id)
{
    if ($id <= 0) {
        set_flash('error', 'Invalid user id.');
        redirect('index.php?page=admin&action=users');
    }

    if ((int)($_SESSION['user_id'] ?? 0) === $id) {
        set_flash('error', 'You cannot delete your own account.');
        redirect('index.php?page=admin&action=users');
    }

    delete_user($conn, $id);
    set_flash('success', 'User deleted.');
    redirect('index.php?page=admin&action=users');
}

function admin_toggle_user_status($conn, $id)
{
    if ($id <= 0) {
        set_flash('error', 'Invalid user id.');
        redirect('index.php?page=admin&action=users');
    }

    if ((int)($_SESSION['user_id'] ?? 0) === $id) {
        set_flash('error', 'You cannot suspend or activate your own account.');
        redirect('index.php?page=admin&action=users');
    }

    $user = get_user_by_id($conn, $id);
    if (!$user) {
        set_flash('error', 'User not found.');
        redirect('index.php?page=admin&action=users');
    }

    $new_status = $user['status'] === 'active' ? 'suspended' : 'active';
    set_user_status($conn, $id, $new_status);
    set_flash('success', 'User status updated.');
    redirect('index.php?page=admin&action=users');
}

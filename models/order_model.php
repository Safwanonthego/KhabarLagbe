<?php

require_once __DIR__ . '/../config/db.php';


function create_order($conn, $customer_id, $restaurant_id, $delivery_type, $total_amount) {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO orders (customer_id, restaurant_id, delivery_type, total_amount) VALUES (?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "iisd", $customer_id, $restaurant_id, $delivery_type, $total_amount);
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($conn);
}

function add_order_item($conn, $order_id, $food_id, $quantity, $price) {
    $stmt = mysqli_prepare($conn, "INSERT INTO order_items (order_id, food_id, quantity, price) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iiid", $order_id, $food_id, $quantity, $price);
    return mysqli_stmt_execute($stmt);
}

function get_order_by_id($conn, $id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function get_order_items($conn, $order_id) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT oi.*, f.name AS food_name FROM order_items oi
         JOIN foods f ON oi.food_id = f.id WHERE oi.order_id = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}


function get_orders_by_customer($conn, $customer_id, $keyword = '') {
    $sql = "SELECT o.*, r.restaurant_name FROM orders o
            JOIN restaurants r ON o.restaurant_id = r.id
            WHERE o.customer_id = ?";
    $types = "i";
    $params = [$customer_id];
    if ($keyword !== '') {
        $sql .= " AND (r.restaurant_name LIKE ? OR o.status LIKE ?)";
        $types .= "ss";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }
    $sql .= " ORDER BY o.id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}


function get_orders_by_restaurant($conn, $restaurant_id, $keyword = '') {
    $sql = "SELECT o.*, u.name AS customer_name FROM orders o
            JOIN users u ON o.customer_id = u.id
            WHERE o.restaurant_id = ?";
    $types = "i";
    $params = [$restaurant_id];
    if ($keyword !== '') {
        $sql .= " AND (u.name LIKE ? OR o.status LIKE ?)";
        $types .= "ss";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }
    $sql .= " ORDER BY o.id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}


function get_orders_by_rider($conn, $rider_id, $keyword = '') {
    $sql = "SELECT o.*, u.name AS customer_name, u.address, r.restaurant_name FROM orders o
            JOIN users u ON o.customer_id = u.id
            JOIN restaurants r ON o.restaurant_id = r.id
            WHERE o.rider_id = ?";
    $types = "i";
    $params = [$rider_id];
    if ($keyword !== '') {
        $sql .= " AND (u.name LIKE ? OR o.status LIKE ?)";
        $types .= "ss";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }
    $sql .= " ORDER BY o.id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}


function get_all_orders($conn, $keyword = '') {
    $sql = "SELECT o.*, u.name AS customer_name, r.restaurant_name FROM orders o
            JOIN users u ON o.customer_id = u.id
            JOIN restaurants r ON o.restaurant_id = r.id WHERE 1=1";
    $types = '';
    $params = [];
    if ($keyword !== '') {
        $sql .= " AND (u.name LIKE ? OR r.restaurant_name LIKE ? OR o.status LIKE ?)";
        $types .= 'sss';
        $like = "%$keyword%";
        $params = [$like, $like, $like];
    }
    $sql .= " ORDER BY o.id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}


function update_order_validation($conn, $order_id, $status) {
    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    return mysqli_stmt_execute($stmt);
}


function assign_rider_to_order($conn, $order_id, $rider_id) {
    $stmt = mysqli_prepare($conn, "UPDATE orders SET rider_id = ?, status = 'assigned' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $rider_id, $order_id);
    return mysqli_stmt_execute($stmt);
}


function update_order_status($conn, $order_id, $status) {
    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    return mysqli_stmt_execute($stmt);
}

function update_delivery_status($conn, $order_id, $rider_id, $status) {
    $order = get_order_by_id($conn, $order_id);
    if (!$order || $order['rider_id'] != $rider_id) {
        return false;
    }

    if (in_array($order['status'], ['delivered', 'picked_up'], true) && $status !== $order['status']) {
        return false;
    }

    $transitions = [
        'assigned' => ['assigned', 'on_the_way'],
        'on_the_way' => ['on_the_way', 'delivered', 'picked_up'],
        'delivered' => ['delivered'],
        'picked_up' => ['picked_up'],
    ];

    if (!isset($transitions[$order['status']]) || !in_array($status, $transitions[$order['status']], true)) {
        return false;
    }

    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ? AND rider_id = ?");
    mysqli_stmt_bind_param($stmt, "sii", $status, $order_id, $rider_id);
    $ok = mysqli_stmt_execute($stmt);

    $stmt2 = mysqli_prepare($conn, "INSERT INTO delivery_status_log (order_id, rider_id, status) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt2, "iis", $order_id, $rider_id, $status);
    mysqli_stmt_execute($stmt2);

    return $ok;
}


function set_order_tip($conn, $order_id, $tip_amount) {
    $stmt = mysqli_prepare($conn, "UPDATE orders SET tip_amount = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "di", $tip_amount, $order_id);
    return mysqli_stmt_execute($stmt);
}

function get_rider_earnings_summary($conn, $rider_id) {
    $sql = "SELECT o.*, u.name AS customer_name, r.restaurant_name, r.delivery_fee,
            (COALESCE(o.tip_amount, 0) + COALESCE(r.delivery_fee, 0)) AS rider_earning
            FROM orders o
            JOIN users u ON o.customer_id = u.id
            JOIN restaurants r ON o.restaurant_id = r.id
            WHERE o.rider_id = ?
            ORDER BY o.id DESC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $rider_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $orders = [];
    $total_earnings = 0.0;
    $completed_orders = 0;
    $active_orders = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $row['rider_earning'] = (float)($row['rider_earning'] ?? 0);
        $orders[] = $row;
        $total_earnings += $row['rider_earning'];

        if (in_array($row['status'], ['delivered', 'picked_up'], true)) {
            $completed_orders++;
        }

        if (in_array($row['status'], ['assigned', 'on_the_way'], true)) {
            $active_orders++;
        }
    }

    return [
        'orders' => $orders,
        'total_earnings' => $total_earnings,
        'completed_orders' => $completed_orders,
        'active_orders' => $active_orders,
    ];
}

function delete_order($conn, $id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM orders WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt);
}


function get_total_revenue($conn) {
    $result = mysqli_query($conn, "SELECT SUM(total_amount) AS revenue, COUNT(*) AS total_orders FROM orders WHERE status IN ('delivered','picked_up')");
    return mysqli_fetch_assoc($result);
}

function get_revenue_by_restaurant($conn) {
    $sql = "SELECT r.restaurant_name, SUM(o.total_amount) AS revenue, COUNT(o.id) AS total_orders
            FROM orders o JOIN restaurants r ON o.restaurant_id = r.id
            WHERE o.status IN ('delivered','picked_up')
            GROUP BY r.id ORDER BY revenue DESC";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

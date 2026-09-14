<?php

require_once __DIR__ . '/../config/db.php';

function add_feedback($conn, $customer_id, $order_id, $restaurant_id, $rating, $comment) {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO feedback (customer_id, order_id, restaurant_id, rating, comment) VALUES (?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "iiiis", $customer_id, $order_id, $restaurant_id, $rating, $comment);
    return mysqli_stmt_execute($stmt);
}

function get_all_feedback($conn, $keyword = '') {
    $sql = "SELECT f.*, u.name AS customer_name, r.restaurant_name FROM feedback f
            JOIN users u ON f.customer_id = u.id
            JOIN restaurants r ON f.restaurant_id = r.id WHERE 1=1";
    $types = '';
    $params = [];
    if ($keyword !== '') {
        $sql .= " AND (u.name LIKE ? OR r.restaurant_name LIKE ? OR f.comment LIKE ?)";
        $types .= 'sss';
        $like = "%$keyword%";
        $params = [$like, $like, $like];
    }
    $sql .= " ORDER BY f.id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function get_feedback_by_customer($conn, $customer_id) {
    $stmt = mysqli_prepare($conn, "SELECT f.*, r.restaurant_name FROM feedback f
            JOIN restaurants r ON f.restaurant_id = r.id WHERE f.customer_id = ? ORDER BY f.id DESC");
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}


function update_feedback_action($conn, $feedback_id, $action, $restaurant_user_id = null) {
    $stmt = mysqli_prepare($conn, "UPDATE feedback SET admin_action = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $action, $feedback_id);
    $ok = mysqli_stmt_execute($stmt);

    if ($action === 'suspended' && $restaurant_user_id) {
        $stmt2 = mysqli_prepare($conn, "UPDATE users SET status = 'suspended' WHERE id = ?");
        mysqli_stmt_bind_param($stmt2, "i", $restaurant_user_id);
        mysqli_stmt_execute($stmt2);
    }
    return $ok;
}

function delete_feedback($conn, $id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM feedback WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt);
}

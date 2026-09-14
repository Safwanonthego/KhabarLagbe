<?php

require_once __DIR__ . '/../config/db.php';

function create_payment($conn, $order_id, $amount, $method) {
    $stmt = mysqli_prepare($conn, "INSERT INTO payments (order_id, amount, method, status) VALUES (?, ?, ?, 'pending')");
    mysqli_stmt_bind_param($stmt, "ids", $order_id, $amount, $method);
    return mysqli_stmt_execute($stmt);
}

function get_all_payments($conn, $keyword = '') {
    $sql = "SELECT p.*, u.name AS customer_name FROM payments p
            JOIN orders o ON p.order_id = o.id
            JOIN users u ON o.customer_id = u.id WHERE 1=1";
    $types = '';
    $params = [];
    if ($keyword !== '') {
        $sql .= " AND (u.name LIKE ? OR p.status LIKE ? OR p.method LIKE ?)";
        $types .= 'sss';
        $like = "%$keyword%";
        $params = [$like, $like, $like];
    }
    $sql .= " ORDER BY p.id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}


function update_payment_status($conn, $id, $status, $transaction_id = null) {
    $stmt = mysqli_prepare($conn, "UPDATE payments SET status = ?, transaction_id = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $status, $transaction_id, $id);
    return mysqli_stmt_execute($stmt);
}

function delete_payment($conn, $id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM payments WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt);
}

<?php

require_once __DIR__ . '/../config/db.php';

function create_restaurant_profile($conn, $user_id, $restaurant_name, $description = '', $delivery_fee = 0.00) {
    $stmt = mysqli_prepare($conn, "INSERT INTO restaurants (user_id, restaurant_name, description, delivery_fee) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isid", $user_id, $restaurant_name, $description, $delivery_fee);
    return mysqli_stmt_execute($stmt);
}

function get_restaurant_by_user_id($conn, $user_id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM restaurants WHERE user_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function get_restaurant_by_id($conn, $id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM restaurants WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

function get_all_restaurants($conn, $keyword = '') {
    $sql = "SELECT r.*, u.status FROM restaurants r JOIN users u ON r.user_id = u.id WHERE 1=1";
    $params = [];
    $types = '';
    if ($keyword !== '') {
        $sql .= " AND r.restaurant_name LIKE ?";
        $types .= 's';
        $params[] = "%$keyword%";
    }
    $stmt = mysqli_prepare($conn, $sql);
    if ($types) mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function update_restaurant_profile($conn, $id, $name, $description, $delivery_fee = 0.00) {
    $stmt = mysqli_prepare($conn, "UPDATE restaurants SET restaurant_name = ?, description = ?, delivery_fee = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssdi", $name, $description, $delivery_fee, $id);
    return mysqli_stmt_execute($stmt);
}

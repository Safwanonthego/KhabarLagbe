<?php

require_once __DIR__ . '/../config/db.php';

function add_food($conn, $restaurant_id, $name, $description, $price, $category, $image) {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO foods (restaurant_id, name, description, price, category, image) VALUES (?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "issdss", $restaurant_id, $name, $description, $price, $category, $image);
    return mysqli_stmt_execute($stmt);
}

function update_food($conn, $id, $name, $description, $price, $category, $status) {
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE foods SET name = ?, description = ?, price = ?, category = ?, status = ? WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, "ssdssi", $name, $description, $price, $category, $status, $id);
    return mysqli_stmt_execute($stmt);
}

function delete_food($conn, $id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM foods WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt);
}

function get_food_by_id($conn, $id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM foods WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}


function get_foods_by_restaurant($conn, $restaurant_id, $keyword = '') {
    $sql = "SELECT * FROM foods WHERE restaurant_id = ?";
    $types = "i";
    $params = [$restaurant_id];
    if ($keyword !== '') {
        $sql .= " AND (name LIKE ? OR category LIKE ?)";
        $types .= "ss";
        $params[] = "%$keyword%";
        $params[] = "%$keyword%";
    }
    $sql .= " ORDER BY id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}


function search_foods($conn, $keyword = '') {
    $sql = "SELECT f.*, r.restaurant_name FROM foods f
            JOIN restaurants r ON f.restaurant_id = r.id
            WHERE f.status = 'available'";
    $types = '';
    $params = [];
    if ($keyword !== '') {
        $sql .= " AND (f.name LIKE ? OR f.category LIKE ? OR r.restaurant_name LIKE ?)";
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

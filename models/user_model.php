<?php

require_once __DIR__ . '/../config/db.php';


function get_user_by_email($conn, $email) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}


function get_user_by_id($conn, $id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}


function create_user($conn, $name, $email, $password, $role, $phone, $address) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $hashed, $role, $phone, $address);
    return mysqli_stmt_execute($stmt);
}


function attempt_login($conn, $email, $password) {
    $user = get_user_by_email($conn, $email);
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}


function get_all_users($conn, $role = null, $keyword = '') {
    $sql = "SELECT * FROM users WHERE 1=1";
    $params = [];
    $types = '';

    if ($role) {
        $sql .= " AND role = ?";
        $types .= 's';
        $params[] = $role;
    }
    if ($keyword !== '') {
        $sql .= " AND (name LIKE ? OR email LIKE ?)";
        $types .= 'ss';
        $like = "%$keyword%";
        $params[] = $like;
        $params[] = $like;
    }
    $sql .= " ORDER BY id DESC";

    $stmt = mysqli_prepare($conn, $sql);
    if ($types) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}


function update_user($conn, $id, $name, $phone, $address) {
    $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $name, $phone, $address, $id);
    return mysqli_stmt_execute($stmt);
}


function set_user_status($conn, $id, $status) {
    $stmt = mysqli_prepare($conn, "UPDATE users SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    return mysqli_stmt_execute($stmt);
}


function delete_user($conn, $id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt);
}


function get_all_riders($conn) {
    $result = mysqli_query($conn, "SELECT * FROM users WHERE role = 'rider' AND status = 'active'");
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

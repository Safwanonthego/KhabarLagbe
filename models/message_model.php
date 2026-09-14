<?php

require_once __DIR__ . '/../config/db.php';

function send_message($conn, $order_id, $sender_id, $receiver_id, $message) {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO messages (order_id, sender_id, receiver_id, message) VALUES (?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "iiis", $order_id, $sender_id, $receiver_id, $message);
    return mysqli_stmt_execute($stmt);
}

function get_messages_for_order($conn, $order_id) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT m.*, u.name AS sender_name FROM messages m
         JOIN users u ON m.sender_id = u.id
         WHERE m.order_id = ? ORDER BY m.created_at ASC"
    );
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
    return $rows;
}

function delete_message($conn, $id) {
    $stmt = mysqli_prepare($conn, "DELETE FROM messages WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt);
}

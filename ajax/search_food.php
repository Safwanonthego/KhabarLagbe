<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../models/food_model.php';

require_login();

$keyword = sanitize($_GET['keyword'] ?? '');
$foods = search_foods($conn, $keyword);

ob_start();
foreach ($foods as $f) {
    echo '<div class="food-card">';
    echo '<h4>' . h($f['name']) . '</h4>';
    echo '<div class="restaurant-name">' . h($f['restaurant_name']) . ' • ' . h($f['category']) . '</div>';
    echo '<div class="price">৳' . number_format($f['price'], 2) . '</div>';
    echo '<button class="btn btn-small" onclick="openOrderModal(' . $f['id'] . ', \'' . h(addslashes($f['name'])) . '\', ' . $f['price'] . ')">Order Now</button>';
    echo '</div>';
}
if (empty($foods)) {
    echo '<p>No food items found.</p>';
}
$html = ob_get_clean();

json_response(true, 'ok', ['html' => $html]);

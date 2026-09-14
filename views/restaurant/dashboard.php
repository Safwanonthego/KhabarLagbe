<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1><?php echo h($restaurant['restaurant_name'] ?? 'Restaurant'); ?> Dashboard</h1>
    </div>

    <div class="stats-grid">
        <div class="stat-card"><h3><?php echo count($foods); ?></h3><p>Menu Items</p></div>
        <div class="stat-card"><h3><?php echo count($orders); ?></h3><p>Total Orders</p></div>
        <div class="stat-card"><h3>৳<?php echo number_format((float)($restaurant['delivery_fee'] ?? 0), 2); ?></h3><p>Delivery Fee</p></div>
        <div class="stat-card"><h3><?php echo $pending_count; ?></h3><p>Pending Validation</p></div>
    </div>

    <div class="card-box">
        <h2>Quick Links (Restaurant Unique Features)</h2>
        <div class="actions-cell">
            <a href="index.php?page=restaurant&action=foods" class="btn">🍕 Add/Edit/Delete Food</a>
            <a href="index.php?page=restaurant&action=orders" class="btn btn-secondary">✅ Order Validation</a>
            <a href="index.php?page=restaurant&action=assign_rider" class="btn btn-secondary">🛵 Assign Delivery Rider</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

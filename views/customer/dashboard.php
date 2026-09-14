<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header"><h1>Welcome, <?php echo h($_SESSION['name']); ?>!</h1></div>

    <div class="stats-grid">
        <div class="stat-card"><h3><?php echo count($orders); ?></h3><p>Total Orders</p></div>
        <div class="stat-card"><h3><?php echo $active_orders; ?></h3><p>Active Orders</p></div>
    </div>

    <div class="card-box">
        <h2>Quick Links (Customer Unique Features)</h2>
        <div class="actions-cell">
            <a href="index.php?page=customer&action=browse" class="btn">🍽️ Browse & Order Food</a>
            <a href="index.php?page=customer&action=orders" class="btn btn-secondary">📦 My Orders (Tip, Pickup/Delivery)</a>
            <a href="index.php?page=customer&action=feedback" class="btn btn-secondary">⭐ Rate & Review</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

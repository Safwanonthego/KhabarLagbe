<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header"><h1>Welcome, <?php echo h($_SESSION['name']); ?>! 🛵</h1></div>

    <div class="stats-grid">
        <div class="stat-card"><h3><?php echo count($orders); ?></h3><p>Total Assigned</p></div>
        <div class="stat-card"><h3><?php echo $active; ?></h3><p>Active Deliveries</p></div>
        <div class="stat-card"><h3><?php echo $completed; ?></h3><p>Completed</p></div>
    </div>

    <div class="card-box">
        <h2>Quick Links (Rider Unique Features)</h2>
        <div class="actions-cell">
            <a href="index.php?page=rider&action=deliveries" class="btn">📋 Assigned Deliveries List</a>
            <a href="index.php?page=rider&action=messages" class="btn btn-secondary">💬 Message Customers</a>
            <a href="index.php?page=rider&action=earnings" class="btn btn-secondary">💰 Earnings Summary</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

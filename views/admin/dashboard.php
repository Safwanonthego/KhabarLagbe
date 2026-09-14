<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Manage users, monitor revenue, act on feedback, and handle payments.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card"><h3>৳<?php echo number_format($revenue['revenue'] ?? 0, 2); ?></h3><p>Total Revenue</p></div>
        <div class="stat-card"><h3><?php echo $revenue['total_orders'] ?? 0; ?></h3><p>Completed Orders</p></div>
        <div class="stat-card"><h3><?php echo $total_users; ?></h3><p>Total Users</p></div>
        <div class="stat-card"><h3><?php echo $total_feedback; ?></h3><p>Feedback Received</p></div>
    </div>

    <div class="card-box">
        <h2>Quick Links (Admin Unique Features)</h2>
        <div class="actions-cell">
            <a href="index.php?page=admin&action=revenue" class="btn">📊 Revenue Report</a>
            <a href="index.php?page=admin&action=feedback" class="btn btn-secondary">📝 Action on Feedback</a>
            <a href="index.php?page=admin&action=payments" class="btn btn-secondary">💳 Payment Management</a>
        </div>
    </div>

    <div class="card-box">
        <h2>General Management (CRUD + Search)</h2>
        <div class="actions-cell">
            <a href="index.php?page=admin&action=users" class="btn btn-secondary">👥 Manage Users</a>
            <a href="index.php?page=admin&action=orders" class="btn btn-secondary">📦 All Orders</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

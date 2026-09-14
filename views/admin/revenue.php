<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1>💰 Revenue Report</h1>
        <p><em>Admin Unique Feature #1</em> — total earnings across the platform.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card"><h3>৳<?php echo number_format($total['revenue'] ?? 0, 2); ?></h3><p>Total Platform Revenue</p></div>
        <div class="stat-card"><h3><?php echo $total['total_orders'] ?? 0; ?></h3><p>Completed Orders</p></div>
    </div>

    <div class="card-box">
        <h2>Revenue by Restaurant</h2>
        <table>
            <tr><th>Restaurant</th><th>Completed Orders</th><th>Revenue</th></tr>
            <?php foreach ($by_restaurant as $r): ?>
            <tr>
                <td><?php echo h($r['restaurant_name'] ?? ''); ?></td>
                <td><?php echo (int)($r['total_orders'] ?? 0); ?></td>
                <td>৳<?php echo number_format((float)($r['revenue'] ?? 0), 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($by_restaurant)): ?>
                <tr><td colspan="3">No completed orders yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

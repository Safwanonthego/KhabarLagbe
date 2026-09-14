<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1>💰 Earnings Summary</h1>
        <p><em>Rider Unique Feature #3</em> — track rider income from customer tips and restaurant delivery fees.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>৳<?php echo number_format((float)($summary['total_earnings'] ?? 0), 2); ?></h3>
            <p>Total Earnings</p>
        </div>
        <div class="stat-card">
            <h3><?php echo (int)($summary['completed_orders'] ?? 0); ?></h3>
            <p>Completed Orders</p>
        </div>
        <div class="stat-card">
            <h3><?php echo (int)($summary['active_orders'] ?? 0); ?></h3>
            <p>Active Deliveries</p>
        </div>
    </div>

    <div class="card-box">
        <h2>Income Breakdown</h2>
        <table>
            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Restaurant</th>
                <th>Tip</th>
                <th>Delivery Fee</th>
                <th>Rider Earnings</th>
                <th>Status</th>
            </tr>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?php echo (int)($o['id'] ?? 0); ?></td>
                <td><?php echo h($o['customer_name'] ?? ''); ?></td>
                <td><?php echo h($o['restaurant_name'] ?? ''); ?></td>
                <td>৳<?php echo number_format((float)($o['tip_amount'] ?? 0), 2); ?></td>
                <td>৳<?php echo number_format((float)($o['delivery_fee'] ?? 0), 2); ?></td>
                <td><strong>৳<?php echo number_format((float)($o['rider_earning'] ?? 0), 2); ?></strong></td>
                <td><span class="badge badge-<?php echo h($o['status'] ?? 'pending'); ?>"><?php echo h($o['status'] ?? 'pending'); ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
            <tr><td colspan="7">No earnings data available yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

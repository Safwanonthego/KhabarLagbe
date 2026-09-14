<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header"><h1>All Orders (Read + Search + Delete)</h1></div>

    <div class="card-box">
        <form method="GET" class="search-bar">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="action" value="orders">
            <input type="text" name="keyword" placeholder="Search by customer, restaurant, status..." value="<?php echo h($keyword ?? ''); ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <tr><th>ID</th><th>Customer</th><th>Restaurant</th><th>Type</th><th>Total</th><th>Status</th><th>Date</th><th>Actions</th></tr>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?php echo (int)($o['id'] ?? 0); ?></td>
                <td><?php echo h($o['customer_name'] ?? ''); ?></td>
                <td><?php echo h($o['restaurant_name'] ?? ''); ?></td>
                <td><?php echo h(ucfirst($o['delivery_type'] ?? '')); ?></td>
                <td>৳<?php echo number_format((float)($o['total_amount'] ?? 0), 2); ?></td>
                <td><span class="badge badge-<?php echo h($o['status'] ?? 'pending'); ?>"><?php echo h($o['status'] ?? 'pending'); ?></span></td>
                <td><?php echo h(date('d M, H:i', strtotime($o['created_at'] ?? 'now'))); ?></td>
                <td><a href="index.php?page=admin&action=orders&delete=<?php echo (int)($o['id'] ?? 0); ?>" class="btn btn-small btn-danger" onclick="return confirm('Delete this order?');">Delete</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
                <tr><td colspan="8">No orders found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

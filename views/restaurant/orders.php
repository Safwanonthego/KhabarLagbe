<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1>✅ Order Validation</h1>
        <p><em>Restaurant Unique Feature #2</em> — accept, reject or start preparing incoming orders.</p>
    </div>

    <div class="card-box">
        <form method="GET" class="search-bar">
            <input type="hidden" name="page" value="restaurant">
            <input type="hidden" name="action" value="orders">
            <input type="text" name="keyword" placeholder="Search by customer or status..." value="<?php echo h($keyword ?? ''); ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <tr><th>ID</th><th>Customer</th><th>Type</th><th>Total</th><th>Status</th><th>Validate</th></tr>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?php echo (int)($o['id'] ?? 0); ?></td>
                <td><?php echo h($o['customer_name'] ?? ''); ?></td>
                <td><?php echo h(ucfirst($o['delivery_type'] ?? '')); ?></td>
                <td>৳<?php echo number_format((float)($o['total_amount'] ?? 0), 2); ?></td>
                <td><span class="badge badge-<?php echo h($o['status'] ?? 'pending'); ?>" id="badge-<?php echo (int)($o['id'] ?? 0); ?>"><?php echo h($o['status'] ?? 'pending'); ?></span></td>
                <td class="actions-cell">
                    <?php if (($o['status'] ?? '') === 'pending'): ?>
                    <form method="POST" style="display:flex; gap:5px;">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="action" value="validate">
                        <input type="hidden" name="order_id" value="<?php echo (int)($o['id'] ?? 0); ?>">
                        <button type="submit" name="decision" value="accepted" class="btn btn-small">Accept</button>
                        <button type="submit" name="decision" value="rejected" class="btn btn-small btn-danger">Reject</button>
                    </form>
                    <?php elseif (($o['status'] ?? '') === 'accepted'): ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="action" value="validate">
                        <input type="hidden" name="order_id" value="<?php echo (int)($o['id'] ?? 0); ?>">
                        <button type="submit" name="decision" value="preparing" class="btn btn-small">Start Preparing</button>
                    </form>
                    <?php else: ?>
                        <em>—</em>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?><tr><td colspan="6">No orders found.</td></tr><?php endif; ?>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

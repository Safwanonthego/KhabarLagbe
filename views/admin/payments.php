<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1>💳 Payment Management</h1>
        <p><em>Admin Unique Feature #3</em> — track and settle order payments / refunds.</p>
    </div>

    <div class="card-box">
        <form method="GET" class="search-bar">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="action" value="payments">
            <input type="text" name="keyword" placeholder="Search by customer, method or status..." value="<?php echo h($keyword ?? ''); ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <tr><th>Customer</th><th>Amount</th><th>Method</th><th>Status</th><th>Transaction ID</th><th>Actions</th></tr>
            <?php foreach ($payments as $p): ?>
            <tr>
                <td><?php echo h($p['customer_name'] ?? ''); ?></td>
                <td>৳<?php echo number_format((float)($p['amount'] ?? 0), 2); ?></td>
                <td><?php echo h(ucfirst($p['method'] ?? '')); ?></td>
                <td><span class="badge"><?php echo h($p['status'] ?? ''); ?></span></td>
                <td><?php echo h($p['transaction_id'] ?? '-'); ?></td>
                <td>
                    <form method="POST" style="display:flex; gap:5px; flex-wrap:wrap;">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="id" value="<?php echo (int)($p['id'] ?? 0); ?>">
                        <select name="status">
                            <option value="pending" <?php echo ($p['status'] ?? '')=='pending'?'selected':''; ?>>Pending</option>
                            <option value="paid" <?php echo ($p['status'] ?? '')=='paid'?'selected':''; ?>>Paid</option>
                            <option value="refunded" <?php echo ($p['status'] ?? '')=='refunded'?'selected':''; ?>>Refunded</option>
                        </select>
                        <input type="text" name="transaction_id" placeholder="Txn ID" style="width:100px;" value="<?php echo h($p['transaction_id'] ?? ''); ?>">
                        <button type="submit" class="btn btn-small">Update</button>
                        <a href="index.php?page=admin&action=payments&delete=<?php echo (int)($p['id'] ?? 0); ?>" class="btn btn-small btn-danger" onclick="return confirm('Delete?');">Del</a>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($payments)): ?>
                <tr><td colspan="6">No payments found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1>📦 My Orders</h1>
        <p><em>Customer Unique Feature</em> — add a tip for your rider.</p>
    </div>

    <div class="card-box">
        <form method="GET" class="search-bar">
            <input type="hidden" name="page" value="customer">
            <input type="hidden" name="action" value="orders">
            <input type="text" name="keyword" placeholder="Search by restaurant or status..." value="<?php echo h($keyword); ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <tr><th>ID</th><th>Restaurant</th><th>Type</th><th>Total</th><th>Tip</th><th>Status</th><th>Actions</th></tr>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?php echo $o['id']; ?></td>
                <td><?php echo h($o['restaurant_name']); ?></td>
                <td><?php echo h(ucfirst($o['delivery_type'])); ?></td>
                <td>৳<?php echo number_format($o['total_amount'], 2); ?></td>
                <td>৳<?php echo number_format($o['tip_amount'], 2); ?></td>
                <td><span class="badge badge-<?php echo h($o['status']); ?>"><?php echo h($o['status']); ?></span></td>
                <td class="actions-cell">
                    <?php if ($o['rider_id'] && !in_array($o['status'], ['delivered','picked_up','cancelled'])): ?>
                        <a class="btn btn-small btn-secondary" href="index.php?page=customer&action=messages&order_id=<?php echo $o['id']; ?>&rider_id=<?php echo $o['rider_id']; ?>">Message Rider</a>
                    <?php endif; ?>
                    <?php if ($o['delivery_type'] === 'pickup' && !in_array($o['status'], ['delivered','picked_up','cancelled','rejected'])): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="action" value="confirm_pickup">
                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                            <button type="submit" class="btn btn-small">Mark Picked Up</button>
                        </form>
                    <?php endif; ?>
                    <?php if (!in_array($o['status'], ['delivered','picked_up'])): ?>
                        <button class="btn btn-small" onclick="openTip(<?php echo $o['id']; ?>)">Add Tip</button>
                    <?php endif; ?>
                    <?php if ($o['status'] === 'pending'): ?>
                        <a href="index.php?page=customer&action=orders&delete=<?php echo $o['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Cancel this order?');">Cancel</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?><tr><td colspan="7">No orders found.</td></tr><?php endif; ?>
        </table>
    </div>
</div>

<div id="tipModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div class="auth-container" style="margin-top:100px;">
        <h2>Add Tip for Rider</h2>
        <form id="tipForm" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="tip">
            <input type="hidden" name="order_id" id="tip_order_id">
            <div class="form-group"><label>Tip Amount (৳)</label><input type="text" name="tip_amount"></div>
            <button type="submit">Submit Tip</button>
            <button type="button" class="btn-secondary" onclick="document.getElementById('tipModal').style.display='none'">Cancel</button>
        </form>
    </div>
</div>

<script>
function openTip(orderId) {
    document.getElementById('tip_order_id').value = orderId;
    document.getElementById('tipModal').style.display = 'block';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

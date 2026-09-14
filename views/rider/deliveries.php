<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1>📋 Assigned Deliveries</h1>
        <p><em>Rider Unique Feature #1 & #2</em> — see your delivery list and update status live (AJAX).</p>
    </div>

    <div class="card-box">
        <form method="GET" class="search-bar">
            <input type="hidden" name="page" value="rider">
            <input type="hidden" name="action" value="deliveries">
            <input type="text" name="keyword" placeholder="Search by customer or status..." value="<?php echo h($keyword); ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <tr><th>ID</th><th>Customer</th><th>Address</th><th>Restaurant</th><th>Status</th><th>Update</th><th>Chat</th></tr>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?php echo $o['id']; ?></td>
                <td><?php echo h($o['customer_name']); ?></td>
                <td><?php echo h($o['address']); ?></td>
                <td><?php echo h($o['restaurant_name']); ?></td>
                <td><span class="badge badge-<?php echo h($o['status']); ?>" id="badge-<?php echo $o['id']; ?>"><?php echo h($o['status']); ?></span></td>
                <td>
                    <?php if (in_array($o['status'], ['delivered', 'picked_up'], true)): ?>
                        <span class="badge badge-<?php echo h($o['status']); ?>"><?php echo h($o['status']); ?></span>
                    <?php else: ?>
                        <select class="ajax-status-select" data-endpoint="ajax/update_delivery_status.php" data-order-id="<?php echo $o['id']; ?>">
                            <option value="assigned" <?php echo $o['status']=='assigned'?'selected':''; ?>>Assigned</option>
                            <option value="on_the_way" <?php echo $o['status']=='on_the_way'?'selected':''; ?>>On the Way</option>
                            <option value="delivered" <?php echo $o['status']=='delivered'?'selected':''; ?>>Delivered</option>
                            <option value="picked_up" <?php echo $o['status']=='picked_up'?'selected':''; ?>>Picked Up (self pickup)</option>
                        </select>
                    <?php endif; ?>
                </td>
                <td><a class="btn btn-small btn-secondary" href="index.php?page=rider&action=messages&order_id=<?php echo $o['id']; ?>&customer_id=<?php echo $o['customer_id']; ?>">Message</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?><tr><td colspan="7">No deliveries assigned yet.</td></tr><?php endif; ?>
        </table>
    </div>
</div>

<script>window.CSRF_TOKEN = "<?php echo generate_csrf_token(); ?>";</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

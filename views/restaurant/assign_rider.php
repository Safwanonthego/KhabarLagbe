<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1>🛵 Assign Delivery Rider</h1>
        <p><em>Restaurant Unique Feature #3</em> — assign an available rider to a ready order (via AJAX).</p>
    </div>

    <div class="card-box">
        <table>
            <tr><th>ID</th><th>Customer</th><th>Status</th><th>Assign Rider</th></tr>
            <?php foreach ($assignable_orders as $o): ?>
            <tr>
                <td>#<?php echo (int)($o['id'] ?? 0); ?></td>
                <td><?php echo h($o['customer_name'] ?? ''); ?></td>
                <td id="badge-<?php echo (int)($o['id'] ?? 0); ?>" class="badge badge-<?php echo h($o['status'] ?? 'pending'); ?>"><?php echo h($o['status'] ?? 'pending'); ?></td>
                <td>
                    <select class="ajax-assign-rider" data-order-id="<?php echo (int)($o['id'] ?? 0); ?>">
                        <option value="">-- Select Rider --</option>
                        <?php foreach ($riders as $r): ?>
                            <option value="<?php echo (int)($r['id'] ?? 0); ?>" <?php echo (int)($o['rider_id'] ?? 0)==(int)($r['id'] ?? 0)?'selected':''; ?>>
                                <?php echo h($r['name'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($assignable_orders)): ?>
                <tr><td colspan="4">No orders ready for rider assignment right now.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<script>window.CSRF_TOKEN = "<?php echo generate_csrf_token(); ?>";</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

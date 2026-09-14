<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1>⭐ Rate & Review</h1>
        <p><em>Customer Unique Feature #3</em> — review completed orders. Admin monitors this feedback.</p>
    </div>

    <?php foreach ($errors as $err): ?><div class="alert alert-error"><?php echo h($err); ?></div><?php endforeach; ?>

    <div class="card-box">
        <h2>Leave Feedback on a Delivered Order</h2>
        <?php if (empty($delivered_orders)): ?>
            <p>No delivered/picked-up orders yet to review.</p>
        <?php else: ?>
        <form id="feedbackForm" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="add_feedback">
            <div class="form-group">
                <label>Order</label>
                <select name="order_id">
                    <?php foreach ($delivered_orders as $o): ?>
                        <option value="<?php echo (int)($o['id'] ?? 0); ?>">#<?php echo (int)($o['id'] ?? 0); ?> - <?php echo h($o['restaurant_name'] ?? ''); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Rating (1-5)</label>
                <input type="number" name="rating" min="1" max="5" value="5">
            </div>
            <div class="form-group">
                <label>Comment</label>
                <textarea name="comment" rows="3"></textarea>
            </div>
            <button type="submit">Submit Feedback</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="card-box">
        <h2>Your Past Reviews</h2>
        <table>
            <tr><th>Restaurant</th><th>Rating</th><th>Comment</th><th>Admin Status</th></tr>
            <?php foreach ($my_feedback as $f): ?>
            <tr>
                <td><?php echo h($f['restaurant_name'] ?? ''); ?></td>
                <td><?php echo str_repeat('⭐', (int)($f['rating'] ?? 0)); ?></td>
                <td><?php echo h($f['comment'] ?? ''); ?></td>
                <td><span class="badge"><?php echo h($f['admin_action'] ?? 'none'); ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($my_feedback)): ?><tr><td colspan="4">No reviews yet.</td></tr><?php endif; ?>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

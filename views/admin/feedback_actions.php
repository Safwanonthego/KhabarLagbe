<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1>📝 Action Based on Feedback</h1>
        <p><em>Admin Unique Feature #2</em> — review complaints/ratings and take action on restaurants.</p>
    </div>

    <div class="card-box">
        <form method="GET" class="search-bar">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="action" value="feedback">
            <input type="text" name="keyword" placeholder="Search by customer, restaurant, comment..." value="<?php echo h($keyword ?? ''); ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <tr><th>Customer</th><th>Restaurant</th><th>Rating</th><th>Comment</th><th>Admin Action</th><th>Take Action</th></tr>
            <?php foreach ($feedback_list as $f): ?>
            <tr>
                <td><?php echo h($f['customer_name'] ?? ''); ?></td>
                <td><?php echo h($f['restaurant_name'] ?? ''); ?></td>
                <td><?php echo str_repeat('⭐', (int)($f['rating'] ?? 0)); ?></td>
                <td><?php echo h($f['comment'] ?? ''); ?></td>
                <td><span class="badge"><?php echo h($f['admin_action'] ?? 'none'); ?></span></td>
                <td class="actions-cell">
                    <form method="POST" style="display:flex; gap:5px;">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="action" value="act">
                        <input type="hidden" name="feedback_id" value="<?php echo (int)($f['id'] ?? 0); ?>">
                        <input type="hidden" name="restaurant_id" value="<?php echo (int)($f['restaurant_id'] ?? 0); ?>">
                        <select name="decision">
                            <option value="resolved">Mark Resolved</option>
                            <option value="warned">Warn Restaurant</option>
                            <option value="suspended">Suspend Restaurant</option>
                        </select>
                        <button type="submit" class="btn btn-small">Apply</button>
                    </form>
                    <a href="index.php?page=admin&action=feedback&delete=<?php echo (int)($f['id'] ?? 0); ?>" class="btn btn-small btn-danger" onclick="return confirm('Delete this feedback?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($feedback_list)): ?>
                <tr><td colspan="6">No feedback found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

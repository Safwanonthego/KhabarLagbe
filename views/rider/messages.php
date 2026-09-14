<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1>💬 Message Customers</h1>
        <p><em>Rider Unique Feature #3</em> — communicate directly about a delivery.</p>
    </div>

    <div class="card-box">
        <h2>Choose an Order to Chat About</h2>
        <table>
            <tr><th>ID</th><th>Customer</th><th>Status</th><th></th></tr>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?php echo (int)($o['id'] ?? 0); ?></td>
                <td><?php echo h($o['customer_name'] ?? ''); ?></td>
                <td><span class="badge badge-<?php echo h($o['status'] ?? 'pending'); ?>"><?php echo h($o['status'] ?? 'pending'); ?></span></td>
                <td><a class="btn btn-small" href="index.php?page=rider&action=messages&order_id=<?php echo (int)($o['id'] ?? 0); ?>&customer_id=<?php echo (int)($o['customer_id'] ?? 0); ?>">Open Chat</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?><tr><td colspan="4">No orders yet.</td></tr><?php endif; ?>
        </table>
    </div>

    <?php if ($order_id): ?>
    <div class="card-box">
        <h2>Chat — Order #<?php echo (int)($order_id ?? 0); ?></h2>
        <div class="chat-box" id="chatBox">
            <?php foreach ($messages as $m): ?>
                <div class="chat-msg">
                    <div class="sender"><?php echo ($m['sender_id'] ?? 0) == current_user_id() ? 'You' : h($m['sender_name'] ?? ''); ?></div>
                    <div class="text"><?php echo h($m['message'] ?? ''); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <form id="messageForm" data-order-id="<?php echo (int)($order_id ?? 0); ?>" data-receiver-id="<?php echo (int)($customer_id ?? 0); ?>">
            <div class="form-group" style="display:flex; gap:8px;">
                <input type="text" name="message" placeholder="Type a message...">
                <button type="submit">Send</button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>window.CSRF_TOKEN = "<?php echo generate_csrf_token(); ?>";</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

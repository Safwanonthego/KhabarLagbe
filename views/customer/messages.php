<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header"><h1>💬 Message Your Rider — Order #<?php echo (int)($order_id ?? 0); ?></h1></div>

    <div class="card-box">
        <div class="chat-box" id="chatBox">
            <?php foreach ($messages as $m): ?>
                <div class="chat-msg">
                    <div class="sender"><?php echo ($m['sender_id'] ?? 0) == current_user_id() ? 'You' : h($m['sender_name'] ?? ''); ?></div>
                    <div class="text"><?php echo h($m['message'] ?? ''); ?></div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($messages)): ?><p>No messages yet. Say hello 👋</p><?php endif; ?>
        </div>
        <form id="messageForm" data-order-id="<?php echo (int)($order_id ?? 0); ?>" data-receiver-id="<?php echo (int)($rider_id ?? 0); ?>">
            <div class="form-group" style="display:flex; gap:8px;">
                <input type="text" name="message" placeholder="Type a message...">
                <button type="submit">Send</button>
            </div>
        </form>
    </div>
</div>

<script>window.CSRF_TOKEN = "<?php echo generate_csrf_token(); ?>";</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

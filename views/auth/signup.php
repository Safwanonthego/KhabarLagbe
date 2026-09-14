<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="auth-container">
    <h2>Create an Account</h2>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?php echo h($err); ?></div>
    <?php endforeach; ?>

    <form id="signupForm" method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <div class="form-group">
            <label>I am signing up as</label>
            <select name="role" id="roleSelect">
                <option value="customer" <?php echo $old['role']=='customer'?'selected':''; ?>>Customer</option>
                <option value="restaurant" <?php echo $old['role']=='restaurant'?'selected':''; ?>>Restaurant</option>
                <option value="rider" <?php echo $old['role']=='rider'?'selected':''; ?>>Delivery Rider</option>
            </select>
        </div>

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?php echo h($old['name']); ?>">
        </div>

        <div class="form-group" id="restaurantNameGroup" style="display:none;">
            <label>Restaurant Name</label>
            <input type="text" name="restaurant_name" value="<?php echo h($_POST['restaurant_name'] ?? ''); ?>">
        </div>

        <div class="form-group" id="deliveryFeeGroup" style="display:none;">
            <label>Delivery Fee (৳)</label>
            <input type="number" step="0.01" min="0" name="delivery_fee" value="<?php echo h($_POST['delivery_fee'] ?? '0'); ?>">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo h($old['email']); ?>">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password">
        </div>

        <div class="form-group">
            <label>Phone (e.g. 017XXXXXXXX)</label>
            <input type="text" name="phone" value="<?php echo h($old['phone']); ?>">
        </div>

        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" value="<?php echo h($old['address']); ?>">
        </div>

        <button type="submit">Sign Up</button>
    </form>

    <p class="switch-link">Already have an account? <a href="index.php?page=login">Login here</a></p>
</div>

<script>
document.getElementById('roleSelect').addEventListener('change', function () {
    const isRestaurant = this.value === 'restaurant';
    document.getElementById('restaurantNameGroup').style.display = isRestaurant ? 'block' : 'none';
    document.getElementById('deliveryFeeGroup').style.display = isRestaurant ? 'block' : 'none';
});
document.getElementById('roleSelect').dispatchEvent(new Event('change'));
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

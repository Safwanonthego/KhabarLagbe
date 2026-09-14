<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1>🍽️ Browse Food</h1>
        <p><em>Customer Unique Feature</em> — choose Pickup or Delivery when ordering.</p>
    </div>

    <?php foreach ($errors as $err): ?><div class="alert alert-error"><?php echo h($err); ?></div><?php endforeach; ?>

    <div class="card-box">
        <form method="GET" class="search-bar">
            <input type="hidden" name="page" value="customer">
            <input type="hidden" name="action" value="browse">
            <input type="text" name="keyword" placeholder="Search food, category or restaurant..." value="<?php echo h($keyword ?? ''); ?>">
            <button type="submit">Search</button>
        </form>

        <div class="food-grid">
            <?php foreach ($foods as $f): ?>
            <div class="food-card">
                <h4><?php echo h($f['name'] ?? ''); ?></h4>
                <div class="restaurant-name"><?php echo h($f['restaurant_name'] ?? ''); ?> • <?php echo h($f['category'] ?? ''); ?></div>
                <div class="price">৳<?php echo number_format((float)($f['price'] ?? 0), 2); ?></div>
                <button class="btn btn-small" onclick="openOrderModal(<?php echo (int)($f['id'] ?? 0); ?>, '<?php echo h(addslashes($f['name'] ?? '')); ?>', <?php echo (float)($f['price'] ?? 0); ?>)">Order Now</button>
            </div>
            <?php endforeach; ?>
            <?php if (empty($foods)): ?><p>No food items found.</p><?php endif; ?>
        </div>
    </div>
</div>

<div id="orderModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div class="auth-container" style="margin-top:100px;">
        <h2 id="om_title">Place Order</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="order">
            <input type="hidden" name="food_id" id="om_food_id">
            <div class="form-group"><label>Quantity</label><input type="number" name="quantity" id="om_qty" value="1" min="1"></div>
            <div class="form-group">
                <label>Delivery Option</label>
                <select name="delivery_type">
                    <option value="delivery">Home Delivery</option>
                    <option value="pickup">Self Pickup</option>
                </select>
            </div>
            <p>Price: ৳<span id="om_price"></span> each</p>
            <button type="submit">Confirm Order</button>
            <button type="button" class="btn-secondary" onclick="document.getElementById('orderModal').style.display='none'">Cancel</button>
        </form>
    </div>
</div>

<script>
function openOrderModal(id, name, price) {
    document.getElementById('om_title').textContent = 'Order: ' + name;
    document.getElementById('om_food_id').value = id;
    document.getElementById('om_price').textContent = price;
    document.getElementById('orderModal').style.display = 'block';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

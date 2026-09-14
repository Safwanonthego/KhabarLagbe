<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <h1>🍕 Food Menu Management</h1>
        <p><em>Restaurant Unique Feature #1</em> — Add / Edit / Delete menu items.</p>
    </div>

    <?php foreach ($errors as $err): ?><div class="alert alert-error"><?php echo h($err); ?></div><?php endforeach; ?>

    <div class="card-box">
        <h2>Add New Food</h2>
        <form id="foodForm" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="add">
            <div class="form-group"><label>Name</label><input type="text" name="name"></div>
            <div class="form-group"><label>Description</label><textarea name="description" rows="2"></textarea></div>
            <div class="form-group"><label>Price (৳)</label><input type="text" name="price"></div>
            <div class="form-group"><label>Category</label><input type="text" name="category" placeholder="e.g. Pizza, Burger, Drinks"></div>
            <button type="submit">Add Food</button>
        </form>
    </div>

    <div class="card-box">
        <h2>Your Menu</h2>
        <form method="GET" class="search-bar">
            <input type="hidden" name="page" value="restaurant">
            <input type="hidden" name="action" value="foods">
            <input type="text" name="keyword" placeholder="Search food by name or category..." value="<?php echo h($keyword); ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <tr><th>Name</th><th>Category</th><th>Price</th><th>Status</th><th>Actions</th></tr>
            <?php foreach ($foods as $f): ?>
            <tr>
                <td><?php echo h($f['name']); ?></td>
                <td><?php echo h($f['category']); ?></td>
                <td>৳<?php echo number_format($f['price'], 2); ?></td>
                <td><span class="badge <?php echo $f['status']=='available'?'badge-delivered':'badge-rejected'; ?>"><?php echo h($f['status']); ?></span></td>
                <td class="actions-cell">
                    <button class="btn btn-small" onclick='openEditFood(<?php echo json_encode($f, JSON_HEX_APOS|JSON_HEX_QUOT); ?>)'>Edit</button>
                    <a href="index.php?page=restaurant&action=foods&delete=<?php echo $f['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Delete this item?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($foods)): ?><tr><td colspan="5">No food items found.</td></tr><?php endif; ?>
        </table>
    </div>
</div>

<div id="editFoodModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div class="auth-container" style="margin-top:80px;">
        <h2>Edit Food</h2>
        <form method="POST" id="foodEditForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="fe_id">
            <div class="form-group"><label>Name</label><input type="text" name="name" id="fe_name"></div>
            <div class="form-group"><label>Description</label><textarea name="description" id="fe_description" rows="2"></textarea></div>
            <div class="form-group"><label>Price</label><input type="text" name="price" id="fe_price"></div>
            <div class="form-group"><label>Category</label><input type="text" name="category" id="fe_category"></div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="fe_status">
                    <option value="available">Available</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>
            <button type="submit">Save Changes</button>
            <button type="button" class="btn-secondary" onclick="document.getElementById('editFoodModal').style.display='none'">Cancel</button>
        </form>
    </div>
</div>

<script>
function openEditFood(food) {
    document.getElementById('fe_id').value = food.id;
    document.getElementById('fe_name').value = food.name;
    document.getElementById('fe_description').value = food.description;
    document.getElementById('fe_price').value = food.price;
    document.getElementById('fe_category').value = food.category;
    document.getElementById('fe_status').value = food.status;
    document.getElementById('editFoodModal').style.display = 'block';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

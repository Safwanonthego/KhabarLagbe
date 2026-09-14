<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="dashboard-header"><h1>Manage Users</h1></div>

    <div class="card-box">
        <form method="GET" class="search-bar">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="action" value="users">
            <input type="text" name="keyword" placeholder="Search by name or email..." value="<?php echo h($keyword); ?>">
            <select name="role" onchange="this.form.submit()">
                <option value="">All Roles</option>
                <option value="admin" <?php echo $role_filter=='admin'?'selected':''; ?>>Admin</option>
                <option value="restaurant" <?php echo $role_filter=='restaurant'?'selected':''; ?>>Restaurant</option>
                <option value="customer" <?php echo $role_filter=='customer'?'selected':''; ?>>Customer</option>
                <option value="rider" <?php echo $role_filter=='rider'?'selected':''; ?>>Rider</option>
            </select>
            <button type="submit">Search</button>
        </form>

        <table>
            <tr><th>Name</th><th>Email</th><th>Role</th><th>Phone</th><th>Status</th><th>Actions</th></tr>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?php echo h($u['name']); ?></td>
                <td><?php echo h($u['email']); ?></td>
                <td><?php echo h(ucfirst($u['role'])); ?></td>
                <td><?php echo h($u['phone']); ?></td>
                <td><span class="badge <?php echo $u['status']=='active'?'badge-delivered':'badge-rejected'; ?>"><?php echo h($u['status']); ?></span></td>
                <td class="actions-cell">
                    <button class="btn btn-small" onclick="openEdit(<?php echo htmlspecialchars(json_encode($u), ENT_QUOTES); ?>)">Edit</button>
                    <a class="btn btn-small btn-secondary" href="index.php?page=admin&action=users&toggle_status=<?php echo $u['id']; ?>">
                        <?php echo $u['status']=='active' ? 'Suspend' : 'Activate'; ?>
                    </a>
                    <a class="btn btn-small btn-danger" href="index.php?page=admin&action=users&delete=<?php echo $u['id']; ?>" onclick="return confirm('Delete this user?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
                <tr><td colspan="6">No users found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div class="auth-container" style="margin-top:100px;">
        <h2>Edit User</h2>
        <form method="POST" id="editForm">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group"><label>Name</label><input type="text" name="name" id="edit_name"></div>
            <div class="form-group"><label>Phone</label><input type="text" name="phone" id="edit_phone"></div>
            <div class="form-group"><label>Address</label><input type="text" name="address" id="edit_address"></div>
            <button type="submit">Save</button>
            <button type="button" class="btn-secondary" onclick="document.getElementById('editModal').style.display='none'">Cancel</button>
        </form>
    </div>
</div>

<script>
function openEdit(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_phone').value = user.phone;
    document.getElementById('edit_address').value = user.address;
    document.getElementById('editModal').style.display = 'block';
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

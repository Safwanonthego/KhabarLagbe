<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="auth-container">
    <h2>Login to KhabarLagbe</h2>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?php echo h($err); ?></div>
    <?php endforeach; ?>

    <form id="loginForm" method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo h($old_email); ?>">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password">
        </div>

        <button type="submit">Login</button>
    </form>

    <p class="switch-link">Don't have an account? <a href="index.php?page=signup">Sign up here</a></p>
    <p class="switch-link" style="color:#888; font-size:0.8rem;">
        Demo logins (password: 123456) — see seed.php / README
    </p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<header class="navbar">
  <div class="navbar-brand">Khabar<span>Lagbe</span></div>
  <nav class="navbar-links">
    <?php if (!empty($_SESSION['user_id'])): ?>
      <span class="nav-user">Hi, <?php echo h($_SESSION['name']); ?> (<?php echo h(ucfirst($_SESSION['role'])); ?>)</span>
      <a href="index.php?page=<?php echo rawurlencode($_SESSION['role']); ?>">Dashboard</a>
      <a href="index.php?page=logout" class="btn-logout">Logout</a>
    <?php else: ?>
      <a href="index.php?page=login">Login</a>
      <a href="index.php?page=signup">Sign Up</a>
    <?php endif; ?>
  </nav>
</header>
<?php $flash_success = get_flash('success'); $flash_error = get_flash('error'); ?>
<?php if ($flash_success): ?>
  <div class="alert alert-success"><?php echo h($flash_success); ?></div>
<?php endif; ?>
<?php if ($flash_error): ?>
  <div class="alert alert-error"><?php echo h($flash_error); ?></div>
<?php endif; ?>

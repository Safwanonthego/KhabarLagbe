<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container" style="padding-top:40px;">
    <div class="hero-panel">
        <div class="hero-copy">
            <span class="eyebrow">Fresh • Fast • Local</span>
            <h1>Khabar<span>Lagbe</span></h1>
            <p>Order your favorite meals with a local feel, fast delivery, and the comfort of home-cooked taste.</p>
            <div class="hero-actions">
                <a href="index.php?page=login" class="btn">Login</a>
                <a href="index.php?page=signup" class="btn btn-secondary">Sign Up</a>
            </div>
            <div class="mini-metrics">
                <div><strong>30 min</strong><span>Average delivery</span></div>
                <div><strong>2.4k+</strong><span>happy customers</span></div>
                <div><strong>200+</strong><span>menu choices</span></div>
            </div>
        </div>
        <div class="hero-showcase">
            <div class="showcase-card primary">
                <span class="badge badge-delivered">Popular</span>
                <h3>Spicy Chicken Burger</h3>
                <p>Juicy grilled chicken, crisp salad, signature sauce.</p>
                <div class="showcase-meta"><strong>৳420</strong><span>4.8 ★</span></div>
            </div>
            <div class="showcase-card secondary">
                <span class="badge badge-assigned">Today</span>
                <h3>Grill Feast Combo</h3>
                <p>BBQ platter with crunchy fries and soft drinks.</p>
                <div class="showcase-meta"><strong>৳680</strong><span>4.9 ★</span></div>
            </div>
        </div>
    </div>

    <div class="stats-grid" style="margin-top:26px;">
        <div class="stat-card"><h3>👑</h3><p>Admin manages revenue, feedback actions & payments</p></div>
        <div class="stat-card"><h3>🍽️</h3><p>Restaurants manage menu, validate & assign orders</p></div>
        <div class="stat-card"><h3>🧑</h3><p>Customers order, tip, and choose pickup/delivery</p></div>
        <div class="stat-card"><h3>🛵</h3><p>Riders manage deliveries & message customers</p></div>
    </div>

    <div class="featured-section">
        <div class="section-heading">
            <h2>Featured Meals</h2>
        </div>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="food-icon">🍔</div>
                <h4>Classic Burger</h4>
                <p>Loaded with cheddar, tomato, onion, and our signature sauce.</p>
                <div class="feature-meta"><span>৳360</span><button class="btn btn-small">Order</button></div>
            </div>
            <div class="feature-card">
                <div class="food-icon">🍕</div>
                <h4>Hot Pizza</h4>
                <p>Thin crust with mozzarella, basil, and a smoky tomato finish.</p>
                <div class="feature-meta"><span>৳540</span><button class="btn btn-small">Order</button></div>
            </div>
            <div class="feature-card">
                <div class="food-icon">🥗</div>
                <h4>Fresh Bowl</h4>
                <p>Healthy greens, grilled protein, crunchy toppings, and dressing.</p>
                <div class="feature-meta"><span>৳430</span><button class="btn btn-small">Order</button></div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

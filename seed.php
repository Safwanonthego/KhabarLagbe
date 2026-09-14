<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/user_model.php';
require_once __DIR__ . '/models/restaurant_model.php';
require_once __DIR__ . '/models/food_model.php';

$demo_users = [
    ['Administrator', 'admin@food.com', 'admin', '01700000000', 'Dhaka Central'],
    ['Pizza Palace', 'pizza@food.com', 'restaurant', '01711111111', 'Gulshan, Dhaka'],
    ['Bengal Bites', 'bengal@food.com', 'restaurant', '01722222222', 'Banani, Dhaka'],
    ['Green Bowl', 'greenbowl@food.com', 'restaurant', '01733333333', 'Dhanmondi, Dhaka'],
    ['Mehedi Hasan', 'mehedi@food.com', 'customer', '01744444444', 'Banani, Dhaka'],
    ['Nusrat Jahan', 'nusrat@food.com', 'customer', '01755555555', 'Mohakhali, Dhaka'],
    ['Samiul Islam', 'sami@food.com', 'customer', '01766666666', 'Uttara, Dhaka'],
    ['Sakibul Islam', 'rider1@food.com', 'rider', '01777777777', 'Mohakhali, Dhaka'],
    ['Arif Rahman', 'rider2@food.com', 'rider', '01788888888', 'Mirpur, Dhaka'],
    ['Tania Ahmed', 'rider3@food.com', 'rider', '01799999999', 'Motijheel, Dhaka'],
];

$restaurant_profiles = [
    'pizza@food.com' => [
        'name' => 'Pizza Palace',
        'description' => 'Classic pizza, loaded cheese, and family favorites.',
        'delivery_fee' => 60.00,
        'foods' => [
            ['Chicken Supreme Pizza', 'Loaded with chicken, peppers, mozzarella, and sauce.', 650.00, 'Pizza'],
            ['Veggie Delight Pizza', 'Fresh vegetables with rich tomato sauce and cheese.', 590.00, 'Pizza'],
            ['Cheesy Garlic Bread', 'Fresh baked garlic bread with mozzarella topping.', 220.00, 'Appetizer'],
            ['Crispy Chicken Burger', 'Crunchy chicken patty with lettuce and special sauce.', 260.00, 'Burger'],
            ['Coke 500ml', 'Cold fizzy soft drink.', 50.00, 'Beverage'],
        ],
    ],
    'bengal@food.com' => [
        'name' => 'Bengal Bites',
        'description' => 'Bangla comfort food and spicy grill classics.',
        'delivery_fee' => 45.00,
        'foods' => [
            ['Beef Kacci Biryani', 'Fragrant biryani with tender beef and aromatic rice.', 420.00, 'Biryani'],
            ['Chicken Roast Platter', 'Roast chicken with rice, salad, and gravy.', 480.00, 'Main Course'],
            ['Shahi Paneer', 'Rich creamy curry with soft paneer cubes.', 330.00, 'Vegetarian'],
            ['Paratha Combo', 'Fresh paratha with chicken curry and salad.', 290.00, 'Combo'],
            ['Lemon Soda', 'Refreshing citrus soda.', 60.00, 'Beverage'],
        ],
    ],
    'greenbowl@food.com' => [
        'name' => 'Green Bowl',
        'description' => 'Healthy bowls, fresh salads, and nutritious meals.',
        'delivery_fee' => 35.00,
        'foods' => [
            ['Avocado Chicken Bowl', 'Grilled chicken, avocado, greens, and quinoa.', 520.00, 'Bowl'],
            ['Falafel Salad Bowl', 'Fresh salad with falafel, hummus, and herbs.', 390.00, 'Salad'],
            ['Green Detox Juice', 'Cucumber, mint, lime, and spinach blend.', 180.00, 'Beverage'],
            ['Grilled Veggie Wrap', 'Healthy wrap with grilled vegetables and sauce.', 270.00, 'Wrap'],
            ['Fruit Yogurt Bowl', 'Healthy yogurt with fresh seasonal fruit.', 220.00, 'Dessert'],
        ],
    ],
];

echo "<h2>Seeding demo data...</h2><ul>";

foreach ($demo_users as [$name, $email, $role, $phone, $address]) {
    if (!get_user_by_email($conn, $email)) {
        create_user($conn, $name, $email, '123456', $role, $phone, $address);
        echo "<li>Created: $email ($role) - password: 123456</li>";

        if ($role === 'restaurant') {
            $uid = mysqli_insert_id($conn);
            $profile = $restaurant_profiles[$email] ?? null;
            $restaurant_name = $profile['name'] ?? $name;
            $description = $profile['description'] ?? 'Fresh food prepared with care.';
            $delivery_fee = $profile['delivery_fee'] ?? 0.00;

            create_restaurant_profile($conn, $uid, $restaurant_name, $description, $delivery_fee);
            $rest = get_restaurant_by_user_id($conn, $uid);

            if ($rest && !empty($profile['foods'])) {
                foreach ($profile['foods'] as [$food_name, $food_description, $food_price, $category]) {
                    add_food($conn, $rest['id'], $food_name, $food_description, (float)$food_price, $category, null);
                }
            }

            echo "<li>Created restaurant profile with delivery fee ৳" . number_format((float)$delivery_fee, 2) . " and menu items.</li>";
        }
    } else {
        echo "<li>Skipped (already exists): $email</li>";
    }
}

echo "</ul><p><strong>Done!</strong> Go to <a href='index.php'>index.php</a> and login. Delete seed.php now for security.</p>";

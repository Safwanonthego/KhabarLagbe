<?php
require_once __DIR__ . '/../models/user_model.php';
require_once __DIR__ . '/../models/restaurant_model.php';

function home_controller($conn)
{
    render_view('views/landing.php', [
        'page_title' => 'Welcome',
        'asset_path' => ''
    ]);
}

function login_controller($conn)
{
    if (!empty($_SESSION['user_id'])) {
        redirect_to_dashboard();
    }

    $errors = [];
    $old_email = '';
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($requestMethod === 'POST') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errors[] = 'Invalid form submission. Please try again.';
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $old_email = $email;

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email is required.';
        }

        if ($password === '') {
            $errors[] = 'Password is required.';
        }

        if (empty($errors)) {
            $user = attempt_login($conn, $email, $password);

            if ($user) {
                if ($user['status'] === 'suspended') {
                    $errors[] = 'Your account has been suspended. Contact admin.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];

                    set_flash('success', 'Welcome back, ' . $user['name'] . '!');
                    redirect('index.php?page=' . rawurlencode($user['role']));
                }
            } else {
                $errors[] = 'Incorrect email or password.';
            }
        }
    }

    render_view('views/auth/login.php', [
        'page_title' => 'Login',
        'asset_path' => '',
        'errors' => $errors,
        'old_email' => $old_email
    ]);
}

function signup_controller($conn)
{
    if (!empty($_SESSION['user_id'])) {
        redirect_to_dashboard();
    }

    $errors = [];
    $old = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'address' => '',
        'role' => 'customer'
    ];
    $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($requestMethod === 'POST') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errors[] = 'Invalid form submission. Please try again.';
        }

        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $role = sanitize($_POST['role'] ?? '');
        $restaurant_name = sanitize($_POST['restaurant_name'] ?? '');
        $delivery_fee = $_POST['delivery_fee'] ?? '0';

        $old = compact('name', 'email', 'phone', 'address', 'role');
        $old['delivery_fee'] = $delivery_fee;

        if ($name === '') $errors[] = 'Name is required.';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
        if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
        if (!preg_match('/^01[0-9]{9}$/', $phone)) $errors[] = 'Phone must be an 11-digit number starting with 01.';
        if ($address === '') $errors[] = 'Address is required.';
        if (!in_array($role, ['customer', 'restaurant', 'rider'], true)) $errors[] = 'Invalid role selected.';
        if ($role === 'restaurant' && $restaurant_name === '') $errors[] = 'Restaurant name is required.';
        if ($role === 'restaurant' && (!is_numeric($delivery_fee) || (float)$delivery_fee < 0)) $errors[] = 'Delivery fee must be a valid non-negative number.';

        if (empty($errors) && get_user_by_email($conn, $email)) {
            $errors[] = 'This email is already registered.';
        }

        if (empty($errors)) {
            if (create_user($conn, $name, $email, $password, $role, $phone, $address)) {
                $new_user_id = mysqli_insert_id($conn);
                if ($role === 'restaurant') {
                    create_restaurant_profile($conn, $new_user_id, $restaurant_name, '', (float)$delivery_fee);
                }
                set_flash('success', 'Account created successfully! Please log in.');
                redirect('index.php?page=login');
            } else {
                $errors[] = 'Something went wrong. Please try again.';
            }
        }
    }

    render_view('views/auth/signup.php', [
        'page_title' => 'Sign Up',
        'asset_path' => '',
        'errors' => $errors,
        'old' => $old
    ]);
}

function logout_controller($conn)
{
    session_unset();
    session_destroy();
    redirect('index.php?page=login');
}

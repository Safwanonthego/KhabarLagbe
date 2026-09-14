<?php
function render_view($path, array $data = [])
{
    $page_title = $data['page_title'] ?? 'KhabarLagbe';
    $asset_path = $data['asset_path'] ?? '';

    extract($data);
    $fullPath = __DIR__ . '/../' . ltrim($path, '/');

    if (!file_exists($fullPath)) {
        die("View not found: {$fullPath}");
    }

    require $fullPath;
}

function redirect_to_dashboard()
{
    if (!empty($_SESSION['role']) && is_valid_role($_SESSION['role'])) {
        redirect('index.php?page=' . rawurlencode($_SESSION['role']));
    }

    redirect('index.php?page=login');
}

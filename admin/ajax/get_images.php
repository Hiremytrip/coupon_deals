<?php
session_start();
require_once '../../database/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Get image type from request
$type = isset($_GET['type']) ? sanitize($_GET['type']) : '';

// Validate image type
if (!in_array($type, ['stores', 'coupons'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid image type']);
    exit();
}

// Set directory path
$dir_path = '../../assets/images/' . $type . '/';

// Check if directory exists
if (!file_exists($dir_path)) {
    // Create directory if it doesn't exist
    mkdir($dir_path, 0777, true);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'images' => []]);
    exit();
}

// Get all images from directory
$images = [];
$files = scandir($dir_path);

foreach ($files as $file) {
    if ($file != '.' && $file != '..' && is_file($dir_path . $file)) {
        // Check if it's an image file
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])) {
            $images[] = $file;
        }
    }
}

// Return images as JSON
header('Content-Type: application/json');
echo json_encode(['success' => true, 'images' => $images]);
exit();
?>


<?php
session_start();
require_once '../database/db.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Check if coupon_id is provided
if (!isset($_POST['coupon_id']) || empty($_POST['coupon_id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Coupon ID is required']);
    exit();
}

$coupon_id = (int)$_POST['coupon_id'];

// Update coupon clicks
$sql = "UPDATE coupons SET clicks = clicks + 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $coupon_id);

if ($stmt->execute()) {
    // If user is logged in, record this click for potential cashback
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        
        // Check if this coupon has cashback
        $sql = "SELECT c.*, s.name as store_name 
                FROM coupons c 
                JOIN stores s ON c.store_id = s.id 
                WHERE c.id = ? AND c.discount_type = 'cashback'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $coupon_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $coupon = $result->fetch_assoc();
            
            // Calculate potential cashback amount (this is just a placeholder)
            // In a real application, this would be calculated based on the actual purchase amount
            $estimated_amount = 100.00; // Placeholder amount
            $cashback_amount = ($estimated_amount * $coupon['discount_value']) / 100;
            
            // Record the click in a tracking table (you would need to create this table)
            // This is just a placeholder for the concept
            /*
            $sql = "INSERT INTO click_tracking (user_id, coupon_id, click_time, potential_cashback) 
                    VALUES (?, ?, NOW(), ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iid", $user_id, $coupon_id, $cashback_amount);
            $stmt->execute();
            */
        }
    }
    
    echo json_encode(['success' => true]);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Failed to update coupon clicks']);
}
?>


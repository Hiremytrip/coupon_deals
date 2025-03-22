<?php
// This script will run the additional_coupons.sql file to add coupons for American brands
require_once 'database/db.php';

// Read the SQL file
$sql = file_get_contents('database/additional_coupons.sql');

// Execute the SQL
if ($conn->multi_query($sql)) {
    echo "Additional coupons for American brands have been added successfully!";
} else {
    echo "Error adding coupons: " . $conn->error;
}

// Close the connection
$conn->close();
?>


<?php
// This script creates the necessary directory structure for image uploads
// Run this script once to set up the directories

// Define the directory paths
$base_dir = '../assets/';
$images_dir = $base_dir . 'images/';
$stores_dir = $images_dir . 'stores/';
$coupons_dir = $images_dir . 'coupons/';

// Create directories with proper permissions
function create_dir($dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo "Created directory: $dir<br>";
            // Set permissions to ensure web server can write to it
            chmod($dir, 0777);
            echo "Set permissions for: $dir<br>";
            return true;
        } else {
            echo "Failed to create directory: $dir<br>";
            return false;
        }
    } else {
        echo "Directory already exists: $dir<br>";
        // Make sure permissions are correct
        chmod($dir, 0777);
        echo "Updated permissions for: $dir<br>";
        return true;
    }
}

// Create the directory structure
echo "<h2>Creating Directory Structure</h2>";
create_dir($base_dir);
create_dir($images_dir);
create_dir($stores_dir);
create_dir($coupons_dir);

echo "<h2>Directory Setup Complete</h2>";
echo "<p>If you see any 'Failed to create directory' messages above, please check your server permissions.</p>";
echo "<p><a href='images.php'>Go to Image Management</a></p>";
?>


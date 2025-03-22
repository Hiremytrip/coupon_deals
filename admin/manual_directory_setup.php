<?php
// This script creates the necessary directory structure for image uploads
// with absolute paths and detailed error reporting

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the absolute path to the document root
$document_root = $_SERVER['DOCUMENT_ROOT'];
$project_path = '/couponbazar'; // Change this if your project is in a different folder

// Define the absolute directory paths
$base_dir = $document_root . $project_path . '/assets';
$images_dir = $base_dir . '/images';
$stores_dir = $images_dir . '/stores';
$coupons_dir = $images_dir . '/coupons';

// Function to create directory with detailed error reporting
function create_directory($path) {
    echo "Attempting to create directory: $path<br>";
    
    // Check if directory already exists
    if (file_exists($path)) {
        echo "✓ Directory already exists.<br>";
        
        // Check if it's writable
        if (is_writable($path)) {
            echo "✓ Directory is writable.<br>";
        } else {
            echo "✗ Directory is not writable. Attempting to set permissions...<br>";
            if (chmod($path, 0777)) {
                echo "✓ Permissions set successfully.<br>";
            } else {
                echo "✗ Failed to set permissions. Please set manually.<br>";
                echo "Command (run as administrator): chmod 777 $path<br>";
            }
        }
        return true;
    }
    
    // Try to create the directory
    if (mkdir($path, 0777, true)) {
        echo "✓ Directory created successfully.<br>";
        
        // Verify it's writable
        if (is_writable($path)) {
            echo "✓ Directory is writable.<br>";
        } else {
            echo "✗ Directory is not writable. Attempting to set permissions...<br>";
            if (chmod($path, 0777)) {
                echo "✓ Permissions set successfully.<br>";
            } else {
                echo "✗ Failed to set permissions. Please set manually.<br>";
                echo "Command (run as administrator): chmod 777 $path<br>";
            }
        }
        return true;
    } else {
        echo "✗ Failed to create directory.<br>";
        echo "Error: " . error_get_last()['message'] . "<br>";
        return false;
    }
}

// Output HTML header
echo "<!DOCTYPE html>
<html>
<head>
    <title>Directory Setup Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #333; }
        .section { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .manual-steps { background-color: #f9f9f9; padding: 15px; border-left: 4px solid #007bff; }
        pre { background-color: #f5f5f5; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Directory Setup Tool</h1>";

// Display server information
echo "<div class='section'>
    <h2>Server Information</h2>
    <p><strong>Document Root:</strong> $document_root</p>
    <p><strong>Project Path:</strong> $project_path</p>
    <p><strong>PHP Version:</strong> " . phpversion() . "</p>
    <p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>
    <p><strong>Current User:</strong> " . get_current_user() . "</p>
</div>";

// Create directories
echo "<div class='section'>
    <h2>Creating Directory Structure</h2>";

$success = true;

echo "<h3>Step 1: Create assets directory</h3>";
if (!create_directory($base_dir)) {
    $success = false;
}

echo "<h3>Step 2: Create images directory</h3>";
if (!create_directory($images_dir)) {
    $success = false;
}

echo "<h3>Step 3: Create stores directory</h3>";
if (!create_directory($stores_dir)) {
    $success = false;
}

echo "<h3>Step 4: Create coupons directory</h3>";
if (!create_directory($coupons_dir)) {
    $success = false;
}

echo "</div>";

// Manual steps if automatic setup fails
if (!$success) {
    echo "<div class='section'>
        <h2>Manual Setup Instructions</h2>
        <div class='manual-steps'>
            <p>If the automatic setup failed, please follow these manual steps:</p>
            <ol>
                <li>Open File Explorer (Windows) or Finder (Mac)</li>
                <li>Navigate to your XAMPP htdocs folder: <code>$document_root</code></li>
                <li>Navigate to your project folder: <code>$project_path</code></li>
                <li>Create the following folders in this order:
                    <ul>
                        <li><code>assets</code></li>
                        <li><code>assets/images</code></li>
                        <li><code>assets/images/stores</code></li>
                        <li><code>assets/images/coupons</code></li>
                    </ul>
                </li>
                <li>Set permissions to allow writing:
                    <ul>
                        <li>Windows: Right-click each folder → Properties → Security → Edit → Add → Everyone → Check 'Full control' → Apply</li>
                        <li>Mac/Linux: Open Terminal and run: <code>chmod -R 777 $document_root$project_path/assets</code></li>
                    </ul>
                </li>
            </ol>
        </div>
    </div>";
}

// Test file creation
echo "<div class='section'>
    <h2>Testing File Creation</h2>";

$test_file = $coupons_dir . '/test.txt';
echo "Attempting to create test file: $test_file<br>";

if (file_put_contents($test_file, 'This is a test file to verify write permissions.')) {
    echo "<p class='success'>✓ Test file created successfully! Your directories are properly set up.</p>";
    
    // Clean up test file
    if (unlink($test_file)) {
        echo "<p>Test file removed.</p>";
    }
} else {
    echo "<p class='error'>✗ Failed to create test file. Please check directory permissions.</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

echo "</div>";

// Next steps
echo "<div class='section'>
    <h2>Next Steps</h2>";
    
if ($success) {
    echo "<p class='success'>Directory setup completed successfully!</p>";
} else {
    echo "<p class='warning'>Some issues were encountered during setup. Please follow the manual instructions above.</p>";
}

echo "<p>You can now:</p>
    <ul>
        <li><a href='edit-coupon.php?id=" . (isset($_GET['id']) ? $_GET['id'] : '1') . "'>Return to Edit Coupon</a></li>
        <li><a href='images.php'>Go to Image Management</a></li>
        <li><a href='index.php'>Go to Admin Dashboard</a></li>
    </ul>
</div>";

// Close HTML
echo "</div></body></html>";
?>


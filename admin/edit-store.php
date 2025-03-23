<?php
session_start();
require_once '../database/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

// Function to sanitize input (if not already defined)
if (!function_exists('sanitize')) {
    function sanitize($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: stores.php");
    exit();
}

$id = (int)$_GET['id'];

// Get store data
$store = null;
$sql = "SELECT * FROM stores WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $store = $result->fetch_assoc();
} else {
    header("Location: stores.php?error=Store not found");
    exit();
}

// Get all categories for dropdown
$categories = [];
$sql = "SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Get store categories
$store_categories = [];
$sql = "SELECT category_id FROM store_categories WHERE store_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $store_categories[] = $row['category_id'];
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $slug = sanitize($_POST['slug']);
    $description = sanitize($_POST['description']);
    $website_url = sanitize($_POST['website_url']);
    $cashback_percent = (float)$_POST['cashback_percent'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $status = sanitize($_POST['status']);
    $category_ids = isset($_POST['categories']) ? $_POST['categories'] : [];
    $storage_type = isset($_POST['storage_type']) ? sanitize($_POST['storage_type']) : 'file';
    
    // Generate slug if not provided
    if (empty($slug)) {
        $slug = strtolower(str_replace(' ', '-', $name));
        // Remove any non-alphanumeric characters except hyphens
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        // Remove multiple hyphens
        $slug = preg_replace('/-+/', '-', $slug);
    }
    
    // Validate input
    if (empty($name) || empty($website_url)) {
        $error = "Please fill all required fields.";
    } else {
        // Check if slug already exists (excluding current store)
        $sql = "SELECT * FROM stores WHERE slug = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $slug, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Slug already exists. Please choose a different one.";
        } else {
            // Handle logo upload
            $logo = $store['logo']; // Keep existing logo by default
            $logo_data = null; // For database storage
            $logo_updated = false;
            
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $filename = $_FILES['logo']['name'];
                $filesize = $_FILES['logo']['size'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                // Validate file type and size
                if (!in_array($ext, $allowed)) {
                    $error = "Invalid file type. Allowed types: " . implode(', ', $allowed);
                } elseif ($filesize > 5242880) { // 5MB max
                    $error = "File size too large. Maximum size is 5MB.";
                } else {
                    // Create unique filename
                    $new_filename = $slug . '-' . time() . '.' . $ext;
                    $upload_path = '../assets/images/stores/' . $new_filename;
                    
                    // Process based on storage type
                    if ($storage_type == 'file') {
                        // File system storage
                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                            // Resize image if needed
                            if (function_exists('imagecreatefromjpeg')) {
                                resizeImage($upload_path, $upload_path, 300, 300, $ext);
                            }
                            
                            // Delete old logo if it exists and is different
                            if (!empty($store['logo']) && $store['logo'] != $new_filename && file_exists('../assets/images/stores/' . $store['logo'])) {
                                unlink('../assets/images/stores/' . $store['logo']);
                            }
                            
                            $logo = $new_filename;
                            $logo_updated = true;
                        } else {
                            $error = "Error uploading logo.";
                        }
                    } else {
                        // Database storage
                        $logo_data = file_get_contents($_FILES['logo']['tmp_name']);
                        $logo = $new_filename; // Still store the filename for reference
                        $logo_updated = true;
                    }
                }
            }
            
            if (empty($error)) {
                // Update store - SIMPLIFIED APPROACH
                $updateSuccess = false;
                
                // Basic store information update (without logo_data)
                $sql = "UPDATE stores SET 
                        name = '" . $conn->real_escape_string($name) . "',
                        slug = '" . $conn->real_escape_string($slug) . "',
                        description = '" . $conn->real_escape_string($description) . "',
                        website_url = '" . $conn->real_escape_string($website_url) . "',
                        cashback_percent = " . $cashback_percent . ",
                        is_featured = " . $is_featured . ",
                        status = '" . $conn->real_escape_string($status) . "'";
                
                // Add logo to update if it was changed
                if ($logo_updated) {
                    $sql .= ", logo = '" . $conn->real_escape_string($logo) . "'";
                }
                
                // Complete the query
                $sql .= " WHERE id = " . $id;
                
                // Execute the update
                if ($conn->query($sql)) {
                    $updateSuccess = true;
                    
                    // If using database storage and we have logo data, update it separately
                    if ($storage_type == 'database' && $logo_data !== null) {
                        // Check if logo_data column exists
                        $result = $conn->query("SHOW COLUMNS FROM stores LIKE 'logo_data'");
                        if ($result->num_rows == 0) {
                            // Column doesn't exist, create it
                            $conn->query("ALTER TABLE stores ADD COLUMN logo_data MEDIUMBLOB");
                        }
                        
                        // Prepare statement for binary data
                        $logoStmt = $conn->prepare("UPDATE stores SET logo_data = ? WHERE id = ?");
                        if ($logoStmt) {
                            $null = NULL;
                            $logoStmt->bind_param("bi", $null, $id);
                            // Bind the binary data directly
                            $logoStmt->send_long_data(0, $logo_data);
                            $logoStmt->execute();
                        }
                    }
                } else {
                    $error = "Error updating store: " . $conn->error;
                }
                
                // If store update was successful, handle categories
                if ($updateSuccess) {
                    // Delete existing store categories
                    $conn->query("DELETE FROM store_categories WHERE store_id = " . $id);
                    
                    // Insert store categories
                    if (!empty($category_ids)) {
                        $values = [];
                        foreach ($category_ids as $category_id) {
                            $category_id = (int)$category_id; // Ensure it's an integer
                            $values[] = "(" . $id . ", " . $category_id . ")";
                        }
                        
                        if (!empty($values)) {
                            $sql = "INSERT INTO store_categories (store_id, category_id) VALUES " . implode(', ', $values);
                            if (!$conn->query($sql)) {
                                $error = "Warning: Not all categories were saved. " . $conn->error;
                            }
                        }
                    }
                    
                    $success = "Store updated successfully!";
                    
                    // Refresh store data
                    $sql = "SELECT * FROM stores WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $store = $result->fetch_assoc();
                    
                    // Refresh store categories
                    $store_categories = [];
                    $sql = "SELECT category_id FROM store_categories WHERE store_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $store_categories[] = $row['category_id'];
                        }
                    }
                }
            }
        }
    }
}

// Function to resize image
function resizeImage($source, $destination, $maxWidth, $maxHeight, $extension) {
    // Check if GD library is available
    if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor')) {
        return false;
    }
    
    list($width, $height) = getimagesize($source);
    
    // Calculate new dimensions while maintaining aspect ratio
    if ($width > $height) {
        $newWidth = $maxWidth;
        $newHeight = intval($height * $newWidth / $width);
    } else {
        $newHeight = $maxHeight;
        $newWidth = intval($width * $newHeight / $height);
    }
    
    // Create a new image with the new dimensions
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Handle transparency for PNG and GIF
    if ($extension == 'png' || $extension == 'gif') {
        imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
    }
    
    // Load the original image
    $originalImage = null;
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            if (function_exists('imagecreatefromjpeg')) {
                $originalImage = imagecreatefromjpeg($source);
            }
            break;
        case 'png':
            if (function_exists('imagecreatefrompng')) {
                $originalImage = imagecreatefrompng($source);
            }
            break;
        case 'gif':
            if (function_exists('imagecreatefromgif')) {
                $originalImage = imagecreatefromgif($source);
            }
            break;
        case 'webp':
            if (function_exists('imagecreatefromwebp')) {
                $originalImage = imagecreatefromwebp($source);
            }
            break;
        default:
            return false;
    }
    
    if (!$originalImage) {
        return false;
    }
    
    // Resize the image
    imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Save the resized image
    $result = false;
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            if (function_exists('imagejpeg')) {
                $result = imagejpeg($newImage, $destination, 85);
            }
            break;
        case 'png':
            if (function_exists('imagepng')) {
                $result = imagepng($newImage, $destination, 8);
            }
            break;
        case 'gif':
            if (function_exists('imagegif')) {
                $result = imagegif($newImage, $destination);
            }
            break;
        case 'webp':
            if (function_exists('imagewebp')) {
                $result = imagewebp($newImage, $destination, 85);
            }
            break;
    }
    
    // Free up memory
    if ($originalImage) {
        imagedestroy($originalImage);
    }
    imagedestroy($newImage);
    
    return $result;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Store - CouponDeals Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .image-preview {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            margin-top: 10px;
        }
        #preview-container {
            display: none;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block admin-sidebar collapse">
                <div class="pt-3 text-center">
                    <a href="index.php" class="text-white text-decoration-none">
                        <h4>CouponDeals Admin</h4>
                    </a>
                </div>
                <hr class="text-white">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">
                            <i class="fas fa-tags"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="stores.php">
                            <i class="fas fa-store"></i> Stores
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="coupons.php">
                            <i class="fas fa-ticket-alt"></i> Coupons
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transactions.php">
                            <i class="fas fa-money-bill-wave"></i> Transactions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item mt-5">
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit Store</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="stores.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Stores
                        </a>
                    </div>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form action="edit-store.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Store Name*</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $store['name']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" class="form-control" id="slug" name="slug" value="<?php echo $store['slug']; ?>">
                                    <small class="text-muted">Leave empty to generate automatically</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo $store['description']; ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="website_url" class="form-label">Website URL*</label>
                                    <input type="url" class="form-control" id="website_url" name="website_url" value="<?php echo $store['website_url']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cashback_percent" class="form-label">Cashback Percentage*</label>
                                    <input type="number" class="form-control" id="cashback_percent" name="cashback_percent" step="0.1" value="<?php echo $store['cashback_percent']; ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="logo" class="form-label">Logo</label>
                                    <?php if (!empty($store['logo'])): ?>
                                        <div class="mb-2">
                                            <?php if (isset($store['logo_data']) && !empty($store['logo_data'])): ?>
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($store['logo_data']); ?>" alt="<?php echo $store['name']; ?>" width="100" class="img-thumbnail">
                                            <?php else: ?>
                                                <img src="../assets/images/stores/<?php echo $store['logo']; ?>" alt="<?php echo $store['name']; ?>" width="100" class="img-thumbnail">
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="logo" name="logo" accept="image/jpeg,image/png,image/gif,image/webp">
                                    <small class="text-muted">Leave empty to keep current logo. Max size: 5MB</small>
                                    <div id="preview-container">
                                        <img id="image-preview" class="image-preview" src="#" alt="Preview">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status*</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active" <?php echo $store['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo $store['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Image Storage Type</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="storage_type" id="storage_file" value="file" checked>
                                        <label class="form-check-label" for="storage_file">
                                            Store in File System
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="storage_type" id="storage_db" value="database">
                                        <label class="form-check-label" for="storage_db">
                                            Store in Database
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Categories</label>
                                <div class="row">
                                    <?php foreach ($categories as $category): ?>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="category_<?php echo $category['id']; ?>" name="categories[]" value="<?php echo $category['id']; ?>" <?php echo in_array($category['id'], $store_categories) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="category_<?php echo $category['id']; ?>">
                                                    <?php echo $category['name']; ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" <?php echo $store['is_featured'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_featured">Featured Store</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Update Store</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Image preview functionality
        document.getElementById('logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                const previewContainer = document.getElementById('preview-container');
                const imagePreview = document.getElementById('image-preview');
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            }
        });
        
        // Auto-generate slug from name
        document.getElementById('name').addEventListener('keyup', function() {
            const nameField = this;
            const slugField = document.getElementById('slug');
            
            // Only auto-generate if slug field is empty or hasn't been manually edited
            if (slugField.value === '' || slugField.dataset.autoGenerated === 'true') {
                const slug = nameField.value.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')   // Replace non-alphanumeric chars with hyphens
                    .replace(/^-+|-+$/g, '')       // Remove leading/trailing hyphens
                    .replace(/-+/g, '-');          // Replace multiple hyphens with single hyphen
                
                slugField.value = slug;
                slugField.dataset.autoGenerated = 'true';
            }
        });
        
        // Mark slug as manually edited
        document.getElementById('slug').addEventListener('input', function() {
            this.dataset.autoGenerated = 'false';
        });
    </script>
</body>
</html>
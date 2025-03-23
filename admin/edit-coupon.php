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

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: coupons.php");
    exit();
}

$id = (int)$_GET['id'];

// Get coupon data
$coupon = null;
$sql = "SELECT * FROM coupons WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $coupon = $result->fetch_assoc();
} else {
    header("Location: coupons.php?error=Coupon not found");
    exit();
}

// Get all stores for dropdown
$stores = [];
$sql = "SELECT * FROM stores WHERE status = 'active' ORDER BY name ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $stores[] = $row;
    }
}

// Check if image_data column exists in coupons table
function checkImageDataColumn($conn) {
    $result = $conn->query("SHOW COLUMNS FROM coupons LIKE 'image_data'");
    if ($result->num_rows == 0) {
        // Column doesn't exist, create it
        $conn->query("ALTER TABLE coupons ADD COLUMN image_data MEDIUMBLOB");
        return "Added image_data column to coupons table.";
    }
    return "";
}

// Check and add image_data column if needed
$columnMessage = checkImageDataColumn($conn);
if (!empty($columnMessage)) {
    $success .= " " . $columnMessage;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $store_id = (int)$_POST['store_id'];
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $coupon_code = sanitize($_POST['coupon_code']);
    $discount_type = sanitize($_POST['discount_type']);
    $discount_value = (float)$_POST['discount_value'];
    $expiry_date = $_POST['expiry_date'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $status = sanitize($_POST['status']);
    $image = isset($_POST['image']) ? sanitize($_POST['image']) : '';
    $storage_type = isset($_POST['storage_type']) ? sanitize($_POST['storage_type']) : 'file';

    // Validate input
    if (empty($store_id) || empty($title) || empty($discount_type) || empty($discount_value) || empty($expiry_date) || empty($status)) {
        $error = "Please fill all required fields.";
    } else {
        // Handle image upload if no existing image was selected
        $image_data = null; // For database storage
        
        if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image_upload']['name'];
            $filesize = $_FILES['image_upload']['size'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                $error = "Invalid file type. Allowed types: " . implode(', ', $allowed);
            } elseif ($filesize > 5242880) { // 5MB max
                $error = "File size too large. Maximum size is 5MB.";
            } else {
                // Generate a safe filename - remove special characters
                $safe_title = preg_replace('/[^a-zA-Z0-9-_]/', '', str_replace(' ', '-', strtolower($title)));
                $new_filename = $safe_title . '-' . time() . '.' . $ext;
                $upload_dir = '../assets/images/coupons/';
                $upload_path = $upload_dir . $new_filename;
                
                // Process based on storage type
                if ($storage_type == 'file') {
                    // File system storage
                    // Check if directories exist
                    if (!file_exists('../assets/')) {
                        $error = "Assets directory does not exist. <a href='manual_directory_setup.php?id=$id'>Click here to set up directories</a>";
                    } elseif (!file_exists('../assets/images/')) {
                        $error = "Images directory does not exist. <a href='manual_directory_setup.php?id=$id'>Click here to set up directories</a>";
                    } elseif (!file_exists($upload_dir)) {
                        $error = "Coupons directory does not exist. <a href='manual_directory_setup.php?id=$id'>Click here to set up directories</a>";
                    } elseif (!is_writable($upload_dir)) {
                        $error = "Coupons directory is not writable. <a href='manual_directory_setup.php?id=$id'>Click here to set up directories</a>";
                    } else {
                        if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $upload_path)) {
                            // Resize image if needed
                            if (function_exists('imagecreatefromjpeg')) {
                                resizeImage($upload_path, $upload_path, 800, 600, $ext);
                            }
                            
                            // Delete old image if it exists and is different
                            if (!empty($coupon['image']) && $coupon['image'] != $new_filename && file_exists($upload_dir . $coupon['image'])) {
                                unlink($upload_dir . $coupon['image']);
                            }
                            
                            $image = $new_filename;
                        } else {
                            $error = "Error uploading image. <a href='manual_directory_setup.php?id=$id'>Click here to check directory permissions</a>";
                        }
                    }
                } else {
                    // Database storage
                    $image_data = file_get_contents($_FILES['image_upload']['tmp_name']);
                    $image = $new_filename; // Still store the filename for reference
                }
            }
        } elseif (empty($image) && empty($coupon['image'])) {
            // No image selected and no previous image
            $image = ''; // Clear the image field
        } else {
            // Keep existing image
            $image = $coupon['image'];
        }
        
        if (empty($error)) {
            // Update coupon
            if ($storage_type == 'file' || $image_data === null) {
                // Standard update without image data
                $sql = "UPDATE coupons SET 
                        store_id = ?, 
                        title = ?, 
                        description = ?, 
                        coupon_code = ?, 
                        discount_type = ?, 
                        discount_value = ?, 
                        expiry_date = ?, 
                        image = ?, 
                        is_featured = ?, 
                        status = ? 
                        WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issssdsisii", $store_id, $title, $description, $coupon_code, $discount_type, $discount_value, $expiry_date, $image, $is_featured, $status, $id);
            } else {
                // Update with image data in database
                $sql = "UPDATE coupons SET 
                        store_id = ?, 
                        title = ?, 
                        description = ?, 
                        coupon_code = ?, 
                        discount_type = ?, 
                        discount_value = ?, 
                        expiry_date = ?, 
                        image = ?, 
                        is_featured = ?, 
                        status = ? 
                        WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issssdsisii", $store_id, $title, $description, $coupon_code, $discount_type, $discount_value, $expiry_date, $image, $is_featured, $status, $id);
                
                // Execute the first update
                if ($stmt->execute()) {
                    // Update the image_data separately
                    $logoStmt = $conn->prepare("UPDATE coupons SET image_data = ? WHERE id = ?");
                    if ($logoStmt) {
                        $null = NULL;
                        $logoStmt->bind_param("bi", $null, $id);
                        // Bind the binary data directly
                        $logoStmt->send_long_data(0, $image_data);
                        $logoStmt->execute();
                    }
                }
            }
            
            if ($stmt->execute()) {
                $success = "Coupon updated successfully!";
                
                // Refresh coupon data
                $sql = "SELECT * FROM coupons WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $coupon = $result->fetch_assoc();
            } else {
                $error = "Error: " . $stmt->error;
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

// Function to sanitize input (if not already defined)
if (!function_exists('sanitize')) {
    function sanitize($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}

// Check if we're returning from image upload
$returnUrl = isset($_GET['return']) ? $_GET['return'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Coupon - CouponDeals Admin</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .image-preview {
        max-width: 200px;
        max-height: 150px;
        margin-top: 10px;
    }
    .image-select-card {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .image-select-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .image-container {
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #f8f9fa;
    }
    .image-container img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
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
                      <a class="nav-link" href="stores.php">
                          <i class="fas fa-store"></i> Stores
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link active" href="coupons.php">
                          <i class="fas fa-ticket-alt"></i> Coupons
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="images.php">
                          <i class="fas fa-images"></i> Images
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
                  <h1 class="h2">Edit Coupon</h1>
                  <div class="btn-toolbar mb-2 mb-md-0">
                      <a href="coupons.php" class="btn btn-sm btn-outline-secondary">
                          <i class="fas fa-arrow-left"></i> Back to Coupons
                      </a>
                  </div>
              </div>
              
              <?php if (!empty($error)): ?>
                  <div class="alert alert-danger"><?php echo $error; ?></div>
              <?php endif; ?>
              
              <?php if (!empty($success)): ?>
                  <div class="alert alert-success"><?php echo $success; ?></div>
              <?php endif; ?>
              
              <?php if (!empty($returnUrl)): ?>
                  <div class="alert alert-info">
                      You've been redirected from the image upload page. Continue editing your coupon below.
                  </div>
              <?php endif; ?>
              
              <div class="card">
                  <div class="card-body">
                      <form action="edit-coupon.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                          <div class="row">
                              <div class="col-md-6 mb-3">
                                  <label for="store_id" class="form-label">Store*</label>
                                  <select class="form-select" id="store_id" name="store_id" required>
                                      <option value="">Select Store</option>
                                      <?php foreach ($stores as $store): ?>
                                          <option value="<?php echo $store['id']; ?>" <?php echo $coupon['store_id'] == $store['id'] ? 'selected' : ''; ?>><?php echo $store['name']; ?></option>
                                      <?php endforeach; ?>
                                  </select>
                              </div>
                              <div class="col-md-6 mb-3">
                                  <label for="title" class="form-label">Title*</label>
                                  <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($coupon['title']); ?>" required>
                              </div>
                          </div>
                          <div class="mb-3">
                              <label for="description" class="form-label">Description</label>
                              <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($coupon['description']); ?></textarea>
                          </div>
                          <div class="row">
                              <div class="col-md-6 mb-3">
                                  <label for="coupon_code" class="form-label">Coupon Code</label>
                                  <input type="text" class="form-control" id="coupon_code" name="coupon_code" value="<?php echo htmlspecialchars($coupon['coupon_code']); ?>">
                                  <small class="text-muted">Leave empty if no code is required</small>
                              </div>
                              <div class="col-md-6 mb-3">
                                  <label for="discount_type" class="form-label">Discount Type*</label>
                                  <select class="form-select" id="discount_type" name="discount_type" required>
                                      <option value="percentage" <?php echo $coupon['discount_type'] == 'percentage' ? 'selected' : ''; ?>>Percentage</option>
                                      <option value="fixed" <?php echo $coupon['discount_type'] == 'fixed' ? 'selected' : ''; ?>>Fixed Amount</option>
                                      <option value="cashback" <?php echo $coupon['discount_type'] == 'cashback' ? 'selected' : ''; ?>>Cashback</option>
                                  </select>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-6 mb-3">
                                  <label for="discount_value" class="form-label">Discount Value*</label>
                                  <input type="number" class="form-control" id="discount_value" name="discount_value" step="0.01" value="<?php echo $coupon['discount_value']; ?>" required>
                              </div>
                              <div class="col-md-6 mb-3">
                                  <label for="expiry_date" class="form-label">Expiry Date*</label>
                                  <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?php echo $coupon['expiry_date']; ?>" required>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-6 mb-3">
                                  <label class="form-label">Coupon Image</label>
                                  <div class="input-group">
                                      <input type="hidden" id="image" name="image" value="<?php echo htmlspecialchars($coupon['image'] ?? ''); ?>">
                                      <button class="btn btn-outline-secondary image-selector-btn" type="button" data-target="image" data-type="coupon">
                                          <i class="fas fa-images"></i> Select Existing Image
                                      </button>
                                  </div>
                                  <div class="mt-2">
                                      <label class="form-label">Or upload new image:</label>
                                      <input type="file" class="form-control" id="image_upload" name="image_upload" accept="image/*">
                                  </div>
                                  <?php if (!empty($coupon['image'])): ?>
                                      <?php if (isset($coupon['image_data']) && !empty($coupon['image_data'])): ?>
                                          <img id="image_preview" src="data:image/jpeg;base64,<?php echo base64_encode($coupon['image_data']); ?>" alt="Coupon Image Preview" class="image-preview mt-2">
                                      <?php else: ?>
                                          <img id="image_preview" src="../assets/images/coupons/<?php echo htmlspecialchars($coupon['image']); ?>" alt="Coupon Image Preview" class="image-preview mt-2">
                                      <?php endif; ?>
                                  <?php else: ?>
                                      <img id="image_preview" src="/placeholder.svg" alt="Coupon Image Preview" class="image-preview mt-2" style="display: none;">
                                  <?php endif; ?>
                              </div>
                              <div class="col-md-6 mb-3">
                                  <label for="status" class="form-label">Status*</label>
                                  <select class="form-select" id="status" name="status" required>
                                      <option value="active" <?php echo $coupon['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                      <option value="inactive" <?php echo $coupon['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
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
                          <div class="mb-3 form-check">
                              <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" <?php echo $coupon['is_featured'] == 1 ? 'checked' : ''; ?>>
                              <label class="form-check-label" for="is_featured">Mark as Featured</label>
                          </div>
                          <button type="submit" class="btn btn-primary">Save Changes</button>
                      </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
  
  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Image preview functionality
    document.getElementById('image_upload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            const imagePreview = document.getElementById('image_preview');
            
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            
            reader.readAsDataURL(file);
        }
    });
  </script>
</body>
</html>
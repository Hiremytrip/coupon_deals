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

// Process image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    $image_type = sanitize($_POST['image_type']);
    
    // Validate image type
    if (!in_array($image_type, ['store', 'coupon'])) {
        $error = "Invalid image type selected.";
    } else {
        // Set upload directory based on image type
        $upload_dir = $image_type == 'store' ? '../assets/images/stores/' : '../assets/images/coupons/';
        
        // Check if directory exists, if not create it
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            
            if (in_array(strtolower($ext), $allowed)) {
                // Use custom filename if provided
                $new_filename = !empty($_POST['custom_filename']) ? sanitize($_POST['custom_filename']) . '.' . $ext : $filename;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $success = "Image uploaded successfully!";
                } else {
                    $error = "Error uploading image.";
                }
            } else {
                $error = "Invalid file type. Allowed types: " . implode(', ', $allowed);
            }
        } else {
            $error = "Please select an image to upload.";
        }
    }
}

// Process image deletion
if (isset($_GET['delete']) && !empty($_GET['delete']) && isset($_GET['type'])) {
    $filename = sanitize($_GET['delete']);
    $type = sanitize($_GET['type']);
    
    if (!in_array($type, ['store', 'coupon'])) {
        $error = "Invalid image type.";
    } else {
        $file_path = $type == 'store' ? '../assets/images/stores/' . $filename : '../assets/images/coupons/' . $filename;
        
        if (file_exists($file_path)) {
            if (unlink($file_path)) {
                $success = "Image deleted successfully!";
            } else {
                $error = "Error deleting image.";
            }
        } else {
            $error = "File not found.";
        }
    }
}

// Get all store images
$store_images = [];
$store_dir = '../assets/images/stores/';
if (file_exists($store_dir)) {
    $store_images = array_diff(scandir($store_dir), ['.', '..']);
}

// Get all coupon images
$coupon_images = [];
$coupon_dir = '../assets/images/coupons/';
if (file_exists($coupon_dir)) {
    $coupon_images = array_diff(scandir($coupon_dir), ['.', '..']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Images - CouponDeals Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .image-container {
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .image-container img {
            max-height: 140px;
            max-width: 100%;
            object-fit: contain;
        }
        .image-card {
            margin-bottom: 20px;
        }
        .image-actions {
            display: flex;
            justify-content: space-between;
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
                        <a class="nav-link" href="stores.php">
                            <i class="fas fa-store"></i> Stores
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="coupons.php">
                            <i class="fas fa-ticket-alt"></i> Coupons
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="images.php">
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
                    <h1 class="h2">Manage Images</h1>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Upload Form -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Upload New Image</h5>
                    </div>
                    <div class="card-body">
                        <form action="images.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="image_type" class="form-label">Image Type*</label>
                                    <select class="form-select" id="image_type" name="image_type" required>
                                        <option value="store">Store Logo</option>
                                        <option value="coupon">Coupon Image</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="custom_filename" class="form-label">Custom Filename (optional)</label>
                                    <input type="text" class="form-control" id="custom_filename" name="custom_filename" placeholder="e.g., amazon or nike-summer">
                                    <small class="text-muted">Leave empty to use original filename</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Select Image*</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                <small class="text-muted">Allowed formats: JPG, JPEG, PNG, GIF</small>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="upload" class="btn btn-primary">Upload Image</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Image Gallery -->
                <ul class="nav nav-tabs mb-4" id="imageTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="store-tab" data-bs-toggle="tab" data-bs-target="#store-images" type="button" role="tab" aria-controls="store-images" aria-selected="true">
                            Store Logos (<?php echo count($store_images); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="coupon-tab" data-bs-toggle="tab" data-bs-target="#coupon-images" type="button" role="tab" aria-controls="coupon-images" aria-selected="false">
                            Coupon Images (<?php echo count($coupon_images); ?>)
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="imageTabContent">
                    <!-- Store Images -->
                    <div class="tab-pane fade show active" id="store-images" role="tabpanel" aria-labelledby="store-tab">
                        <div class="row">
                            <?php if (empty($store_images)): ?>
                                <div class="col-12">
                                    <div class="alert alert-info">No store logo images found.</div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($store_images as $image): ?>
                                    <div class="col-md-3 col-sm-4 col-6">
                                        <div class="card image-card">
                                            <div class="image-container">
                                                <img src="../assets/images/stores/<?php echo $image; ?>" alt="<?php echo $image; ?>">
                                            </div>
                                            <div class="card-body p-2">
                                                <p class="card-text small text-truncate" title="<?php echo $image; ?>"><?php echo $image; ?></p>
                                                <div class="image-actions">
                                                    <a href="../assets/images/stores/<?php echo $image; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-success copy-path" data-path="assets/images/stores/<?php echo $image; ?>">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <a href="images.php?delete=<?php echo $image; ?>&type=store" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this image?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Coupon Images -->
                    <div class="tab-pane fade" id="coupon-images" role="tabpanel" aria-labelledby="coupon-tab">
                        <div class="row">
                            <?php if (empty($coupon_images)): ?>
                                <div class="col-12">
                                    <div class="alert alert-info">No coupon images found.</div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($coupon_images as $image): ?>
                                    <div class="col-md-3 col-sm-4 col-6">
                                        <div class="card image-card">
                                            <div class="image-container">
                                                <img src="../assets/images/coupons/<?php echo $image; ?>" alt="<?php echo $image; ?>">
                                            </div>
                                            <div class="card-body p-2">
                                                <p class="card-text small text-truncate" title="<?php echo $image; ?>"><?php echo $image; ?></p>
                                                <div class="image-actions">
                                                    <a href="../assets/images/coupons/<?php echo $image; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-success copy-path" data-path="assets/images/coupons/<?php echo $image; ?>">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <a href="images.php?delete=<?php echo $image; ?>&type=coupon" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this image?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS for copying image paths -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event to copy path buttons
            const copyButtons = document.querySelectorAll('.copy-path');
            copyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const path = this.getAttribute('data-path');
                    navigator.clipboard.writeText(path).then(() => {
                        // Change button text temporarily
                        const originalHTML = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check"></i>';
                        this.classList.remove('btn-outline-success');
                        this.classList.add('btn-success');
                        
                        // Reset button after 1.5 seconds
                        setTimeout(() => {
                            this.innerHTML = originalHTML;
                            this.classList.remove('btn-success');
                            this.classList.add('btn-outline-success');
                        }, 1500);
                    });
                });
            });
        });
    </script>
</body>
</html>


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
    
    // Generate slug if not provided
    if (empty($slug)) {
        $slug = strtolower(str_replace(' ', '-', $name));
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
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['logo']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                
                if (in_array(strtolower($ext), $allowed)) {
                    $new_filename = $slug . '.' . $ext;
                    $upload_path = '../assets/images/stores/' . $new_filename;
                    
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                        $logo = $new_filename;
                    } else {
                        $error = "Error uploading logo.";
                    }
                } else {
                    $error = "Invalid file type. Allowed types: " . implode(', ', $allowed);
                }
            }
            
            if (empty($error)) {
                // Update store
                $sql = "UPDATE stores SET name = ?, slug = ?, logo = ?, description = ?, website_url = ?, cashback_percent = ?, is_featured = ?, status = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssdsi", $name, $slug, $logo, $description, $website_url, $cashback_percent, $is_featured, $status, $id);
                
                if ($stmt->execute()) {
                    // Delete existing store categories
                    $sql = "DELETE FROM store_categories WHERE store_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    
                    // Insert store categories
                    if (!empty($category_ids)) {
                        $values = [];
                        $types = '';
                        $params = [];
                        
                        foreach ($category_ids as $category_id) {
                            $values[] = "(?, ?)";
                            $types .= "ii";
                            $params[] = $id;
                            $params[] = $category_id;
                        }
                        
                        $sql = "INSERT INTO store_categories (store_id, category_id) VALUES " . implode(', ', $values);
                        $stmt = $conn->prepare($sql);
                        
                        // Bind parameters dynamically
                        $bind_params = array($types);
                        foreach ($params as $key => $value) {
                            $bind_params[] = &$params[$key];
                        }
                        call_user_func_array(array($stmt, 'bind_param'), $bind_params);
                        
                        $stmt->execute();
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
                } else {
                    $error = "Error: " . $stmt->error;
                }
            }
        }
    }
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
                                            <img src="../assets/images/stores/<?php echo $store['logo']; ?>" alt="<?php echo $store['name']; ?>" width="100" class="img-thumbnail">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="logo" name="logo">
                                    <small class="text-muted">Leave empty to keep current logo</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status*</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active" <?php echo $store['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo $store['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
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
</body>
</html>


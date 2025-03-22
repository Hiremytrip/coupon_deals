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

// Get all categories for dropdown
$categories = [];
$sql = "SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
      $categories[] = $row;
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
  $logo = sanitize($_POST['logo']);
  
  // Generate slug if not provided
  if (empty($slug)) {
      $slug = strtolower(str_replace(' ', '-', $name));
  }
  
  // Validate input
  if (empty($name) || empty($website_url)) {
      $error = "Please fill all required fields.";
  } else {
      // Check if slug already exists
      $sql = "SELECT * FROM stores WHERE slug = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $slug);
      $stmt->execute();
      $result = $stmt->get_result();
      
      if ($result->num_rows > 0) {
          $error = "Slug already exists. Please choose a different one.";
      } else {
          // Handle logo upload if no existing logo was selected
          if (empty($logo) && isset($_FILES['logo_upload']) && $_FILES['logo_upload']['error'] == 0) {
              $allowed = ['jpg', 'jpeg', 'png', 'gif'];
              $filename = $_FILES['logo_upload']['name'];
              $ext = pathinfo($filename, PATHINFO_EXTENSION);
              
              if (in_array(strtolower($ext), $allowed)) {
                  $new_filename = $slug . '.' . $ext;
                  $upload_path = '../assets/images/stores/' . $new_filename;
                  
                  if (move_uploaded_file($_FILES['logo_upload']['tmp_name'], $upload_path)) {
                      $logo = $new_filename;
                  } else {
                      $error = "Error uploading logo.";
                  }
              } else {
                  $error = "Invalid file type. Allowed types: " . implode(', ', $allowed);
              }
          }
          
          if (empty($error)) {
              // Insert new store
              $sql = "INSERT INTO stores (name, slug, logo, description, website_url, cashback_percent, is_featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("sssssdis", $name, $slug, $logo, $description, $website_url, $cashback_percent, $is_featured, $status);
              
              if ($stmt->execute()) {
                  $store_id = $stmt->insert_id;
                  
                  // Insert store categories
                  if (!empty($category_ids)) {
                      $values = [];
                      $types = '';
                      $params = [];
                      
                      foreach ($category_ids as $category_id) {
                          $values[] = "(?, ?)";
                          $types .= "ii";
                          $params[] = $store_id;
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
                  
                  $success = "Store added successfully!";
                  // Redirect to stores page
                  header("Location: stores.php?success=Store added successfully");
                  exit();
              } else {
                  $error = "Error: " . $stmt->error;
              }
          }
      }
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
  <title>Add Store - CouponDeals Admin</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .image-preview {
        max-width: 100px;
        max-height: 100px;
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
                  <h1 class="h2">Add New Store</h1>
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
              
              <?php if (!empty($returnUrl)): ?>
                  <div class="alert alert-info">
                      You've been redirected from the image upload page. Continue adding your store below.
                  </div>
              <?php endif; ?>
              
              <div class="card">
                  <div class="card-body">
                      <form action="add-store.php" method="POST" enctype="multipart/form-data">
                          <div class="row">
                              <div class="col-md-6 mb-3">
                                  <label for="name" class="form-label">Store Name*</label>
                                  <input type="text" class="form-control" id="name" name="name" required>
                              </div>
                              <div class="col-md-6 mb-3">
                                  <label for="slug" class="form-label">Slug</label>
                                  <input type="text" class="form-control" id="slug" name="slug">
                                  <small class="text-muted">Leave empty to generate automatically</small>
                              </div>
                          </div>
                          <div class="mb-3">
                              <label for="description" class="form-label">Description</label>
                              <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                          </div>
                          <div class="row">
                              <div class="col-md-6 mb-3">
                                  <label for="website_url" class="form-label">Website URL*</label>
                                  <input type="url" class="form-control" id="website_url" name="website_url" required>
                              </div>
                              <div class="col-md-6 mb-3">
                                  <label for="cashback_percent" class="form-label">Cashback Percentage*</label>
                                  <input type="number" class="form-control" id="cashback_percent" name="cashback_percent" step="0.1" required>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-6 mb-3">
                                  <label class="form-label">Store Logo</label>
                                  <div class="input-group">
                                      <input type="hidden" id="logo" name="logo" value="">
                                      <button class="btn btn-outline-secondary image-selector-btn" type="button" data-target="logo" data-type="store">
                                          <i class="fas fa-images"></i> Select Existing Logo
                                      </button>
                                  </div>
                                  <div class="mt-2">
                                      <label class="form-label">Or upload new logo:</label>
                                      <input type="file" class="form-control" id="logo_upload" name="logo_upload" accept="image/*">
                                  </div>
                                  <img id="logo_preview" src="/placeholder.svg" alt="Logo Preview" class="image-preview mt-2" style="display: none;">
                              </div>
                              <div class="col-md-6 mb-3">
                                  <label for="status" class="form-label">Status*</label>
                                  <select class="form-select" id="status" name="status" required>
                                      <option value="active">Active</option>
                                      <option value="inactive">Inactive</option>
                                  </select>
                              </div>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Categories</label>
                              <div class="row">
                                  <?php foreach ($categories as $category): ?>
                                      <div class="col-md-4 mb-2">
                                          <div class="form-check">
                                              <input class="form-check-input" type="checkbox" id="category_<?php echo $category['id']; ?>" name="categories[]" value="<?php echo $category['id']; ?>">
                                              <label class="form-check-label" for="category_<?php echo $category['id']; ?>">
                                                  <?php echo $category['name']; ?>
                                              </label>
                                          </div>
                                      </div>
                                  <?php endforeach; ?>
                              </div>
                          </div>
                          <div class="mb-3 form-check">
                              <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured">
                              <label class="form-check-label" for="is_featured">Featured Store</label>
                          </div>
                          <div class="d-grid">
                              <button type="submit" class="btn btn-primary">Add Store</button>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Image Selector JS -->
  <script src="js/image-selector.js"></script>
  <script>
      // Preview uploaded image
      document.getElementById('logo_upload').addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (file) {
              const reader = new FileReader();
              reader.onload = function(e) {
                  const preview = document.getElementById('logo_preview');
                  preview.src = e.target.result;
                  preview.style.display = 'block';
              }
              reader.readAsDataURL(file);
              // Clear the selected image from the selector
              document.getElementById('logo').value = '';
          }
      });
  </script>
</body>
</html>


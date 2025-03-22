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

// Get current settings
$settings = [
    'site_name' => 'CouponDeals',
    'site_description' => 'India\'s Trusted Coupons, Offers & Cashback Website',
    'admin_email' => 'admin@example.com',
    'min_withdrawal' => 100,
    'cashback_processing_days' => 30,
    'maintenance_mode' => 0
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $site_name = sanitize($_POST['site_name']);
    $site_description = sanitize($_POST['site_description']);
    $admin_email = sanitize($_POST['admin_email']);
    $min_withdrawal = (float)$_POST['min_withdrawal'];
    $cashback_processing_days = (int)$_POST['cashback_processing_days'];
    $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
    
    // Validate input
    if (empty($site_name) || empty($site_description) || empty($admin_email)) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Update settings
        $settings = [
            'site_name' => $site_name,
            'site_description' => $site_description,
            'admin_email' => $admin_email,
            'min_withdrawal' => $min_withdrawal,
            'cashback_processing_days' => $cashback_processing_days,
            'maintenance_mode' => $maintenance_mode
        ];
        
        $success = "Settings updated successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - CouponDeals Admin</title>
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
                        <a class="nav-link" href="transactions.php">
                            <i class="fas fa-money-bill-wave"></i> Transactions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="settings.php">
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
                    <h1 class="h2">Website Settings</h1>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form action="settings.php" method="POST">
                            <h5 class="mb-3">General Settings</h5>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="site_name" class="form-label">Site Name*</label>
                                    <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo $settings['site_name']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="admin_email" class="form-label">Admin Email*</label>
                                    <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?php echo $settings['admin_email']; ?>" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="site_description" class="form-label">Site Description*</label>
                                    <textarea class="form-control" id="site_description" name="site_description" rows="2" required><?php echo $settings['site_description']; ?></textarea>
                                </div>
                            </div>
                            
                            <h5 class="mb-3">Cashback Settings</h5>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="min_withdrawal" class="form-label">Minimum Withdrawal Amount (₹)*</label>
                                    <input type="number" class="form-control" id="min_withdrawal" name="min_withdrawal" value="<?php echo $settings['min_withdrawal']; ?>" step="0.01" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cashback_processing_days" class="form-label">Cashback Processing Days*</label>
                                    <input type="number" class="form-control" id="cashback_processing_days" name="cashback_processing_days" value="<?php echo $settings['cashback_processing_days']; ?>" required>
                                    <small class="text-muted">Number of days before cashback is approved</small>
                                </div>
                            </div>
                            
                            <h5 class="mb-3">Maintenance Settings</h5>
                            <div class="row mb-4">
                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" <?php echo $settings['maintenance_mode'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="maintenance_mode">
                                            Enable Maintenance Mode
                                        </label>
                                        <small class="d-block text-muted">When enabled, only administrators can access the website</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">System Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                                <p><strong>MySQL Version:</strong> <?php echo $conn->server_info; ?></p>
                                <p><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Current Date/Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                                <p><strong>Website Directory:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
                                <p><strong>Session Timeout:</strong> <?php echo ini_get('session.gc_maxlifetime'); ?> seconds</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


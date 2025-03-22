<?php
session_start();
require_once '../database/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get dashboard statistics
$totalUsers = 0;
$totalStores = 0;
$totalCoupons = 0;
$totalTransactions = 0;

// Count users
$sql = "SELECT COUNT(*) as count FROM users";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalUsers = $row['count'];
}

// Count stores
$sql = "SELECT COUNT(*) as count FROM stores";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalStores = $row['count'];
}

// Count coupons
$sql = "SELECT COUNT(*) as count FROM coupons";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalCoupons = $row['count'];
}

// Count transactions
$sql = "SELECT COUNT(*) as count FROM transactions";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $totalTransactions = $row['count'];
}

// Get recent coupons
$recentCoupons = [];
$sql = "SELECT c.*, s.name as store_name 
        FROM coupons c 
        JOIN stores s ON c.store_id = s.id 
        ORDER BY c.created_at DESC 
        LIMIT 5";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $recentCoupons[] = $row;
    }
}

// Get recent transactions
$recentTransactions = [];
$sql = "SELECT t.*, u.username, c.title as coupon_title 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        LEFT JOIN coupons c ON t.coupon_id = c.id 
        ORDER BY t.transaction_date DESC 
        LIMIT 5";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $recentTransactions[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CouponDeals</title>
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
                        <a class="nav-link active" href="index.php">
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
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="../index.php" class="btn btn-sm btn-outline-secondary" target="_blank">
                                <i class="fas fa-external-link-alt"></i>  target="_blank">
                                <i class="fas fa-external-link-alt"></i> View Site
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Users</h6>
                                        <h2 class="mb-0"><?php echo $totalUsers; ?></h2>
                                    </div>
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a href="users.php" class="text-white text-decoration-none small">View Details</a>
                                <i class="fas fa-angle-right text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Stores</h6>
                                        <h2 class="mb-0"><?php echo $totalStores; ?></h2>
                                    </div>
                                    <i class="fas fa-store fa-2x"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a href="stores.php" class="text-white text-decoration-none small">View Details</a>
                                <i class="fas fa-angle-right text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card bg-warning text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Coupons</h6>
                                        <h2 class="mb-0"><?php echo $totalCoupons; ?></h2>
                                    </div>
                                    <i class="fas fa-ticket-alt fa-2x"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a href="coupons.php" class="text-white text-decoration-none small">View Details</a>
                                <i class="fas fa-angle-right text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card dashboard-card bg-danger text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Transactions</h6>
                                        <h2 class="mb-0"><?php echo $totalTransactions; ?></h2>
                                    </div>
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a href="transactions.php" class="text-white text-decoration-none small">View Details</a>
                                <i class="fas fa-angle-right text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Data -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Coupons</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Title</th>
                                                <th>Store</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentCoupons as $coupon): ?>
                                                <tr>
                                                    <td><?php echo $coupon['id']; ?></td>
                                                    <td><?php echo substr($coupon['title'], 0, 30) . (strlen($coupon['title']) > 30 ? '...' : ''); ?></td>
                                                    <td><?php echo $coupon['store_name']; ?></td>
                                                    <td>
                                                        <?php if ($coupon['status'] == 'active'): ?>
                                                            <span class="badge bg-success">Active</span>
                                                        <?php elseif ($coupon['status'] == 'expired'): ?>
                                                            <span class="badge bg-danger">Expired</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="edit-coupon.php?id=<?php echo $coupon['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($recentCoupons)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No coupons found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-end mt-3">
                                    <a href="coupons.php" class="btn btn-sm btn-outline-primary">View All Coupons</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Transactions</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>User</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentTransactions as $transaction): ?>
                                                <tr>
                                                    <td><?php echo $transaction['id']; ?></td>
                                                    <td><?php echo $transaction['username']; ?></td>
                                                    <td>₹<?php echo number_format($transaction['amount'], 2); ?></td>
                                                    <td>
                                                        <?php if ($transaction['status'] == 'pending'): ?>
                                                            <span class="badge bg-warning">Pending</span>
                                                        <?php elseif ($transaction['status'] == 'approved'): ?>
                                                            <span class="badge bg-success">Approved</span>
                                                        <?php elseif ($transaction['status'] == 'rejected'): ?>
                                                            <span class="badge bg-danger">Rejected</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-info">Paid</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="edit-transaction.php?id=<?php echo $transaction['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($recentTransactions)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No transactions found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-end mt-3">
                                    <a href="transactions.php" class="btn btn-sm btn-outline-primary">View All Transactions</a>
                                </div>
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


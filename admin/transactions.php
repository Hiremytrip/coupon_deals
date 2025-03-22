<?php
session_start();
require_once '../database/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Update transaction status
if (isset($_GET['approve']) && !empty($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $sql = "UPDATE transactions SET status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // Get transaction details
    $sql = "SELECT * FROM transactions WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transaction = $result->fetch_assoc();
    
    // Update user wallet balance
    $sql = "UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $transaction['amount'], $transaction['user_id']);
    $stmt->execute();
    
    header("Location: transactions.php?success=Transaction approved successfully");
    exit();
}

if (isset($_GET['reject']) && !empty($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    $sql = "UPDATE transactions SET status = 'rejected' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    header("Location: transactions.php?success=Transaction rejected successfully");
    exit();
}

if (isset($_GET['paid']) && !empty($_GET['paid'])) {
    $id = (int)$_GET['paid'];
    $sql = "UPDATE transactions SET status = 'paid' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    header("Location: transactions.php?success=Transaction marked as paid");
    exit();
}

// Get all transactions
$transactions = [];
$sql = "SELECT t.*, u.username, c.title as coupon_title 
        FROM transactions t 
        JOIN users u ON t.user_id = u.id 
        LEFT JOIN coupons c ON t.coupon_id = c.id 
        ORDER BY t.transaction_date DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Transactions - CouponDeals Admin</title>
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
                        <a class="nav-link active" href="transactions.php">
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
                    <h1 class="h2">Manage Transactions</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="add-transaction.php" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus"></i> Add New Transaction
                        </a>
                    </div>
                </div>
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_GET['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Coupon</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Notes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td><?php echo $transaction['id']; ?></td>
                                            <td><?php echo $transaction['username']; ?></td>
                                            <td><?php echo $transaction['coupon_title'] ? $transaction['coupon_title'] : '<span class="text-muted">N/A</span>'; ?></td>
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
                                            <td><?php echo date('d M Y H:i', strtotime($transaction['transaction_date'])); ?></td>
                                            <td><?php echo $transaction['notes'] ? $transaction['notes'] : '<span class="text-muted">No notes</span>'; ?></td>
                                            <td>
                                                <?php if ($transaction['status'] == 'pending'): ?>
                                                    <a href="transactions.php?approve=<?php echo $transaction['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to approve this transaction?')">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="transactions.php?reject=<?php echo $transaction['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to reject this transaction?')">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php elseif ($transaction['status'] == 'approved'): ?>
                                                    <a href="transactions.php?paid=<?php echo $transaction['id']; ?>" class="btn btn-sm btn-info" onclick="return confirm('Are you sure you want to mark this transaction as paid?')">
                                                        <i class="fas fa-money-bill"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="edit-transaction.php?id=<?php echo $transaction['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($transactions)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No transactions found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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


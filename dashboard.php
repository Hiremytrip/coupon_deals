<?php include 'includes/header.php'; ?>

<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$user = null;
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
}

// Get user transactions
$transactions = [];
$sql = "SELECT t.*, c.title as coupon_title, s.name as store_name 
        FROM transactions t 
        LEFT JOIN coupons c ON t.coupon_id = c.id 
        LEFT JOIN stores s ON c.store_id = s.id 
        WHERE t.user_id = ? 
        ORDER BY t.transaction_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
}

// Process withdrawal request
if (isset($_POST['withdraw']) && !empty($_POST['amount'])) {
    $amount = (float)$_POST['amount'];
    $notes = sanitize($_POST['notes']);
    
    if ($amount <= 0) {
        $error = "Please enter a valid amount.";
    } elseif ($amount > $user['wallet_balance']) {
        $error = "Insufficient balance.";
    } else {
        // Insert withdrawal transaction
        $sql = "INSERT INTO transactions (user_id, amount, status, notes) VALUES (?, ?, 'pending', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ids", $user_id, $amount, $notes);
        
        if ($stmt->execute()) {
            $success = "Withdrawal request submitted successfully!";
            
            // Refresh user data
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
            }
            
            // Refresh transactions
            $transactions = [];
            $sql = "SELECT t.*, c.title as coupon_title, s.name as store_name 
                    FROM transactions t 
                    LEFT JOIN coupons c ON t.coupon_id = c.id 
                    LEFT JOIN stores s ON c.store_id = s.id 
                    WHERE t.user_id = ? 
                    ORDER BY t.transaction_date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $transactions[] = $row;
                }
            }
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}
?>

<!-- Dashboard Header -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">My Dashboard</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-inline-block p-3 bg-white rounded shadow-sm">
                    <h5 class="mb-0">Wallet Balance</h5>
                    <h2 class="text-danger mb-0">₹<?php echo number_format($user['wallet_balance'], 2); ?></h2>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dashboard Content -->
<section class="py-5">
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Account Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
                        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                        <p><strong>Full Name:</strong> <?php echo $user['full_name'] ? $user['full_name'] : 'Not provided'; ?></p>
                        <p><strong>Phone:</strong> <?php echo $user['phone'] ? $user['phone'] : 'Not provided'; ?></p>
                        <p><strong>Member Since:</strong> <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
                        <hr>
                        <h5>Withdraw Funds</h5>
                        <form action="dashboard.php" method="POST">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount (₹)</label>
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="100" max="<?php echo $user['wallet_balance']; ?>" required>
                                <small class="text-muted">Minimum withdrawal: ₹100</small>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Payment Details</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter your payment details (UPI ID, Bank Account, etc.)" required></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="withdraw" class="btn btn-danger" <?php echo $user['wallet_balance'] < 100 ? 'disabled' : ''; ?>>Request Withdrawal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Transactions -->
            <div class="col-md-8">
                <div class="card dashboard-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Recent Transactions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($transactions)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No transactions found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($transactions as $transaction): ?>
                                            <tr>
                                                <td><?php echo date('d M Y', strtotime($transaction['transaction_date'])); ?></td>
                                                <td>
                                                    <?php if ($transaction['coupon_id']): ?>
                                                        Cashback from <?php echo $transaction['store_name']; ?> - <?php echo $transaction['coupon_title']; ?>
                                                    <?php else: ?>
                                                        <?php echo $transaction['amount'] > 0 ? 'Withdrawal Request' : 'Deposit'; ?>
                                                        <?php echo $transaction['notes'] ? ' - ' . $transaction['notes'] : ''; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="<?php echo $transaction['amount'] > 0 ? 'text-success' : 'text-danger'; ?>">
                                                    ₹<?php echo number_format(abs($transaction['amount']), 2); ?>
                                                </td>
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
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card dashboard-card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">How Cashback Works</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                <i class="fas fa-shopping-cart fa-3x text-danger mb-3"></i>
                                <h5>1. Shop Through CouponDeals</h5>
                                <p class="small">Click on our offers and shop at your favorite stores</p>
                            </div>
                            <div class="col-md-4 text-center mb-3">
                                <i class="fas fa-clock fa-3x text-danger mb-3"></i>
                                <h5>2. Wait for Confirmation</h5>
                                <p class="small">Cashback is tracked and confirmed within 30 days</p>
                            </div>
                            <div class="col-md-4 text-center mb-3">
                                <i class="fas fa-wallet fa-3x text-danger mb-3"></i>
                                <h5>3. Get Paid</h5>
                                <p class="small">Withdraw your cashback once it reaches ₹100</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>


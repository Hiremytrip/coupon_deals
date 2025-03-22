<?php include 'includes/header.php'; ?>

<?php
// Check if slug is provided
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header("Location: stores.php");
    exit();
}

$slug = sanitize($_GET['slug']);

// Get store data
$store = null;
$sql = "SELECT * FROM stores WHERE slug = ? AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $store = $result->fetch_assoc();
} else {
    header("Location: stores.php");
    exit();
}

// Get store categories
$categories = [];
$sql = "SELECT c.* FROM categories c 
        JOIN store_categories sc ON c.id = sc.category_id 
        WHERE sc.store_id = ? AND c.status = 'active' 
        ORDER BY c.name ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $store['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Get store coupons
$coupons = [];
$sql = "SELECT * FROM coupons WHERE store_id = ? AND status = 'active' ORDER BY is_featured DESC, created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $store['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $coupons[] = $row;
    }
}
?>

<!-- Store Header -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="stores.php">USA Stores</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $store['name']; ?></li>
                    </ol>
                </nav>
                <h1 class="mb-2"><?php echo $store['name']; ?> Coupons & Deals</h1>
                <p class="lead mb-3"><?php echo $store['description']; ?></p>
                
                <div class="d-flex align-items-center mb-3">
                    <?php if ($store['cashback_percent'] > 0): ?>
                        <div class="me-3">
                            <span class="badge bg-success p-2">
                                <i class="fas fa-wallet me-1"></i> 
                                Up to <?php echo $store['cashback_percent']; ?>% Cashback
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <a href="<?php echo $store['website_url']; ?>" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt me-1"></i> Visit US Store
                    </a>
                </div>

                <!-- USA Store Badge -->
                <div class="d-inline-block bg-primary text-white px-3 py-2 rounded-pill">
                    <i class="fas fa-flag-usa me-2"></i> Official US Store
                </div>
            </div>
            <div class="col-md-4 text-center">
                <img src="assets/images/stores/<?php echo $store['logo']; ?>" alt="<?php echo $store['name']; ?>" class="img-fluid store-logo-large">
            </div>
        </div>
    </div>
</section>

<!-- Cashback Info Section -->
<section class="py-4">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4>How to earn cashback at <?php echo $store['name']; ?></h4>
                        <ol>
                            <li>Click on any offer or "Visit Website" button</li>
                            <li>Shop as usual on <?php echo $store['name']; ?> website</li>
                            <li>Cashback will be tracked automatically</li>
                            <li>Earn up to <?php echo $store['cashback_percent']; ?>% cashback on your purchase</li>
                        </ol>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="display-4 text-danger fw-bold"><?php echo $store['cashback_percent']; ?>%</div>
                        <p class="lead">Maximum Cashback</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Coupons Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="mb-4"><?php echo $store['name']; ?> Coupons & Offers</h2>
        
        <?php if (empty($coupons)): ?>
            <div class="alert alert-info">No active coupons found for this store.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($coupons as $coupon): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card coupon-card h-100">
                            <div class="card-body">
                                <h6><?php echo $coupon['title']; ?></h6>
                                
                                <?php if ($coupon['discount_type'] == 'percentage'): ?>
                                    <div class="coupon-discount mb-2">Up to <?php echo $coupon['discount_value']; ?>% Off</div>
                                <?php elseif ($coupon['discount_type'] == 'fixed'): ?>
                                    <div class="coupon-discount mb-2">₹<?php echo $coupon['discount_value']; ?> Off</div>
                                <?php elseif ($coupon['discount_type'] == 'cashback'): ?>
                                    <div class="coupon-cashback mb-2">Up to <?php echo $coupon['discount_value']; ?>% Cashback</div>
                                <?php endif; ?>
                                
                                <p class="card-text small text-muted mb-3"><?php echo substr($coupon['description'], 0, 100); ?>...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    
                                    <?php if (!empty($coupon['coupon_code'])): ?>
                                        <span class="coupon-code"><?php echo $coupon['coupon_code']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">No Code Required</span>
                                    <?php endif; ?>
                                    
                                    <a href="<?php echo $store['website_url']; ?>" target="_blank" class="btn btn-sm btn-danger get-coupon-btn" data-coupon-id="<?php echo $coupon['id']; ?>" data-coupon-code="<?php echo $coupon['coupon_code']; ?>" data-store-url="<?php echo $store['website_url']; ?>">Get Deal</a>
                                </div>
                            </div>
                            <div class="card-footer bg-white text-muted small">
                                Expires: <?php echo date('d M Y', strtotime($coupon['expiry_date'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>


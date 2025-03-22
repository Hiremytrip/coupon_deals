<?php include 'includes/header.php'; ?>

<?php
// Get top cashback stores
$stores = [];
$sql = "SELECT * FROM stores WHERE status = 'active' AND name NOT IN ('Flipkart', 'Myntra', 'Ajio') AND cashback_percent > 0 ORDER BY cashback_percent DESC, is_featured DESC LIMIT 12";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
while($row = $result->fetch_assoc()) {
    $stores[] = $row;
}
}

// Get cashback coupons
$coupons = [];
$sql = "SELECT c.*, s.name as store_name, s.logo as store_logo, s.website_url 
      FROM coupons c 
      JOIN stores s ON c.store_id = s.id 
      WHERE c.status = 'active' AND s.status = 'active' AND c.discount_type = 'cashback' 
      AND s.name NOT IN ('Flipkart', 'Myntra', 'Ajio')
      ORDER BY c.discount_value DESC, c.is_featured DESC LIMIT 6";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
      $coupons[] = $row;
  }
}
?>

<!-- Cashback Header -->
<section class="py-4 bg-light">
    <div class="container">
        <h1 class="mb-0">Cashback Offers</h1>
        <p class="lead">Earn cashback on your online shopping</p>
    </div>
</section>

<!-- How Cashback Works Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">How Cashback Works</h2>
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card how-it-works-card h-100">
                    <div class="card-body text-center">
                        <div class="step-number mx-auto">1</div>
                        <h4>Click & Shop</h4>
                        <p>Click on any cashback offer and shop at your favorite store</p>
                        <i class="fas fa-mouse-pointer fa-3x text-danger mb-3"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card how-it-works-card h-100">
                    <div class="card-body text-center">
                        <div class="step-number mx-auto">2</div>
                        <h4>Cashback Tracked</h4>
                        <p>Your purchase is tracked automatically</p>
                        <i class="fas fa-chart-line fa-3x text-danger mb-3"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card how-it-works-card h-100">
                    <div class="card-body text-center">
                        <div class="step-number mx-auto">3</div>
                        <h4>Cashback Confirmed</h4>
                        <p>Cashback is confirmed after the return period (usually 30-60 days)</p>
                        <i class="fas fa-check-circle fa-3x text-danger mb-3"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card how-it-works-card h-100">
                    <div class="card-body text-center">
                        <div class="step-number mx-auto">4</div>
                        <h4>Get Paid</h4>
                        <p>Withdraw your cashback to your bank account or as vouchers</p>
                        <i class="fas fa-wallet fa-3x text-danger mb-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Top Cashback Stores Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="mb-4">Top Cashback Stores</h2>
        
        <?php if (empty($stores)): ?>
            <div class="alert alert-info">No cashback stores found.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($stores as $store): ?>
                    <div class="col-md-2 col-6 mb-4">
                        <a href="store.php?slug=<?php echo $store['slug']; ?>" class="text-decoration-none">
                            <div class="card store-card h-100">
                                <div class="card-body text-center">
                                    <img src="assets/images/stores/<?php echo $store['logo']; ?>" alt="<?php echo $store['name']; ?>" class="store-logo mb-3">
                                    <h5 class="card-title"><?php echo $store['name']; ?></h5>
                                    
                                    <?php
                                    if (strpos($store['cashback_percent'], '.') !== false && substr($store['cashback_percent'], -1) == '0') {
                                        $cashbackDisplay = floor($store['cashback_percent']) . '%';
                                    } else {
                                        $cashbackDisplay = $store['cashback_percent'] . '%';
                                    }
                                    
                                    if ($store['cashback_percent'] >= 10) {
                                        echo '<div class="coupon-discount">Flat ' . $cashbackDisplay . '</div>';
                                    } else {
                                        echo '<div class="coupon-cashback">Upto ' . $cashbackDisplay . '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-3">
                <a href="stores.php" class="btn btn-outline-danger">View All Stores</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Cashback Offers Section -->
<?php if (!empty($coupons)): ?>
<section class="py-5">
    <div class="container">
        <h2 class="mb-4">Latest Cashback Offers</h2>
        <div class="row">
            <?php foreach ($coupons as $coupon): ?>
                <div class="col-md-4 mb-4">
                    <div class="card coupon-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="assets/images/stores/<?php echo $coupon['store_logo']; ?>" alt="<?php echo $coupon['store_name']; ?>" class="coupon-logo me-3">
                                <h5 class="card-title mb-0"><?php echo $coupon['store_name']; ?></h5>
                            </div>
                            <h6><?php echo $coupon['title']; ?></h6>
                            <div class="coupon-cashback mb-2">Up to <?php echo $coupon['discount_value']; ?>% Cashback</div>
                            <p class="card-text small text-muted mb-3"><?php echo substr($coupon['description'], 0, 100); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">No Code Required</span>
                                <a href="<?php echo $coupon['website_url']; ?>" target="_blank" class="btn btn-sm btn-danger get-coupon-btn" data-coupon-id="<?php echo $coupon['id']; ?>" data-store-url="<?php echo $coupon['website_url']; ?>">Get Cashback</a>
                            </div>
                        </div>
                        <div class="card-footer bg-white text-muted small">
                            Expires: <?php echo date('d M Y', strtotime($coupon['expiry_date'])); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-3">
            <a href="offers.php?type=cashback" class="btn btn-outline-danger">View All Cashback Offers</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Frequently Asked Questions</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="accordion" id="cashbackFAQ">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                What is cashback?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#cashbackFAQ">
                            <div class="accordion-body">
                                Cashback is a reward system where you get a percentage of your purchase amount back when you shop through CouponDeals. It's like getting a discount after your purchase is completed.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                How long does it take to receive cashback?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#cashbackFAQ">
                            <div class="accordion-body">
                                Cashback is typically confirmed after the store's return period ends, which is usually 30-60 days after your purchase. Once confirmed, it will be added to your CouponDeals wallet.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                How can I withdraw my cashback?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#cashbackFAQ">
                            <div class="accordion-body">
                                You can withdraw your cashback once you have a minimum balance of ₹100 in your wallet. Go to your dashboard and request a withdrawal. You can choose to receive it in your bank account, as a gift card, or as a mobile recharge.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Why was my cashback declined?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#cashbackFAQ">
                            <div class="accordion-body">
                                Cashback may be declined for several reasons, including: using another coupon code not from our site, returning the purchased items, cancelling your order, or if your tracking was lost. If you believe your cashback was declined in error, please contact our support team.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>


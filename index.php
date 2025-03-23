<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h2 class="mb-3">Welcome to CouponDeals - Trusted Coupons, Offers & Cashback Website</h2>
                <p class="lead mb-4">Save money on your online shopping with our verified coupons and earn cashback</p>
            </div>
        </div>
    </div>
</section>

<!-- Carousel Section -->
<section class="mb-5">
    <div class="container">
        <div id="featuredCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $sql = "SELECT c.*, s.name as store_name, s.website_url 
                        FROM coupons c 
                        JOIN stores s ON c.store_id = s.id 
                        WHERE c.is_featured = 1 AND c.status = 'active' 
                        ORDER BY c.created_at DESC 
                        LIMIT 5";
                $result = $conn->query($sql);
                $active = true;
                
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $activeClass = $active ? 'active' : '';
                        $active = false;
                        
                        echo '<div class="carousel-item ' . $activeClass . '">';
                        echo '<div class="row align-items-center bg-light p-4 rounded">';
                        echo '<div class="col-md-6">';
                        echo '<h3>' . $row['title'] . '</h3>';
                        echo '<p>' . $row['description'] . '</p>';
                        
                        if ($row['discount_type'] == 'percentage') {
                            echo '<div class="coupon-discount">Up to ' . $row['discount_value'] . '% Off</div>';
                        } else if ($row['discount_type'] == 'fixed') {
                            echo '<div class="coupon-discount">₹' . $row['discount_value'] . ' Off</div>';
                        } else if ($row['discount_type'] == 'cashback') {
                            echo '<div class="coupon-cashback">Up to ' . $row['discount_value'] . '% Cashback</div>';
                        }
                        
                        echo '<div class="mt-3">';
                        if (!empty($row['coupon_code'])) {
                            echo '<span class="coupon-code me-2">' . $row['coupon_code'] . '</span>';
                        }
                        echo '<a href="' . $row['website_url'] . '" target="_blank" class="btn btn-danger get-coupon-btn" data-coupon-id="' . $row['id'] . '" data-coupon-code="' . $row['coupon_code'] . '" data-store-url="' . $row['website_url'] . '">Get Deal</a>';
                        echo '</div>';
                        echo '</div>';

                        echo '<div class="col-md-6 text-center d-none d-md-block">'; // Hide image on mobile
                        if (!empty($row['image'])) {
                            echo '<img src="assets/images/coupons/' . $row['image'] . '" alt="' . $row['title'] . '" class="img-fluid rounded">';
                        } else {
                            echo '<img src="assets/images/stores/' . $row['store_id'] . '.png" alt="' . $row['store_name'] . '" class="img-fluid rounded" style="max-height: 200px;">';
                        }
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="mb-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <h2>Three Steps To Save With CouponDeals</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card how-it-works-card h-100">
                    <div class="card-body text-center">
                        <div class="step-number mx-auto">1</div>
                        <h4>Log In & Shop</h4>
                        <p>Click your favorite coupon & shop</p>
                        <i class="fas fa-sign-in-alt fa-3x text-danger mb-3"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card how-it-works-card h-100">
                    <div class="card-body text-center">
                        <div class="step-number mx-auto">2</div>
                        <h4>Cashback Earned</h4>
                        <p>Cashback gets added to your CouponDeals wallet</p>
                        <i class="fas fa-wallet fa-3x text-danger mb-3"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card how-it-works-card h-100">
                    <div class="card-body text-center">
                        <div class="step-number mx-auto">3</div>
                        <h4>Withdraw Cashback</h4>
                        <p>To your bank account, or as a voucher, recharge</p>
                        <i class="fas fa-money-bill-wave fa-3x text-danger mb-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Top Stores Section -->
<section class="mb-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mb-4">
                <h2>Top Stores</h2>
            </div>
        </div>
        <div class="row">
            <?php
            $sql = "SELECT * FROM stores WHERE status = 'active' AND name NOT IN ('Flipkart', 'Myntra', 'Ajio') ORDER BY is_featured DESC, name ASC LIMIT 6";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-2 col-6 mb-4">';
                    echo '<a href="store.php?slug=' . $row['slug'] . '" class="text-decoration-none">';
                    echo '<div class="card store-card h-100">';
                    echo '<div class="card-body text-center">';
                    echo '<img src="assets/images/stores/' . $row['logo'] . '" alt="' . $row['name'] . '" class="store-logo mb-3">';
                    echo '<h5 class="card-title">' . $row['name'] . '</h5>';
                    
                    if ($row['cashback_percent'] > 0) {
                        if (strpos($row['cashback_percent'], '.') !== false && substr($row['cashback_percent'], -1) == '0') {
                            $cashbackDisplay = floor($row['cashback_percent']) . '%';
                        } else {
                            $cashbackDisplay = $row['cashback_percent'] . '%';
                        }
                        
                        if ($row['cashback_percent'] >= 10) {
                            echo '<div class="coupon-discount">Flat ' . $cashbackDisplay . '</div>';
                        } else {
                            echo '<div class="coupon-cashback">Upto ' . $cashbackDisplay . '</div>';
                        }
                    }
                    
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                    echo '</div>';
                }
            }
            ?>
            <div class="col-md-12 text-center mt-3">
                <a href="stores.php" class="btn btn-outline-danger">View All Stores</a>
            </div>
        </div>
    </div>
</section>

<!-- Latest Coupons Section -->
<section class="mb-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mb-4">
                <h2>Latest Coupons & Offers</h2>
            </div>
        </div>
        <div class="row">
            <?php
            $sql = "SELECT c.*, s.name as store_name, s.logo as store_logo, s.website_url 
        FROM coupons c 
        JOIN stores s ON c.store_id = s.id 
        WHERE c.status = 'active' AND s.name NOT IN ('Flipkart', 'Myntra', 'Ajio')
        AND (s.name IN ('Nike', 'Apple', 'Target', 'Best Buy', 'Amazon', 'Macy', 'Home Depot', 'GameStop', 'Nordstrom', 'Microsoft', 'Levi Strauss & Co', 'Gap', 'Walmart'))
        ORDER BY c.created_at DESC 
        LIMIT 6";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
            <!-- Update the coupon card display -->
            <div class="col-md-4 mb-4">
                <div class="card coupon-card h-100">
                    <?php if (!empty($row['image'])): ?>
                    <img src="assets/images/coupons/<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>"
                        class="coupon-image">
                    <div class="usa-badge">
                        <i class="fas fa-flag-usa"></i>
                        <span>US Deal</span>
                    </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="assets/images/stores/<?php echo $row['store_logo']; ?>"
                                alt="<?php echo $row['store_name']; ?>" class="coupon-logo">
                            <div class="ms-3">
                                <h5 class="card-title mb-0"><?php echo $row['store_name']; ?></h5>
                                <small class="text-muted">Official US Store</small>
                            </div>
                        </div>
                        <h6><?php echo $row['title']; ?></h6>

                        <?php if ($row['discount_type'] == 'percentage'): ?>
                        <div class="coupon-discount mb-2">Up to <?php echo $row['discount_value']; ?>% Off</div>
                        <?php elseif ($row['discount_type'] == 'fixed'): ?>
                        <div class="coupon-discount mb-2">$<?php echo $row['discount_value']; ?> Off</div>
                        <?php elseif ($row['discount_type'] == 'cashback'): ?>
                        <div class="coupon-cashback mb-2">Up to <?php echo $row['discount_value']; ?>% Cashback</div>
                        <?php endif; ?>

                        <p class="card-text small text-muted mb-3"><?php echo substr($row['description'], 0, 100); ?>...
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <?php if (!empty($row['coupon_code'])): ?>
                            <span class="coupon-code"><?php echo $row['coupon_code']; ?></span>
                            <?php else: ?>
                            <span class="text-muted">No Code Required</span>
                            <?php endif; ?>

                            <a href="<?php echo $row['website_url']; ?>" target="_blank"
                                class="btn btn-sm btn-danger get-coupon-btn" data-coupon-id="<?php echo $row['id']; ?>"
                                data-coupon-code="<?php echo $row['coupon_code']; ?>"
                                data-store-url="<?php echo $row['website_url']; ?>">
                                Get Deal
                            </a>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-muted small">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Expires: <?php echo date('M d, Y', strtotime($row['expiry_date'])); ?></span>
                            <span><i class="fas fa-users me-1"></i> <?php echo rand(10, 100); ?> used today</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            }
            ?>
            <div class="col-md-12 text-center mt-3">
                <a href="offers.php" class="btn btn-outline-danger">View All Offers</a>
            </div>
        </div>
    </div>
</section>

<!-- Popular Categories Section -->
<section class="mb-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12 mb-4">
                <h2>Popular Categories</h2>
            </div>
        </div>
        <div class="row">
            <?php
            $sql = "SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC LIMIT 6";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-2 col-6 mb-4">';
                    echo '<a href="category.php?slug=' . $row['slug'] . '" class="text-decoration-none">';
                    echo '<div class="card store-card h-100">';
                    echo '<div class="card-body text-center">';
                    echo '<i class="fas ' . $row['icon'] . ' fa-3x text-danger mb-3"></i>';
                    echo '<h5 class="card-title">' . $row['name'] . '</h5>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                    echo '</div>';
                }
            }
            ?>
            <div class="col-md-12 text-center mt-3">
                <a href="categories.php" class="btn btn-outline-danger">View All Categories</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
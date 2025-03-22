<?php include 'includes/header.php'; ?>

<!-- Offers Header -->
<section class="py-4 bg-light">
    <div class="container">
        <h1 class="mb-0">US Deals & Offers</h1>
        <p class="lead">Save big with exclusive deals from top US retailers</p>
    </div>
</section>

<!-- Featured Deals Section -->
<section class="py-5">
    <div class="container">
        <h2 class="mb-4">Featured Deals</h2>
        <div class="row">
            <?php
            $sql = "SELECT c.*, s.name as store_name, s.website_url, s.logo as store_logo 
                    FROM coupons c 
                    JOIN stores s ON c.store_id = s.id 
                    WHERE c.is_featured = 1 AND c.status = 'active' AND s.name NOT IN ('Flipkart', 'Myntra', 'Ajio')
                    ORDER BY c.created_at DESC 
                    LIMIT 5";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card featured-deal-card h-100">
                            <?php if (!empty($row['image'])): ?>
                                <img src="assets/images/coupons/<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>" class="card-img-top featured-deal-image">
                            <?php endif; ?>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <?php if (!empty($row['store_logo'])): ?>
                                        <img src="assets/images/stores/<?php echo $row['store_logo']; ?>" alt="<?php echo $row['store_name']; ?>" class="store-logo-small me-2">
                                    <?php else: ?>
                                        <img src="assets/images/default-logo.png" alt="Default Store Logo" class="store-logo-small me-2">
                                    <?php endif; ?>
                                    <h5 class="mb-0"><?php echo $row['store_name']; ?></h5>
                                </div>
                                <h4 class="card-title"><?php echo $row['title']; ?></h4>
                                <p class="card-text"><?php echo $row['description']; ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php if (!empty($row['coupon_code'])): ?>
                                        <span class="coupon-code"><?php echo $row['coupon_code']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">No Code Required</span>
                                    <?php endif; ?>
                                    <a href="<?php echo $row['website_url']; ?>" target="_blank" class="btn btn-primary">Get Deal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- Deal Categories -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="mb-4">Shop by Category</h2>
        <div class="row">
            <div class="col-md-3 mb-4">
                <a href="#tech-deals" class="text-decoration-none">
                    <div class="card category-card">
                        <div class="card-body text-center">
                            <i class="fas fa-laptop fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Tech Deals</h5>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="#fashion-deals" class="text-decoration-none">
                    <div class="card category-card">
                        <div class="card-body text-center">
                            <i class="fas fa-tshirt fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Fashion Deals</h5>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="#home-deals" class="text-decoration-none">
                    <div class="card category-card">
                        <div class="card-body text-center">
                            <i class="fas fa-home fa-3x text-danger mb-3"></i>
                            <h5 class="card-title">Home & Garden</h5>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="#membership-deals" class="text-decoration-none">
                    <div class="card category-card">
                        <div class="card-body text-center">
                            <i class="fas fa-crown fa-3x text-warning mb-3"></i>
                            <h5 class="card-title">Membership Deals</h5>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Tech Deals Section -->
<section id="tech-deals" class="py-5">
    <div class="container">
        <h2 class="mb-4">Tech Deals</h2>
        <div class="row">
            <?php
            $sql = "SELECT c.*, s.name as store_name, s.logo as store_logo, s.website_url 
                    FROM coupons c 
                    JOIN stores s ON c.store_id = s.id 
                    WHERE c.status = 'active' 
                    AND s.name NOT IN ('Flipkart', 'Myntra', 'Ajio')
                    AND (s.id IN (4, 6) OR c.title LIKE '%tech%' OR c.title LIKE '%electronic%') 
                    ORDER BY c.is_featured DESC, c.created_at DESC 
                    LIMIT 6";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    include 'templates/coupon-card.php';
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- Fashion Deals Section -->
<section id="fashion-deals" class="py-5 bg-light">
    <div class="container">
        <h2 class="mb-4">Fashion Deals</h2>
        <div class="row">
            <?php
            $sql = "SELECT c.*, s.name as store_name, s.logo as store_logo, s.website_url 
                    FROM coupons c 
                    JOIN stores s ON c.store_id = s.id 
                    WHERE c.status = 'active' 
                    AND s.name NOT IN ('Flipkart', 'Myntra', 'Ajio')
                    AND (s.id IN (5, 7, 8) OR c.title LIKE '%fashion%' OR c.title LIKE '%clothing%') 
                    ORDER BY c.is_featured DESC, c.created_at DESC 
                    LIMIT 6";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    include 'templates/coupon-card.php';
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- Home & Garden Deals Section -->
<section id="home-deals" class="py-5">
    <div class="container">
        <h2 class="mb-4">Home & Garden Deals</h2>
        <div class="row">
            <?php
            $sql = "SELECT c.*, s.name as store_name, s.logo as store_logo, s.website_url 
                    FROM coupons c 
                    JOIN stores s ON c.store_id = s.id 
                    WHERE c.status = 'active' 
                    AND s.name NOT IN ('Flipkart', 'Myntra', 'Ajio')
                    AND (s.id IN (9) OR c.title LIKE '%home%' OR c.title LIKE '%garden%') 
                    ORDER BY c.is_featured DESC, c.created_at DESC 
                    LIMIT 6";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    include 'templates/coupon-card.php';
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- Membership Deals Section -->
<section id="membership-deals" class="py-5 bg-light">
    <div class="container">
        <h2 class="mb-4">Exclusive Membership Deals</h2>
        <div class="row">
            <?php
            $sql = "SELECT c.*, s.name as store_name, s.logo as store_logo, s.website_url 
                    FROM coupons c 
                    JOIN stores s ON c.store_id = s.id 
                    WHERE c.status = 'active' 
                    AND s.name NOT IN ('Flipkart', 'Myntra', 'Ajio')
                    AND (c.title LIKE '%member%' OR c.title LIKE '%plus%' OR c.title LIKE '%prime%') 
                    ORDER BY c.is_featured DESC, c.created_at DESC 
                    LIMIT 3";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    include 'templates/coupon-card.php';
                }
            }
            ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

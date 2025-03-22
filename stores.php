<?php include 'includes/header.php'; ?>

<?php
// Get all active stores
$stores = [];
$sql = "SELECT * FROM stores WHERE status = 'active' AND name NOT IN ('Flipkart', 'Myntra', 'Ajio') ORDER BY is_featured DESC, name ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $stores[] = $row;
    }
}
?>

<!-- Stores Header -->
<section class="py-4 bg-light">
    <div class="container">
        <h1 class="mb-0">All Stores</h1>
        <p class="lead">Browse coupons and cashback offers from top stores</p>
    </div>
</section>

<!-- Stores Section -->
<section class="py-5">
    <div class="container">
        <?php if (empty($stores)): ?>
            <div class="alert alert-info">No stores found.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($stores as $store): ?>
                    <div class="col-md-3 col-6 mb-4">
                        <a href="store.php?slug=<?php echo $store['slug']; ?>" class="text-decoration-none">
                            <div class="card store-card h-100">
                                <div class="card-body text-center">
                                    <img src="assets/images/stores/<?php echo $store['logo']; ?>" alt="<?php echo $store['name']; ?>" class="store-logo mb-3">
                                    <h5 class="card-title"><?php echo $store['name']; ?></h5>
                                    
                                    <?php if ($store['cashback_percent'] > 0): ?>
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
                                    <?php endif; ?>
                                    
                                    <p class="card-text small text-muted mt-2"><?php echo substr($store['description'], 0, 60); ?><?php echo strlen($store['description']) > 60 ? '...' : ''; ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>


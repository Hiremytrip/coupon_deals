<?php include 'includes/header.php'; ?>

<?php
// Check if slug is provided
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
  header("Location: categories.php");
  exit();
}

$slug = sanitize($_GET['slug']);

// Get category data
$category = null;
$sql = "SELECT * FROM categories WHERE slug = ? AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
  $category = $result->fetch_assoc();
} else {
  header("Location: categories.php");
  exit();
}

// Get stores in this category
$stores = [];
$sql = "SELECT s.* FROM stores s 
      JOIN store_categories sc ON s.id = sc.store_id 
      WHERE sc.category_id = ? AND s.status = 'active' AND s.name NOT IN ('Flipkart', 'Myntra', 'Ajio')
      ORDER BY s.is_featured DESC, s.name ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
      $stores[] = $row;
  }
}

// Get coupons for stores in this category
$coupons = [];
$sql = "SELECT c.*, s.name as store_name, s.logo as store_logo, s.website_url 
      FROM coupons c 
      JOIN stores s ON c.store_id = s.id 
      JOIN store_categories sc ON s.id = sc.store_id 
      WHERE sc.category_id = ? AND c.status = 'active' AND s.status = 'active' AND s.name NOT IN ('Flipkart', 'Myntra', 'Ajio')
      ORDER BY c.is_featured DESC, c.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
      $coupons[] = $row;
  }
}
?>

<!-- Category Header -->
<section class="py-4 bg-light">
  <div class="container">
      <div class="row align-items-center">
          <div class="col-md-8">
              <nav aria-label="breadcrumb">
                  <ol class="breadcrumb mb-2">
                      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                      <li class="breadcrumb-item"><a href="categories.php">Categories</a></li>
                      <li class="breadcrumb-item active" aria-current="page"><?php echo $category['name']; ?></li>
                  </ol>
              </nav>
              <h1 class="mb-0"><?php echo $category['name']; ?> Coupons & Offers</h1>
              <p class="lead"><?php echo $category['description']; ?></p>
          </div>
          <div class="col-md-4 text-center">
              <i class="fas <?php echo $category['icon']; ?> fa-5x text-danger"></i>
          </div>
      </div>
  </div>
</section>

<!-- Stores Section -->
<?php if (!empty($stores)): ?>
<section class="py-5">
  <div class="container">
      <h2 class="mb-4">Top <?php echo $category['name']; ?> Stores</h2>
      <div class="row">
          <?php foreach ($stores as $store): ?>
              <div class="col-md-2 col-6 mb-4">
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
                          </div>
                      </div>
                  </a>
              </div>
          <?php endforeach; ?>
      </div>
  </div>
</section>
<?php endif; ?>

<!-- Coupons Section -->
<section class="py-5 bg-light">
  <div class="container">
      <h2 class="mb-4">Latest <?php echo $category['name']; ?> Coupons & Offers</h2>
      
      <?php if (empty($coupons)): ?>
          <div class="alert alert-info">No coupons found for this category.</div>
      <?php else: ?>
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
                                  
                                  <a href="<?php echo $coupon['website_url']; ?>" target="_blank" class="btn btn-sm btn-danger get-coupon-btn" data-coupon-id="<?php echo $coupon['id']; ?>" data-coupon-code="<?php echo $coupon['coupon_code']; ?>" data-store-url="<?php echo $coupon['website_url']; ?>">Get Deal</a>
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


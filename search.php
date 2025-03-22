<?php include 'includes/header.php'; ?>

<?php
// Check if search query is provided
if (!isset($_GET['q']) || empty($_GET['q'])) {
  header("Location: index.php");
  exit();
}

$query = sanitize($_GET['q']);

// Search stores
$stores = [];
$sql = "SELECT * FROM stores WHERE (name LIKE ? OR description LIKE ?) AND status = 'active' AND name NOT IN ('Flipkart', 'Myntra', 'Ajio') ORDER BY is_featured DESC, name ASC";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $query . "%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
      $stores[] = $row;
  }
}

// Search coupons
$coupons = [];
$sql = "SELECT c.*, s.name as store_name, s.logo as store_logo, s.website_url 
      FROM coupons c 
      JOIN stores s ON c.store_id = s.id 
      WHERE (c.title LIKE ? OR c.description LIKE ? OR c.coupon_code LIKE ? OR s.name LIKE ?) 
      AND c.status = 'active' AND s.status = 'active' AND s.name NOT IN ('Flipkart', 'Myntra', 'Ajio')
      ORDER BY c.is_featured DESC, c.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
      $coupons[] = $row;
  }
}

// Search categories
$categories = [];
$sql = "SELECT * FROM categories WHERE (name LIKE ? OR description LIKE ?) AND status = 'active' ORDER BY name ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
      $categories[] = $row;
  }
}
?>

<!-- Search Header -->
<section class="py-4 bg-light">
  <div class="container">
      <h1 class="mb-0">Search Results for "<?php echo $query; ?>"</h1>
      <p class="lead"><?php echo count($stores) + count($coupons) + count($categories); ?> results found</p>
      
      <form action="search.php" method="GET" class="mt-3">
          <div class="input-group">
              <input type="text" class="form-control" name="q" value="<?php echo $query; ?>" placeholder="Search for stores, coupons...">
              <button class="btn btn-danger" type="submit"><i class="fas fa-search"></i></button>
          </div>
      </form>
  </div>
</section>

<!-- Categories Section -->
<?php if (!empty($categories)): ?>
<section class="py-4">
  <div class="container">
      <h2 class="mb-3">Categories</h2>
      <div class="row">
          <?php foreach ($categories as $category): ?>
              <div class="col-md-3 col-6 mb-4">
                  <a href="category.php?slug=<?php echo $category['slug']; ?>" class="text-decoration-none">
                      <div class="card store-card h-100">
                          <div class="card-body text-center">
                              <i class="fas <?php echo $category['icon']; ?> fa-3x text-danger mb-3"></i>
                              <h5 class="card-title"><?php echo $category['name']; ?></h5>
                              <p class="card-text small text-muted"><?php echo substr($category['description'], 0, 60); ?><?php echo strlen($category['description']) > 60 ? '...' : ''; ?></p>
                          </div>
                      </div>
                  </a>
              </div>
          <?php endforeach; ?>
      </div>
  </div>
</section>
<?php endif; ?>

<!-- Stores Section -->
<?php if (!empty($stores)): ?>
<section class="py-4 <?php echo !empty($categories) ? 'bg-light' : ''; ?>">
  <div class="container">
      <h2 class="mb-3">Stores</h2>
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
<?php if (!empty($coupons)): ?>
<section class="py-4 <?php echo (!empty($categories) && empty($stores)) || (empty($categories) && !empty($stores)) ? 'bg-light' : ''; ?>">
  <div class="container">
      <h2 class="mb-3">Coupons & Offers</h2>
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
  </div>
</section>
<?php endif; ?>

<!-- No Results -->
<?php if (empty($categories) && empty($stores) && empty($coupons)): ?>
<section class="py-5">
  <div class="container">
      <div class="text-center">
          <i class="fas fa-search fa-4x text-muted mb-3"></i>
          <h3>No results found</h3>
          <p>We couldn't find any matches for "<?php echo $query; ?>". Please try another search term.</p>
      </div>
  </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>


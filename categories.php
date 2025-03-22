<?php include 'includes/header.php'; ?>

<?php
// Get all active categories
$categories = [];
$sql = "SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<!-- Categories Header -->
<section class="py-4 bg-light">
    <div class="container">
        <h1 class="mb-0">All Categories</h1>
        <p class="lead">Browse coupons and offers by category</p>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <?php if (empty($categories)): ?>
            <div class="alert alert-info">No categories found.</div>
        <?php else: ?>
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
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>


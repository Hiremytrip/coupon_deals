</main>
    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="bg-secondary bg-opacity-25 p-3 rounded h-100">
                        <h5 class="mb-3 text-white">About CouponDeals</h5>
                        <p class="text-light">CouponDeals is  trusted coupons, offers & cashback website. Save money on your online shopping with our verified coupons and earn cashback.</p>
                        <div class="social-links">
                            <a href="#" class="text-white me-3 hover-opacity"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-white me-3 hover-opacity"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-white me-3 hover-opacity"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-white hover-opacity"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="bg-secondary bg-opacity-25 p-3 rounded h-100">
                        <h5 class="mb-3 text-white">Quick Links</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="index.php" class="text-light text-decoration-none hover-opacity">Home</a></li>
                            <li class="mb-2"><a href="about.php" class="text-light text-decoration-none hover-opacity">About Us</a></li>
                            <li class="mb-2"><a href="contact.php" class="text-light text-decoration-none hover-opacity">Contact Us</a></li>
                            <li class="mb-2"><a href="faq.php" class="text-light text-decoration-none hover-opacity">FAQ</a></li>
                            <li class="mb-2"><a href="blog.php" class="text-light text-decoration-none hover-opacity">Blog</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="bg-secondary bg-opacity-25 p-3 rounded h-100">
                        <h5 class="mb-3 text-white">Popular Categories</h5>
                        <ul class="list-unstyled">
                            <?php
                            $sql = "SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC LIMIT 5";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo '<li class="mb-2"><a href="category.php?slug=' . $row['slug'] . '" class="text-light text-decoration-none hover-opacity">' . $row['name'] . '</a></li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="bg-secondary bg-opacity-25 p-3 rounded h-100">
                        <h5 class="mb-3 text-white">Popular Stores</h5>
                        <ul class="list-unstyled">
                            <?php
                            $sql = "SELECT * FROM stores WHERE status = 'active' ORDER BY name ASC LIMIT 5";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo '<li class="mb-2"><a href="store.php?slug=' . $row['slug'] . '" class="text-light text-decoration-none hover-opacity">' . $row['name'] . '</a></li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <hr class="my-4 border-secondary">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <p class="mb-0 text-light">&copy; <?php echo date('Y'); ?> CouponDeals. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end mb-3">
                    <a href="terms.php" class="text-light text-decoration-none me-3 hover-opacity">Terms of Service</a>
                    <a href="privacy.php" class="text-light text-decoration-none hover-opacity">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    
</body>
</html>


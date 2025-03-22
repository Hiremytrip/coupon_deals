<?php include 'includes/header.php'; ?>

<!-- How It Works Header -->
<section class="py-4 bg-light">
    <div class="container">
        <h1 class="mb-0">How CouponDeals Works</h1>
        <p class="lead">Learn how to save money and earn cashback with CouponDeals</p>
    </div>
</section>

<!-- Main Steps Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card how-it-works-card h-100">
                    <div class="card-body text-center">
                        <div class="step-number mx-auto">1</div>
                        <h3>Find Offers</h3>
                        <i class="fas fa-search fa-4x text-danger my-4"></i>
                        <p>Browse our website for the best coupons, deals, and cashback offers from your favorite stores.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card how-it-works-card h-100">
                    <div class="card-body text-center">
                        <div class="step-number mx-auto">2</div>
                        <h3>Shop & Save</h3>
                        <i class="fas fa-shopping-cart fa-4x text-danger my-4"></i>
                        <p>Click on any offer to visit the store's website. Shop as usual and enjoy instant savings with coupon codes.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card how-it-works-card h-100">
                    <div class="card-body text-center">
                        <div class="step-number mx-auto">3</div>
                        <h3>Earn Cashback</h3>
                        <i class="fas fa-wallet fa-4x text-danger my-4"></i>
                        <p>Cashback will be tracked automatically and added to your CouponDeals wallet once confirmed.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Coupons vs Cashback Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Coupons vs Cashback: What's the Difference?</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-danger text-white">
                        <h3 class="mb-0">Coupons & Deals</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Instant Savings:</strong> Get immediate discounts at checkout</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Coupon Codes:</strong> Enter the code during checkout to apply the discount</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Deals:</strong> Special offers that don't require a code</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Limited Time:</strong> Most coupons have an expiry date</li>
                        </ul>
                        <div class="text-center mt-4">
                            <a href="offers.php" class="btn btn-outline-danger">Browse Coupons</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Cashback</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Money Back:</strong> Get a percentage of your purchase amount back</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Delayed Reward:</strong> Cashback is confirmed after the return period (30-60 days)</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Withdrawable:</strong> Withdraw your cashback to your bank account or as vouchers</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> <strong>Stackable:</strong> Often can be combined with store coupons for extra savings</li>
                        </ul>
                        <div class="text-center mt-4">
                            <a href="cashback.php" class="btn btn-outline-primary">Browse Cashback</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cashback Process Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">The Cashback Process</h2>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="timeline">
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4><i class="fas fa-shopping-bag text-danger me-2"></i> Day 1: Make a Purchase</h4>
                                    <p>Click through CouponDeals and complete your purchase at the store. Your transaction is tracked automatically.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4><i class="fas fa-clock text-danger me-2"></i> Day 1-7: Pending Status</h4>
                                    <p>Your cashback appears in your account with a "Pending" status. This means we've tracked your purchase.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4><i class="fas fa-store text-danger me-2"></i> Day 30-60: Store Confirms</h4>
                                    <p>After the store's return period ends, they confirm the purchase is final and approve the cashback.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4><i class="fas fa-money-bill-wave text-danger me-2"></i> Anytime: Withdraw</h4>
                                    <p>Once your cashback is approved and your balance reaches ₹100, you can withdraw it to your bank account or as vouchers.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Frequently Asked Questions</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="accordion" id="howItWorksFAQ">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Is CouponDeals free to use?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#howItWorksFAQ">
                            <div class="accordion-body">
                                Yes, CouponDeals is completely free to use. We earn a commission from stores when you make a purchase through our links, which is how we're able to share cashback with you.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Can I use coupon codes from other websites?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#howItWorksFAQ">
                            <div class="accordion-body">
                                Using coupon codes from other websites may invalidate your cashback. For the best results, only use the coupon codes provided on CouponDeals.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                What is the minimum withdrawal amount?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#howItWorksFAQ">
                            <div class="accordion-body">
                                The minimum withdrawal amount is ₹100. Once your cashback balance reaches this amount, you can request a withdrawal through your dashboard.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                How do I know if my purchase was tracked?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#howItWorksFAQ">
                            <div class="accordion-body">
                                After making a purchase, you should see a pending cashback transaction in your dashboard within 7 days. If you don't see it, please contact our support team with your order details.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                What payment methods are available for withdrawals?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#howItWorksFAQ">
                            <div class="accordion-body">
                                We offer several withdrawal options including bank transfer (NEFT/IMPS), UPI, Amazon gift cards, Flipkart gift cards, and mobile recharges.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Get Started Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center">
            <h2 class="mb-4">Ready to Start Saving?</h2>
            <p class="lead mb-4">Join thousands of smart shoppers who save money every day with CouponDeals</p>
            <?php if (!$isLoggedIn): ?>
                <a href="register.php" class="btn btn-danger btn-lg me-2">Sign Up Now</a>
                <a href="stores.php" class="btn btn-outline-danger btn-lg">Browse Stores</a>
            <?php else: ?>
                <a href="stores.php" class="btn btn-danger btn-lg me-2">Browse Stores</a>
                <a href="dashboard.php" class="btn btn-outline-danger btn-lg">My Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>


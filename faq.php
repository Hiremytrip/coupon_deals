<?php include 'includes/header.php'; ?>

<!-- FAQ Header -->
<section class="py-4 bg-light">
    <div class="container">
        <h1 class="mb-0">Frequently Asked Questions</h1>
        <p class="lead">Find answers to common questions about CouponDeals</p>
    </div>
</section>

<!-- FAQ Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="list-group sticky-top" style="top: 20px;">
                    <a href="#general" class="list-group-item list-group-item-action">General Questions</a>
                    <a href="#coupons" class="list-group-item list-group-item-action">Coupons & Deals</a>
                    <a href="#cashback" class="list-group-item list-group-item-action">Cashback</a>
                    <a href="#account" class="list-group-item list-group-item-action">Account & Wallet</a>
                    <a href="#payments" class="list-group-item list-group-item-action">Payments & Withdrawals</a>
                    <a href="#technical" class="list-group-item list-group-item-action">Technical Issues</a>
                </div>
            </div>
            <div class="col-md-9">
                <!-- General Questions -->
                <div id="general" class="mb-5">
                    <h2 class="border-bottom pb-2 mb-4">General Questions</h2>
                    <div class="accordion" id="generalFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="general1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#generalCollapse1" aria-expanded="true" aria-controls="generalCollapse1">
                                    What is CouponDeals?
                                </button>
                            </h2>
                            <div id="generalCollapse1" class="accordion-collapse collapse show" aria-labelledby="general1" data-bs-parent="#generalFAQ">
                                <div class="accordion-body">
                                    CouponDeals is India's trusted coupons, offers, and cashback website. We help you save money on your online shopping by providing the latest coupons, deals, and cashback offers from your favorite stores.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="general2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#generalCollapse2" aria-expanded="false" aria-controls="generalCollapse2">
                                    Is CouponDeals free to use?
                                </button>
                            </h2>
                            <div id="generalCollapse2" class="accordion-collapse collapse" aria-labelledby="general2" data-bs-parent="#generalFAQ">
                                <div class="accordion-body">
                                    Yes, CouponDeals is completely free to use. We earn a commission from stores when you make a purchase through our links, which is how we're able to share cashback with you.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="general3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#generalCollapse3" aria-expanded="false" aria-controls="generalCollapse3">
                                    How does CouponDeals work?
                                </button>
                            </h2>
                            <div id="generalCollapse3" class="accordion-collapse collapse" aria-labelledby="general3" data-bs-parent="#generalFAQ">
                                <div class="accordion-body">
                                    CouponDeals partners with thousands of online stores to bring you exclusive coupons, deals, and cashback offers. When you shop through our platform, we earn a commission from the store, which we share with you as cashback. Simply click on any offer, shop as usual, and earn cashback on your purchase.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Coupons & Deals -->
                <div id="coupons" class="mb-5">
                    <h2 class="border-bottom pb-2 mb-4">Coupons & Deals</h2>
                    <div class="accordion" id="couponsFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="coupons1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#couponsCollapse1" aria-expanded="true" aria-controls="couponsCollapse1">
                                    How do I use a coupon code?
                                </button>
                            </h2>
                            <div id="couponsCollapse1" class="accordion-collapse collapse show" aria-labelledby="coupons1" data-bs-parent="#couponsFAQ">
                                <div class="accordion-body">
                                    To use a coupon code, click on the "Get Deal" button next to the coupon. The code will be copied to your clipboard automatically. Then, proceed to the store's website and paste the code in the promo code or coupon code field during checkout.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="coupons2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#couponsCollapse2" aria-expanded="false" aria-controls="couponsCollapse2">
                                    What if a coupon code doesn't work?
                                </button>
                            </h2>
                            <div id="couponsCollapse2" class="accordion-collapse collapse" aria-labelledby="coupons2" data-bs-parent="#couponsFAQ">
                                <div class="accordion-body">
                                    While we strive to provide only working coupon codes, sometimes stores may change or expire their promotions without notice. If a coupon code doesn't work, please try another one or contact our support team to report the issue.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="coupons3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#couponsCollapse3" aria-expanded="false" aria-controls="couponsCollapse3">
                                    What's the difference between a coupon and a deal?
                                </button>
                            </h2>
                            <div id="couponsCollapse3" class="accordion-collapse collapse" aria-labelledby="coupons3" data-bs-parent="#couponsFAQ">
                                <div class="accordion-body">
                                    A coupon requires a code that you need to enter during checkout to get the discount. A deal is a special offer that doesn't require a code - you simply click through to the store and the discount is automatically applied or the product is already discounted.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cashback -->
                <div id="cashback" class="mb-5">
                    <h2 class="border-bottom pb-2 mb-4">Cashback</h2>
                    <div class="accordion" id="cashbackFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="cashback1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#cashbackCollapse1" aria-expanded="true" aria-controls="cashbackCollapse1">
                                    How does cashback work?
                                </button>
                            </h2>
                            <div id="cashbackCollapse1" class="accordion-collapse collapse show" aria-labelledby="cashback1" data-bs-parent="#cashbackFAQ">
                                <div class="accordion-body">
                                    When you click on a cashback offer and make a purchase, we track your transaction and earn a commission from the store. We then share a portion of this commission with you as cashback. The cashback is added to your CouponDeals wallet once the store confirms your purchase (usually after the return period ends).
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="cashback2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cashbackCollapse2" aria-expanded="false" aria-controls="cashbackCollapse2">
                                    When will I receive my cashback?
                                </button>
                            </h2>
                            <div id="cashbackCollapse2" class="accordion-collapse collapse" aria-labelledby="cashback2" data-bs-parent="#cashbackFAQ">
                                <div class="accordion-body">
                                    Cashback is typically confirmed after the store's return period ends, which is usually 30-60 days after your purchase. Once confirmed, it will be added to your CouponDeals wallet and you can withdraw it once you reach the minimum withdrawal amount.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="cashback3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cashbackCollapse3" aria-expanded="false" aria-controls="cashbackCollapse3">
                                    Why was my cashback declined?
                                </button>
                            </h2>
                            <div id="cashbackCollapse3" class="accordion-collapse collapse" aria-labelledby="cashback3" data-bs-parent="#cashbackFAQ">
                                <div class="accordion-body">
                                    Cashback may be declined for several reasons, including: using another coupon code not from our site, returning the purchased items, cancelling your order, or if your tracking was lost. If you believe your cashback was declined in error, please contact our support team.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Account & Wallet -->
                <div id="account" class="mb-5">
                    <h2 class="border-bottom pb-2 mb-4">Account & Wallet</h2>
                    <div class="accordion" id="accountFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="account1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#accountCollapse1" aria-expanded="true" aria-controls="accountCollapse1">
                                    How do I create an account?
                                </button>
                            </h2>
                            <div id="accountCollapse1" class="accordion-collapse collapse show" aria-labelledby="account1" data-bs-parent="#accountFAQ">
                                <div class="accordion-body">
                                    To create an account, click on the "Register" button in the top right corner of the website. Fill in your details, including your name, email address, and password. Once registered, you can start earning cashback on your purchases.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="account2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accountCollapse2" aria-expanded="false" aria-controls="accountCollapse2">
                                    How do I check my cashback balance?
                                </button>
                            </h2>
                            <div id="accountCollapse2" class="accordion-collapse collapse" aria-labelledby="account2" data-bs-parent="#accountFAQ">
                                <div class="accordion-body">
                                    To check your cashback balance, log in to your account and go to your dashboard. Your current wallet balance will be displayed at the top of the page. You can also view your transaction history to see all your cashback earnings and withdrawals.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="account3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accountCollapse3" aria-expanded="false" aria-controls="accountCollapse3">
                                    I forgot my password. How can I reset it?
                                </button>
                            </h2>
                            <div id="accountCollapse3" class="accordion-collapse collapse" aria-labelledby="account3" data-bs-parent="#accountFAQ">
                                <div class="accordion-body">
                                    If you forgot your password, click on the "Login" button, then click on "Forgot Password" below the login form. Enter your email address, and we'll send you a link to reset your password. Follow the instructions in the email to create a new password.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payments & Withdrawals -->
                <div id="payments" class="mb-5">
                    <h2 class="border-bottom pb-2 mb-4">Payments & Withdrawals</h2>
                    <div class="accordion" id="paymentsFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="payments1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#paymentsCollapse1" aria-expanded="true" aria-controls="paymentsCollapse1">
                                    What is the minimum withdrawal amount?
                                </button>
                            </h2>
                            <div id="paymentsCollapse1" class="accordion-collapse collapse show" aria-labelledby="payments1" data-bs-parent="#paymentsFAQ">
                                <div class="accordion-body">
                                    The minimum withdrawal amount is ₹100. Once your cashback balance reaches this amount, you can request a withdrawal through your dashboard.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="payments2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#paymentsCollapse2" aria-expanded="false" aria-controls="paymentsCollapse2">
                                    What payment methods are available for withdrawals?
                                </button>
                            </h2>
                            <div id="paymentsCollapse2" class="accordion-collapse collapse" aria-labelledby="payments2" data-bs-parent="#paymentsFAQ">
                                <div class="accordion-body">
                                    We offer several withdrawal options including bank transfer (NEFT/IMPS), UPI, Amazon gift cards, Flipkart gift cards, and mobile recharges. You can choose your preferred method when requesting a withdrawal.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="payments3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#paymentsCollapse3" aria-expanded="false" aria-controls="paymentsCollapse3">
                                    How long does it take to process a withdrawal?
                                </button>
                            </h2>
                            <div id="paymentsCollapse3" class="accordion-collapse collapse" aria-labelledby="payments3" data-bs-parent="#paymentsFAQ">
                                <div class="accordion-body">
                                    Withdrawal requests are typically processed within 3-5 business days. Bank transfers and UPI payments may take 1-2 additional business days to reflect in your account, depending on your bank's processing time.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Technical Issues -->
                <div id="technical" class="mb-5">
                    <h2 class="border-bottom pb-2 mb-4">Technical Issues</h2>
                    <div class="accordion" id="technicalFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="technical1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#technicalCollapse1" aria-expanded="true" aria-controls="technicalCollapse1">
                                    My cashback wasn't tracked. What should I do?
                                </button>
                            </h2>
                            <div id="technicalCollapse1" class="accordion-collapse collapse show" aria-labelledby="technical1" data-bs-parent="#technicalFAQ">
                                <div class="accordion-body">
                                    If your cashback wasn't tracked, please wait for 7 days as it can take some time to appear in your account. If it still doesn't show up, please contact our support team with your order details, including the store name, order number, purchase date, and amount.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="technical2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#technicalCollapse2" aria-expanded="false" aria-controls="technicalCollapse2">
                                    Why am I not able to log in?
                                </button>
                            </h2>
                            <div id="technicalCollapse2" class="accordion-collapse collapse" aria-labelledby="technical2" data-bs-parent="#technicalFAQ">
                                <div class="accordion-body">
                                    If you're having trouble logging in, please check that you're using the correct email address and password. If you've forgotten your password, use the "Forgot Password" link to reset it. If you're still having issues, clear your browser cache and cookies, or try using a different browser.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="technical3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#technicalCollapse3" aria-expanded="false" aria-controls="technicalCollapse3">
                                    The website is not loading properly. What can I do?
                                </button>
                            </h2>
                            <div id="technicalCollapse3" class="accordion-collapse collapse" aria-labelledby="technical3" data-bs-parent="#technicalFAQ">
                                <div class="accordion-body">
                                    If the website is not loading properly, try refreshing the page or clearing your browser cache and cookies. You can also try using a different browser or device. If the issue persists, please contact our support team with details of the problem and screenshots if possible.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Still Have Questions -->
                <div class="text-center py-4">
                    <h3>Still Have Questions?</h3>
                    <p class="lead">Contact our support team and we'll be happy to help.</p>
                    <a href="contact.php" class="btn btn-danger">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>


<?php include 'includes/header.php'; ?>

<?php
$error = '';
$success = '';

// Process contact form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    // Validate input
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // In a real application, you would send an email here
        // For now, we'll just show a success message
        $success = "Thank you for your message! We'll get back to you soon.";
    }
}
?>

<!-- Contact Header -->
<section class="py-4 bg-light">
    <div class="container">
        <h1 class="mb-0">Contact Us</h1>
        <p class="lead">Get in touch with our team</p>
    </div>
</section>

<!-- Contact Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <h2 class="mb-4">Get In Touch</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form action="contact.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Your Name*</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                      id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Your Email*</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject*</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message*</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Send Message</button>
                </form>
            </div>
            <div class="col-md-6 mb-4">
                <h2 class="mb-4">Contact Information</h2>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5><i class="fas fa-map-marker-alt text-danger me-2"></i> Address</h5>
                        <p class="ms-4 mb-4">123 Main Street, Mumbai, Maharashtra 400001, India</p>
                        
                        <h5><i class="fas fa-envelope text-danger me-2"></i> Email</h5>
                        <p class="ms-4 mb-4"><a href="mailto:support@coupondeals.com">support@coupondeals.com</a></p>
                        
                        <h5><i class="fas fa-phone text-danger me-2"></i> Phone</h5>
                        <p class="ms-4 mb-4"><a href="tel:+919876543210">+91 9876543210</a></p>
                        
                        <h5><i class="fas fa-clock text-danger me-2"></i> Business Hours</h5>
                        <p class="ms-4">Monday - Friday: 9:00 AM - 6:00 PM<br>
                        Saturday: 10:00 AM - 4:00 PM<br>
                        Sunday: Closed</p>
                    </div>
                </div>
                
                <h2 class="mb-4">Connect With Us</h2>
                <div class="d-flex">
                    <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-info me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-outline-danger me-2"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="btn btn-outline-primary"><i class="fab fa-linkedin-in"></i></a>
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
                <div class="accordion" id="contactFAQ">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                How long does it take to get a response?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#contactFAQ">
                            <div class="accordion-body">
                                We aim to respond to all inquiries within 24-48 hours during business days. For urgent matters, please call our customer support number.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                I have an issue with my cashback. How can I report it?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#contactFAQ">
                            <div class="accordion-body">
                                For cashback issues, please use the contact form and select "Cashback Issue" as the subject. Include your order details, transaction date, and store name to help us resolve your issue faster.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                I'm a store owner. How can I partner with CouponDeals?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#contactFAQ">
                            <div class="accordion-body">
                                We're always looking for new partnerships! Please use the contact form with the subject "Partnership Inquiry" and our partnerships team will get back to you with more information.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>


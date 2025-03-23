<?php
/**
 * Coupon card template
 * 
 * This template displays a single coupon card
 */

// Make sure we have coupon data
if (!isset($coupon) || empty($coupon)) {
    return;
}

// Extract coupon data
$id = isset($coupon['id']) ? $coupon['id'] : '';
$title = isset($coupon['title']) ? $coupon['title'] : 'Coupon Title';
$description = isset($coupon['description']) ? $coupon['description'] : '';
$code = isset($coupon['code']) ? $coupon['code'] : '';
$discount = isset($coupon['discount']) ? $coupon['discount'] : '';
$expiry = isset($coupon['expiry_date']) ? $coupon['expiry_date'] : '';
$store = isset($coupon['store']) ? $coupon['store'] : '';
$store_logo = isset($coupon['store_logo']) ? $coupon['store_logo'] : 'assets/images/placeholder.jpg';
$url = isset($coupon['url']) ? $coupon['url'] : '#';
$verified = isset($coupon['verified']) && $coupon['verified'] ? true : false;
?>

<div class="coupon-card">
    <div class="coupon-store-logo">
        <img src="<?php echo $store_logo; ?>" alt="<?php echo $store; ?>" loading="lazy">
    </div>
    <div class="coupon-content">
        <h3 class="coupon-title"><?php echo $title; ?></h3>
        <?php if (!empty($description)): ?>
            <div class="coupon-description"><?php echo $description; ?></div>
        <?php endif; ?>
        <?php if (!empty($discount)): ?>
            <div class="coupon-discount"><?php echo $discount; ?></div>
        <?php endif; ?>
        <?php if (!empty($expiry)): ?>
            <div class="coupon-expiry">Valid till: <?php echo date('d M Y', strtotime($expiry)); ?></div>
        <?php endif; ?>
        <?php if ($verified): ?>
            <div class="coupon-verified"><i class="fas fa-check-circle"></i> Verified</div>
        <?php endif; ?>
    </div>
    <div class="coupon-footer">
        <?php if (!empty($code)): ?>
            <div class="coupon-code">
                <span class="code-text"><?php echo $code; ?></span>
                <button class="copy-btn" data-clipboard-text="<?php echo $code; ?>" onclick="copyCode('<?php echo $code; ?>')">
                    Copy
                </button>
            </div>
        <?php endif; ?>
        <a href="<?php echo $url; ?>" class="coupon-btn" target="_blank" rel="noopener">Get Deal</a>
    </div>
</div>
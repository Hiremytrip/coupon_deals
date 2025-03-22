<?php
session_start();
require_once 'database/db.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CouponDeals - Save Money with Coupons, Offers & Cashback</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <!-- Top Bar -->
        <div class="bg-dark text-white py-2">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small>Trusted Coupons, Offers & Cashback Website</small>
                    </div>
                    <!-- <div class="col-md-6 text-end">
                        <?php if ($isLoggedIn): ?>
                            <div class="dropdown d-inline-block">
                                <a class="text-white text-decoration-none dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['username']; ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="dashboard.php">My Dashboard</a></li>
                                    <?php if ($isAdmin): ?>
                                        <li><a class="dropdown-item" href="admin/index.php">Admin Panel</a></li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="text-white text-decoration-none me-3"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                            <a href="register.php" class="text-white text-decoration-none"><i class="fas fa-user-plus me-1"></i> Register</a>
                        <?php endif; ?>
                    </div> -->
                </div>
            </div>
        </div>
        
        <!-- Main Header -->
        <div class="bg-white py-3 border-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-3 col-6">
                        <a href="index.php" class="text-decoration-none">
                            <h1 class="m-0 text-danger fw-bold">CouponDeals</h1>
                        </a>
                    </div>
                    <div class="col-md-6 d-none d-md-block">
                        <form action="search.php" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" name="q" placeholder="Search for stores, coupons...">
                                <button class="btn btn-danger" type="submit"><i class="fas fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                    <!-- <div class="col-md-3 col-6 text-end">
                        <?php if ($isLoggedIn): ?>
                            <a href="dashboard.php" class="btn btn-outline-danger d-none d-md-inline-block">
                                <i class="fas fa-wallet me-1"></i> ₹<?php echo number_format($_SESSION['wallet_balance'], 2); ?>
                            </a>
                        <?php else: ?>
                            <a href="how-it-works.php" class="btn btn-outline-danger d-none d-md-inline-block">
                                <i class="fas fa-info-circle me-1"></i> How It Works
                            </a>
                        <?php endif; ?>
                    </div> -->
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="categories.php">Categories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="stores.php">Top Stores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="offers.php">Best Offers</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cashback.php">Cashback</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <!-- Main Content -->
    <main>


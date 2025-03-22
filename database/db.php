<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coupon_deals";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input data
function sanitize($data) {
  global $conn;
  return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Function to generate random string
function generateRandomString($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

// Add American brands if they don't exist
$americanBrands = [
  // Existing brands
  [
      'name' => 'Gap',
      'slug' => 'gap',
      'logo' => 'gap.png',
      'description' => 'Gap Inc. is an American clothing and accessories retailer offering casual wear, accessories, and personal care products.',
      'website_url' => 'https://www.gap.com/',
      'cashback_percent' => 8.00,
      'is_featured' => 1,
      'status' => 'active'
  ],
  [
      'name' => 'Walmart',
      'slug' => 'walmart',
      'logo' => 'walmart.png',
      'description' => 'Walmart Inc. is an American multinational retail corporation that operates a chain of hypermarkets, discount department stores, and grocery stores.',
      'website_url' => 'https://www.walmart.com/',
      'cashback_percent' => 5.00,
      'is_featured' => 1,
      'status' => 'active'
  ],
  // New brands
  [
      'name' => 'Nike',
      'slug' => 'nike',
      'logo' => 'nike.png',
      'description' => 'Nike, Inc. is an American multinational corporation that designs, develops, manufactures, and markets footwear, apparel, equipment, and accessories worldwide.',
      'website_url' => 'https://www.nike.com/',
      'cashback_percent' => 7.50,
      'is_featured' => 1,
      'status' => 'active'
  ],
  [
      'name' => 'Apple',
      'slug' => 'apple',
      'logo' => 'apple.png',
      'description' => 'Apple Inc. is an American multinational technology company that designs, develops, and sells consumer electronics, computer software, and online services.',
      'website_url' => 'https://www.apple.com/',
      'cashback_percent' => 3.00,
      'is_featured' => 1,
      'status' => 'active'
  ],
  [
      'name' => 'Target',
      'slug' => 'target',
      'logo' => 'target.png',
      'description' => 'Target Corporation is an American retail corporation and one of the largest discount store retailers in the United States.',
      'website_url' => 'https://www.target.com/',
      'cashback_percent' => 4.50,
      'is_featured' => 1,
      'status' => 'active'
  ],
  [
      'name' => 'Best Buy',
      'slug' => 'bestbuy',
      'logo' => 'bestbuy.png',
      'description' => 'Best Buy Co., Inc. is an American multinational consumer electronics retailer headquartered in Richfield, Minnesota.',
      'website_url' => 'https://www.bestbuy.com/',
      'cashback_percent' => 6.00,
      'is_featured' => 1,
      'status' => 'active'
  ],
  [
      'name' => 'Amazon',
      'slug' => 'amazon',
      'logo' => 'amazon.png',
      'description' => 'Amazon.com, Inc. is an American multinational technology company focusing on e-commerce, cloud computing, digital streaming, and artificial intelligence.',
      'website_url' => 'https://www.amazon.com/',
      'cashback_percent' => 6.50,
      'is_featured' => 1,
      'status' => 'active'
  ],
  [
      'name' => 'Home Depot',
      'slug' => 'homedepot',
      'logo' => 'homedepot.png',
      'description' => 'The Home Depot, Inc. is the largest home improvement retailer in the United States, supplying tools, construction products, and services.',
      'website_url' => 'https://www.homedepot.com/',
      'cashback_percent' => 4.00,
      'is_featured' => 0,
      'status' => 'active'
  ],
  [
      'name' => 'GameStop',
      'slug' => 'gamestop',
      'logo' => 'gamestop.png',
      'description' => 'GameStop Corp. is an American video game, consumer electronics, and gaming merchandise retailer.',
      'website_url' => 'https://www.gamestop.com/',
      'cashback_percent' => 5.50,
      'is_featured' => 0,
      'status' => 'active'
  ],
  [
      'name' => 'Nordstrom',
      'slug' => 'nordstrom',
      'logo' => 'nordstrom.png',
      'description' => 'Nordstrom, Inc. is an American luxury department store chain headquartered in Seattle, Washington, and founded by John W. Nordstrom and Carl F. Wallin in 1901.',
      'website_url' => 'https://www.nordstrom.com/',
      'cashback_percent' => 9.00,
      'is_featured' => 0,
      'status' => 'active'
  ],
  [
      'name' => 'Microsoft',
      'slug' => 'microsoft',
      'logo' => 'microsoft.png',
      'description' => 'Microsoft Corporation is an American multinational technology corporation that produces computer software, consumer electronics, personal computers, and related services.',
      'website_url' => 'https://www.microsoft.com/',
      'cashback_percent' => 3.50,
      'is_featured' => 0,
      'status' => 'active'
  ]
];

foreach ($americanBrands as $brand) {
  $checkSql = "SELECT * FROM stores WHERE name = ?";
  $stmt = $conn->prepare($checkSql);
  $stmt->bind_param("s", $brand['name']);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows == 0) {
      $insertSql = "INSERT INTO stores (name, slug, logo, description, website_url, cashback_percent, is_featured, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
      $insertStmt = $conn->prepare($insertSql);
      $insertStmt->bind_param("sssssdis", 
          $brand['name'], 
          $brand['slug'], 
          $brand['logo'], 
          $brand['description'], 
          $brand['website_url'], 
          $brand['cashback_percent'], 
          $brand['is_featured'], 
          $brand['status']
      );
      $insertStmt->execute();
  }
}
?>


<?php
// This script will check which brands are in the database
require_once 'database/db.php';

// Get all stores
$sql = "SELECT id, name, slug, cashback_percent, is_featured, status FROM stores ORDER BY id ASC";
$result = $conn->query($sql);

echo "<h2>Stores in Database</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Name</th><th>Slug</th><th>Cashback %</th><th>Featured</th><th>Status</th></tr>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['slug'] . "</td>";
        echo "<td>" . $row['cashback_percent'] . "</td>";
        echo "<td>" . ($row['is_featured'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No stores found</td></tr>";
}

echo "</table>";

// Get count of coupons per store
echo "<h2>Coupon Count by Store</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Store ID</th><th>Store Name</th><th>Coupon Count</th></tr>";

$sql = "SELECT s.id, s.name, COUNT(c.id) as coupon_count 
        FROM stores s 
        LEFT JOIN coupons c ON s.id = c.store_id 
        GROUP BY s.id 
        ORDER BY coupon_count DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['coupon_count'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3'>No data found</td></tr>";
}

echo "</table>";

// Close the connection
$conn->close();
?>


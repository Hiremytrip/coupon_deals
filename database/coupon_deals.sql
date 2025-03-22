-- Create database
CREATE DATABASE IF NOT EXISTS coupon_deals;
USE coupon_deals;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('admin', 'user') DEFAULT 'user',
    wallet_balance DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Stores table
CREATE TABLE IF NOT EXISTS stores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    logo VARCHAR(255),
    description TEXT,
    website_url VARCHAR(255),
    cashback_percent DECIMAL(5,2),
    is_featured BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Store categories relationship
CREATE TABLE IF NOT EXISTS store_categories (
    store_id INT,
    category_id INT,
    PRIMARY KEY (store_id, category_id),
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Coupons table
CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    coupon_code VARCHAR(50),
    discount_type ENUM('percentage', 'fixed', 'cashback') DEFAULT 'percentage',
    discount_value DECIMAL(10,2),
    expiry_date DATE,
    image VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'expired', 'inactive') DEFAULT 'active',
    clicks INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
);

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    coupon_id INT,
    amount DECIMAL(10,2),
    status ENUM('pending', 'approved', 'rejected', 'paid') DEFAULT 'pending',
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('admin', 'admin@example.com', '$2y$10$YPFj8apjGcfiCL6b4qq6s.66QjulAS4JlA.RdTwoA.GzgAnFfPpvO', 'Admin User', 'admin');

-- Insert sample categories
INSERT INTO categories (name, slug, description, icon) VALUES
('Electronics', 'electronics', 'Electronic gadgets and devices', 'fa-laptop'),
('Fashion', 'fashion', 'Clothing, shoes, and accessories', 'fa-tshirt'),
('Food & Dining', 'food-dining', 'Restaurants and food delivery', 'fa-utensils'),
('Travel', 'travel', 'Hotels, flights, and vacation packages', 'fa-plane'),
('Health & Beauty', 'health-beauty', 'Health, wellness, and beauty products', 'fa-spa');

-- Insert updated stores
INSERT INTO stores (name, slug, logo, description, website_url, cashback_percent, is_featured) VALUES
('Amazon', 'amazon', 'amazon.png', 'Online shopping for electronics, apparel, computers, books, and more', 'https://www.amazon.com', 6.5, TRUE),
('Apple', 'apple', 'apple.png', 'Electronics, software, and digital services from Apple', 'https://www.apple.com', 5.0, TRUE),
('Best Buy', 'best-buy', 'bestbuy.png', 'Electronics, appliances, and tech gadgets', 'https://www.bestbuy.com', 4.5, TRUE),
('Gap', 'gap', 'gap.png', 'Clothing and accessories', 'https://www.gap.com', 10.0, TRUE),
('GameStop', 'gamestop', 'gamestop.png', 'Video games, consoles, and gaming accessories', 'https://www.gamestop.com', 7.5, TRUE),
('Home Depot', 'home-depot', 'homedepot.png', 'Home improvement, tools, and appliances', 'https://www.homedepot.com', 3.0, TRUE),
('Hostinger', 'hostinger', 'hostinger.png', 'Web hosting provider with free domain', 'https://www.hostinger.com', 41.3, TRUE),
('Levi Strauss & Co', 'levi-strauss', 'levi.png', 'Global clothing retailer and brand', 'https://www.levi.com', 6.0, TRUE),
('Macy', 'macys', 'macys.png', 'Department store with a variety of clothing, home goods, and more', 'https://www.macys.com', 5.0, TRUE),
('Microsoft', 'microsoft', 'microsoft.png', 'Technology company selling software, hardware, and cloud services', 'https://www.microsoft.com', 3.5, TRUE),
('Nike', 'nike', 'nike.png', 'Sportswear and athletic footwear', 'https://www.nike.com', 8.0, TRUE),
('Nordstrom', 'nordstrom', 'nordstrom.png', 'Clothing, shoes, accessories, and beauty products', 'https://www.nordstrom.com', 6.5, TRUE),
('Target', 'target', 'target.png', 'General merchandise retailer with a variety of products', 'https://www.target.com', 4.0, TRUE),
('Walmart', 'walmart', 'walmart.png', 'Superstore selling everything from groceries to electronics', 'https://www.walmart.com', 3.0, TRUE);

-- Connect stores with categories
INSERT INTO store_categories (store_id, category_id) VALUES
(1, 1), -- Amazon - Electronics
(1, 2), -- Amazon - Fashion
(2, 1), -- Apple - Electronics
(3, 1), -- Best Buy - Electronics
(4, 2), -- Gap - Fashion
(5, 1), -- GameStop - Electronics
(6, 1), -- Home Depot - Electronics
(7, 1), -- Hostinger - Electronics
(8, 2), -- Levi Strauss & Co - Fashion
(9, 2), -- Macy's - Fashion
(10, 1), -- Microsoft - Electronics
(11, 2), -- Nike - Fashion
(12, 2), -- Nordstrom - Fashion
(13, 2), -- Target - Fashion
(14, 1); -- Walmart - Electronics

-- Insert sample coupons (using updated store IDs based on new stores)
INSERT INTO coupons (store_id, title, description, coupon_code, discount_type, discount_value, expiry_date, is_featured, status) VALUES
(1, 'Get 10% Off All Electronics', 'Enjoy a 10% discount on all electronics at Amazon.', 'AMZ10ELECTRO', 'percentage', 10.00, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), FALSE, 'active'),
(1, 'Save $15 on Orders Over $100', 'Save $15 on your next order of $100 or more at Amazon.', 'AMZ15SAVE', 'fixed amount', 15.00, DATE_ADD(CURRENT_DATE, INTERVAL 45 DAY), FALSE, 'active'),
(2, '50 Off Apple Products', 'Get $50 off any Apple product purchase.', 'APPLE50OFF', 'fixed amount', 50.00, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), TRUE, 'active'),
(2, '10% Off Apple Accessories', 'Save 10% on all Apple accessories with this special coupon.', 'APPLE10ACC', 'percentage', 10.00, DATE_ADD(CURRENT_DATE, INTERVAL 40 DAY), TRUE, 'active'),
(3, 'Save 20% on Home Appliances', 'Get 20% off on home appliances at Best Buy.', 'BB20HOME', 'percentage', 20.00, DATE_ADD(CURRENT_DATE, INTERVAL 35 DAY), TRUE, 'active'),
(3, 'Save $30 on Orders Over $200', 'Save $30 on any Best Buy purchase over $200.', 'BB30OFF', 'fixed amount', 30.00, DATE_ADD(CURRENT_DATE, INTERVAL 50 DAY), TRUE, 'active'),
(4, '25% Off Your First Order', 'Enjoy 25% off your first order at Gap.', 'GAP25FIRST', 'percentage', 25.00, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), TRUE, 'active'),
(4, 'Save $10 on Orders Over $50', 'Save $10 on your Gap order when you spend $50 or more.', 'GAP10SAVE', 'fixed amount', 10.00, DATE_ADD(CURRENT_DATE, INTERVAL 50 DAY), TRUE, 'active'),
(5, '15% Off All Video Games', 'Save 15% on all video games at GameStop.', 'GS15VGAMES', 'percentage', 15.00, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), TRUE, 'active'),
(5, 'Buy 1 Get 1 50% Off on Accessories', 'Buy one accessory, get another 50% off at GameStop.', 'GSBOGO50', 'percentage', 50.00, DATE_ADD(CURRENT_DATE, INTERVAL 45 DAY), TRUE, 'active'),
(6, '10% Off All Home Improvement Products', 'Save 10% on all home improvement products at Home Depot.', 'HD10HOME', 'percentage', 10.00, DATE_ADD(CURRENT_DATE, INTERVAL 35 DAY), TRUE, 'active'),
(6, 'Save $25 on Orders Over $150', 'Get $25 off orders of $150 or more at Home Depot.', 'HD25SAVE', 'fixed amount', 25.00, DATE_ADD(CURRENT_DATE, INTERVAL 50 DAY), TRUE, 'active'),
(7, '30% Off Hosting Plans', 'Get 30% off any hosting plan at Hostinger.', 'HOST30OFF', 'percentage', 30.00, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), TRUE, 'active'),
(7, 'Save $10 on VPS Hosting', 'Save $10 on any VPS hosting plan at Hostinger.', 'HOST10VPS', 'fixed amount', 10.00, DATE_ADD(CURRENT_DATE, INTERVAL 45 DAY), TRUE, 'active'),
(8, '20% Off All Jeans', 'Get 20% off all jeans at Levi Strauss & Co.', 'LEVI20JEANS', 'percentage', 20.00, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), TRUE, 'active'),
(8, 'Buy 2 Get 1 Free on Shirts', 'Buy 2 shirts, get 1 free at Levi Strauss & Co.', 'LEVI2FOR1', 'fixed amount', 0.00, DATE_ADD(CURRENT_DATE, INTERVAL 40 DAY), TRUE, 'active'),
(9, '15% Off Sitewide', 'Get 15% off everything at Macy with this coupon.', 'MACYS15OFF', 'percentage', 15.00, DATE_ADD(CURRENT_DATE, INTERVAL 35 DAY), TRUE, 'active'),
(9, 'Save $20 on Orders Over $100', 'Save $20 on orders over $100 at Macy', 'MACYS20OFF', 'fixed amount', 20.00, DATE_ADD(CURRENT_DATE, INTERVAL 50 DAY), TRUE, 'active'),
(10, '10% Off All Software', 'Save 10% on all software purchases at Microsoft.', 'MS10SOFT', 'percentage', 10.00, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), TRUE, 'active'),
(10, 'Save $25 on Microsoft Surface', 'Save $25 on any Microsoft Surface device.', 'MS25SURFACE', 'fixed amount', 25.00, DATE_ADD(CURRENT_DATE, INTERVAL 40 DAY), TRUE, 'active'),
(11, '20% Off All Shoes', 'Get 20% off all shoes at Nike.', 'NIKE20SHOES', 'percentage', 20.00, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), TRUE, 'active'),
(11, 'Save $30 on Orders Over $150', 'Save $30 on orders over $150 at Nike.', 'NIKE30OFF', 'fixed amount', 30.00, DATE_ADD(CURRENT_DATE, INTERVAL 50 DAY), TRUE, 'active'),
(12, '15% Off First Purchase', 'Get 15% off your first purchase at Nordstrom.', 'NORD15FIRST', 'percentage', 15.00, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), TRUE, 'active'),
(12, 'Save $20 on Orders Over $100', 'Save $20 on orders over $100 at Nordstrom.', 'NORD20OFF', 'fixed amount', 20.00, DATE_ADD(CURRENT_DATE, INTERVAL 50 DAY), TRUE, 'active'),
(13, '10% Off Grocery Orders', 'Save 10% on all grocery orders at Target.', 'TARGET10GRO', 'percentage', 10.00, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), TRUE, 'active'),
(13, 'Save $15 on Orders Over $75', 'Get $15 off orders over $75 at Target.', 'TARGET15OFF', 'fixed amount', 15.00, DATE_ADD(CURRENT_DATE, INTERVAL 45 DAY), TRUE, 'active'),
(14, '25% Off All Electronics', 'Save 25% on all electronics at Walmart.', 'WAL25ELECTRO', 'percentage', 25.00, DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY), TRUE, 'active'),
(14, 'Save $50 on Orders Over $200', 'Get $50 off orders over $200 at Walmart.', 'WAL50OFF', 'fixed amount', 50.00, DATE_ADD(CURRENT_DATE, INTERVAL 45 DAY), TRUE, 'active');

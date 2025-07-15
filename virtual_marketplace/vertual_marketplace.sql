-- Create Database
CREATE DATABASE IF NOT EXISTS virtual_marketplace;
USE virtual_marketplace;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

-- Create the users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  description TEXT,
  image VARCHAR(255)
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  product_name VARCHAR(100) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  quantity INT DEFAULT 1,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert 10 Products
INSERT INTO products (name, price, description, image) VALUES
('Red T-Shirt', 499.00, 'Comfortable cotton red t-shirt.', 'images/product1.jpg'),
('Blue Jeans', 999.00, 'Stylish blue jeans with stretch.', 'images/product2.jpg'),
('White Sneakers', 1499.00, 'Trendy white sneakers for all occasions.', 'images/product3.jpg'),
('Black Hoodie', 799.00, 'Warm and soft black hoodie.', 'images/product4.jpg'),
('Leather Wallet', 299.00, 'Premium quality brown leather wallet.', 'images/product5.jpg'),
('Sports Watch', 1299.00, 'Water-resistant sports watch with stopwatch.', 'images/product6.jpg'),
('Backpack', 699.00, 'Durable and spacious travel backpack.', 'images/product7.jpg'),
('Wireless Earbuds', 1099.00, 'Bluetooth wireless earbuds with charging case.', 'images/product8.jpg'),
('Sunglasses', 399.00, 'UV-protected stylish sunglasses.', 'images/product9.jpg'),
('Cap', 199.00, 'Casual baseball cap, adjustable.', 'images/product10.jpg');

-- Insert Test User (password = 123456)
INSERT INTO users (name, email, password) VALUES
('Test User', 'test@example.com', '$2y$10$P/qYl0vJTTyc2.H/f/3ZyOTYFdxGLt9QeCRc3cI6DTnLhaRWQ4r12');

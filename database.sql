CREATE DATABASE IF NOT EXISTS product_catalog CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE product_catalog;

CREATE TABLE IF NOT EXISTS categories (
 id INT AUTO_INCREMENT PRIMARY KEY,
 category_name VARCHAR(100) NOT NULL UNIQUE,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
 id INT AUTO_INCREMENT PRIMARY KEY,
 product_name VARCHAR(150) NOT NULL,
 category_id INT NOT NULL,
 price DECIMAL(10,2) NOT NULL DEFAULT 0,
 stock INT NOT NULL DEFAULT 0,
 description TEXT NOT NULL,
 image VARCHAR(500) DEFAULT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX(category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(100) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO users (username,password) VALUES ('admin','$2y$12$vStu63N3te6De8s0e9nseOX.RrDWMhsqCUb6ZSU.UpFikhcLXhamK');
INSERT IGNORE INTO categories (category_name) VALUES ('Smartphone'),('Laptop'),('Electronics'),('Accessories'),('Monitor'),('Printer');

INSERT INTO products (product_name,category_id,price,stock,description,image)
SELECT 'iPhone 15 Pro Max',id,69999.00,10,'Premium smartphone with a powerful processor, advanced camera system, and high-resolution display.','https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?auto=format&fit=crop&w=900&q=85' FROM categories WHERE category_name='Smartphone' AND NOT EXISTS (SELECT 1 FROM products WHERE product_name='iPhone 15 Pro Max');
INSERT INTO products (product_name,category_id,price,stock,description,image)
SELECT 'Acer Aspire 5',id,25999.00,10,'Reliable everyday laptop for students, office work, browsing, and productivity.','https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=85' FROM categories WHERE category_name='Laptop' AND NOT EXISTS (SELECT 1 FROM products WHERE product_name='Acer Aspire 5');
INSERT INTO products (product_name,category_id,price,stock,description,image)
SELECT 'Wireless Headphones',id,650.00,15,'Comfortable wireless headphones with clear sound for music, study, and entertainment.','https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=85' FROM categories WHERE category_name='Electronics' AND NOT EXISTS (SELECT 1 FROM products WHERE product_name='Wireless Headphones');
INSERT INTO products (product_name,category_id,price,stock,description,image)
SELECT 'Mechanical Keyboard',id,1899.00,12,'Responsive mechanical keyboard designed for comfortable typing and gaming.','https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=900&q=85' FROM categories WHERE category_name='Accessories' AND NOT EXISTS (SELECT 1 FROM products WHERE product_name='Mechanical Keyboard');
INSERT INTO products (product_name,category_id,price,stock,description,image)
SELECT 'Samsung 24-inch Monitor',id,5499.00,7,'Slim Full HD monitor with vivid colors and a clean workspace-friendly design.','https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=900&q=85' FROM categories WHERE category_name='Monitor' AND NOT EXISTS (SELECT 1 FROM products WHERE product_name='Samsung 24-inch Monitor');
INSERT INTO products (product_name,category_id,price,stock,description,image)
SELECT 'Epson Ink Tank Printer',id,6299.00,5,'Practical ink tank printer for school documents, home use, and office printing.','https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=900&q=85' FROM categories WHERE category_name='Printer' AND NOT EXISTS (SELECT 1 FROM products WHERE product_name='Epson Ink Tank Printer');

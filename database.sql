-- Tạo database
CREATE DATABASE IF NOT EXISTS mobile_store
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mobile_store;

-- USERS
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  fullname VARCHAR(150) NOT NULL,
  role TINYINT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- CATEGORIES
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- PRODUCTS
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  description TEXT,
  thumbnail VARCHAR(255),
  base_price DECIMAL(15,2) NOT NULL DEFAULT 0,
  discount_percent INT NOT NULL DEFAULT 0,
  category_id INT NOT NULL,
  CONSTRAINT fk_products_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- PRODUCT VARIANTS
CREATE TABLE product_variants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  color VARCHAR(100) NOT NULL,
  storage VARCHAR(50) NOT NULL,
  price DECIMAL(15,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  UNIQUE KEY uq_variant (product_id, color, storage),
  CONSTRAINT fk_variants_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ORDERS
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total_money DECIMAL(15,2) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ORDER DETAILS
CREATE TABLE order_details (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_variant_id INT NOT NULL,
  quantity INT NOT NULL,
  price_at_purchase DECIMAL(15,2) NOT NULL,
  CONSTRAINT fk_order_details_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_order_details_variant
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- DỮ LIỆU DEMO
INSERT INTO users (username, password, fullname, role) VALUES
('admin',  '$2y$10$QpWlV8u0cGAbKQz6vB5O2e7PjY2m6w9W6v3znEqeGMWbTOWkgN59C', 'Quản trị viên', 1),
('staff',  '$2y$10$QpWlV8u0cGAbKQz6vB5O2e7PjY2m6w9W6v3znEqeGMWbTOWkgN59C', 'Nhân viên kho', 2),
('khach',  '$2y$10$QpWlV8u0cGAbKQz6vB5O2e7PjY2m6w9W6v3znEqeGMWbTOWkgN59C', 'Khách hàng demo', 0);
-- hash tương ứng password "123456"

INSERT INTO categories (name, slug) VALUES
('iPhone', 'iphone'),
('Samsung', 'samsung'),
('Xiaomi', 'xiaomi');

INSERT INTO products (name, slug, description, thumbnail, base_price, discount_percent, category_id) VALUES
('iPhone 15 Pro', 'iphone-15-pro',
 'iPhone 15 Pro với thiết kế viền titan, chip A17 Pro, camera nâng cấp.',
 'assets/img/iphone15pro.jpg', 32000000, 5, 1),
('Galaxy S24 Ultra', 'galaxy-s24-ultra',
 'Samsung Galaxy S24 Ultra với màn hình AMOLED 120Hz, camera 200MP.',
 'assets/img/galaxys24ultra.jpg', 28000000, 10, 2),
('Xiaomi 14', 'xiaomi-14',
 'Xiaomi 14 hiệu năng mạnh mẽ, sạc siêu nhanh, camera Leica.',
 'assets/img/xiaomi14.jpg', 19000000, 8, 3);

INSERT INTO product_variants (product_id, color, storage, price, stock) VALUES
-- iPhone 15 Pro
(1, 'Đen',  '128GB', 28990000, 10),
(1, 'Đen',  '256GB', 30990000, 8),
(1, 'Xanh', '256GB', 31490000, 5),
(1, 'Trắng','512GB', 35990000, 3),

-- Galaxy S24 Ultra
(2, 'Đen', '256GB', 24990000, 12),
(2, 'Xanh','256GB', 25290000, 7),
(2, 'Đen', '512GB', 27990000, 4),

-- Xiaomi 14
(3, 'Đen', '256GB', 16990000, 15),
(3, 'Xanh','512GB', 19990000, 6);

-- DEMO ĐƠN HÀNG
INSERT INTO orders (user_id, total_money, status) VALUES
(3, 45980000, 'completed');

INSERT INTO order_details (order_id, product_variant_id, quantity, price_at_purchase) VALUES
(1, 1, 1, 28990000),
(1, 8, 1, 16990000);

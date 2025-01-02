-- Create database
CREATE DATABASE IF NOT EXISTS inventory_system;
USE inventory_system;

-- Suppliers table
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Parties (Customers) table
CREATE TABLE parties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    sku VARCHAR(50) UNIQUE,
    category VARCHAR(50),
    unit_id INT,
    purchase_price DECIMAL(10,2),
    selling_price DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES units(id)
);

-- Units table
CREATE TABLE units (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    short_name VARCHAR(10) NOT NULL,
    base_unit_id INT,
    conversion_factor DECIMAL(10,4),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (base_unit_id) REFERENCES units(id)
);

-- Insert some basic units
INSERT INTO units (name, short_name) VALUES 
('Piece', 'Pc'),
('Box', 'Box'),
('Kilogram', 'Kg'),
('Gram', 'g'),
('Liter', 'L'),
('Milliliter', 'ml'),
('Meter', 'm'),
('Centimeter', 'cm'),
('Dozen', 'Dz');

-- Purchase Orders table
CREATE TABLE purchase_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_id INT,
    order_date DATE,
    total_amount DECIMAL(10,2),
    status ENUM('pending', 'completed', 'cancelled'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

-- Purchase Order Items table
CREATE TABLE purchase_order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    purchase_order_id INT,
    product_id INT,
    quantity DECIMAL(10,2),
    unit_id INT,
    unit_price DECIMAL(10,2),
    total_price DECIMAL(10,2),
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (unit_id) REFERENCES units(id)
);

-- Sales Orders table
CREATE TABLE sales_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    party_id INT,
    order_date DATE,
    total_amount DECIMAL(10,2),
    status ENUM('pending', 'completed', 'cancelled'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (party_id) REFERENCES parties(id)
);

-- Sales Order Items table
CREATE TABLE sales_order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sales_order_id INT,
    product_id INT,
    quantity DECIMAL(10,2),
    unit_id INT,
    unit_price DECIMAL(10,2),
    total_price DECIMAL(10,2),
    FOREIGN KEY (sales_order_id) REFERENCES sales_orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (unit_id) REFERENCES units(id)
);

-- Expenses table
CREATE TABLE expenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category ENUM('salaries', 'rent', 'utilities', 'other'),
    amount DECIMAL(10,2),
    description TEXT,
    expense_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Purchase Receipts table
CREATE TABLE purchase_receipts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_id INT,
    purchase_order_id INT,
    amount DECIMAL(10,2),
    payment_date DATE,
    payment_mode ENUM('cash', 'cheque', 'online', 'upi'),
    reference_no VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id)
);

-- Sales Transactions table
CREATE TABLE sales_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    party_id INT,
    sales_order_id INT,
    amount DECIMAL(10,2),
    payment_date DATE,
    payment_mode ENUM('cash', 'cheque', 'online', 'upi'),
    reference_no VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (party_id) REFERENCES parties(id),
    FOREIGN KEY (sales_order_id) REFERENCES sales_orders(id)
);

-- Add payment_status column to purchase_orders and sales_orders
ALTER TABLE purchase_orders ADD COLUMN payment_status ENUM('pending', 'partial', 'completed') DEFAULT 'pending';
ALTER TABLE sales_orders ADD COLUMN payment_status ENUM('pending', 'partial', 'completed') DEFAULT 'pending';

-- Add paid_amount column to purchase_orders and sales_orders
ALTER TABLE purchase_orders ADD COLUMN paid_amount DECIMAL(10,2) DEFAULT 0;
ALTER TABLE sales_orders ADD COLUMN paid_amount DECIMAL(10,2) DEFAULT 0; 


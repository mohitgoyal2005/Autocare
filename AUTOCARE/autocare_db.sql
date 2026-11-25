CREATE DATABASE IF NOT EXISTS autocare_db;
USE autocare_db;

-- 1. Customers & Vehicles Table
CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    vehicle_type ENUM('Car', 'Bike') NOT NULL,
    vehicle_number VARCHAR(20) NOT NULL UNIQUE,
    vehicle_model VARCHAR(50) NOT NULL
);

-- 2. Mechanics Table
CREATE TABLE mechanics (
    mechanic_id INT AUTO_INCREMENT PRIMARY KEY,
    mechanic_name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    status ENUM('Available', 'Busy') DEFAULT 'Available'
);

-- 3. Service Packages Table
CREATE TABLE service_packages (
    package_id INT AUTO_INCREMENT PRIMARY KEY,
    package_name VARCHAR(100) NOT NULL,
    vehicle_category ENUM('Car', 'Bike', 'Both') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT
);

-- 4. Service Bookings & Payments Table (Main CRUD Module with Foreign Keys)
CREATE TABLE service_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    package_id INT NOT NULL,
    mechanic_id INT NOT NULL,
    booking_date DATE NOT NULL,
    service_status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending',
    payment_status ENUM('Unpaid', 'Paid') DEFAULT 'Unpaid',
    total_amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES service_packages(package_id) ON DELETE CASCADE,
    FOREIGN KEY (mechanic_id) REFERENCES mechanics(mechanic_id) ON DELETE CASCADE
);

-- Sample Data for Testing & Demonstration
INSERT INTO customers (customer_name, phone, email, vehicle_type, vehicle_number, vehicle_model) VALUES
('Rahul Sharma', '9876543210', 'rahul@example.com', 'Car', 'RJ19CA4521', 'Hyundai Creta'),
('Aman Verma', '9123456780', 'aman@example.com', 'Bike', 'RJ19BS8890', 'Royal Enfield Classic 350'),
('Priya Singh', '9988776655', 'priya@example.com', 'Car', 'RJ19CB1122', 'Maruti Swift');

INSERT INTO mechanics (mechanic_name, specialization, phone, status) VALUES
('Ramesh Mistri', 'Engine & Transmission', '9829011122', 'Available'),
('Suresh Kumar', 'Bike Servicing & Electrical', '9829033344', 'Available'),
('Vikram Rathore', 'AC, Brakes & General Service', '9829055566', 'Busy');

INSERT INTO service_packages (package_name, vehicle_category, price, description) VALUES
('Basic Bike Service', 'Bike', 799.00, 'Engine oil change, chain lube, brake check, and wash'),
('Full Car General Service', 'Car', 3499.00, 'Synthetic oil, oil & air filter, 40-point checkup, interior vacuum'),
('Car AC & Spa Package', 'Car', 1999.00, 'AC gas top-up, cooling coil clean, and foam wash'),
('Bike Complete Overhaul', 'Bike', 1899.00, 'Full engine tuning, clutch plate check, oil & filter replacement');

INSERT INTO service_bookings (customer_id, package_id, mechanic_id, booking_date, service_status, payment_status, total_amount) VALUES
(1, 2, 1, '2026-11-18', 'Completed', 'Paid', 3499.00),
(2, 1, 2, '2026-11-19', 'In Progress', 'Unpaid', 799.00),
(3, 3, 3, '2026-11-19', 'Pending', 'Unpaid', 1999.00);
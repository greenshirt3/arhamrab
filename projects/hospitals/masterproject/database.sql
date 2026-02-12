-- City International Smart Hospital Database Schema

CREATE DATABASE IF NOT EXISTS city_hospital_db;
USE city_hospital_db;

-- Users (Staff, Doctors, Admin)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'doctor', 'reception', 'lab', 'pharmacy') NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    department VARCHAR(50) DEFAULT NULL,
    qr_code_string VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Patients
CREATE TABLE patients (
    id VARCHAR(20) PRIMARY KEY, -- E.g., PAT-2025-0001
    full_name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    contact_number VARCHAR(20),
    address TEXT,
    qr_code_string VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medicines / Inventory
CREATE TABLE inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    batch_no VARCHAR(50),
    stock_qty INT DEFAULT 0,
    unit_price DECIMAL(10, 2) NOT NULL,
    expiry_date DATE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Appointments & Visits
CREATE TABLE visits (
    id VARCHAR(20) PRIMARY KEY, -- E.g., OPD-2025-999
    patient_id VARCHAR(20) NOT NULL,
    doctor_id INT NOT NULL,
    department VARCHAR(50),
    status ENUM('waiting', 'completed', 'cancelled') DEFAULT 'waiting',
    visit_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id)
);

-- Medical Records / Prescriptions
CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_id VARCHAR(20) NOT NULL,
    diagnosis TEXT,
    medicines JSON, -- Stores array of meds {name, dosage, freq}
    notes TEXT,
    FOREIGN KEY (visit_id) REFERENCES visits(id)
);

-- Lab Reports
CREATE TABLE lab_reports (
    id VARCHAR(20) PRIMARY KEY, -- E.g., LAB-2025-555
    visit_id VARCHAR(20),
    patient_id VARCHAR(20),
    test_type VARCHAR(100),
    result_data JSON, -- Dynamic results
    status ENUM('pending', 'completed') DEFAULT 'pending',
    technician_id INT,
    completed_at DATETIME
);
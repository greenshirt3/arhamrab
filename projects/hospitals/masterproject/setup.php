<?php
require_once 'config.php';

echo "<h1>Initializing City Smart Hospital Database...</h1>";

// 1. ENSURE DATA DIRECTORY EXISTS
if (!file_exists(DIR_DATA)) {
    mkdir(DIR_DATA, 0777, true);
    echo "<p>✅ Created 'data' directory.</p>";
}

// 2. CREATE USERS (Admin, Doctors, Staff)
$users = [
    ['id' => 1, 'username' => 'admin', 'password' => password_hash('admin123', PASSWORD_DEFAULT), 'role' => 'admin', 'name' => 'System Administrator', 'dept' => 'Management'],
    ['id' => 2, 'username' => 'doc1', 'password' => password_hash('doc123', PASSWORD_DEFAULT), 'role' => 'doctor', 'name' => 'Ali Raza', 'dept' => 'Cardiology'],
    ['id' => 3, 'username' => 'doc2', 'password' => password_hash('doc123', PASSWORD_DEFAULT), 'role' => 'doctor', 'name' => 'Sara Khan', 'dept' => 'Pediatrics'],
    ['id' => 4, 'username' => 'recep', 'password' => password_hash('recep123', PASSWORD_DEFAULT), 'role' => 'reception', 'name' => 'Front Desk', 'dept' => 'OPD'],
    ['id' => 5, 'username' => 'pharm', 'password' => password_hash('pharm123', PASSWORD_DEFAULT), 'role' => 'pharmacy', 'name' => 'Chief Pharmacist', 'dept' => 'Pharmacy'],
    ['id' => 6, 'username' => 'lab', 'password' => password_hash('lab123', PASSWORD_DEFAULT), 'role' => 'lab', 'name' => 'Lab Technician', 'dept' => 'Pathology'],
];
file_put_contents(FILE_USERS, json_encode($users, JSON_PRETTY_PRINT));
echo "<p>✅ Created Users (admin, doc1, doc2, recep, pharm, lab). Password for all is '123' suffix (e.g. admin123).</p>";

// 3. CREATE INVENTORY (Medicines)
$inventory = [
    ['id' => 101, 'name' => 'Panadol Extra', 'batch_no' => 'B-991', 'stock_qty' => 500, 'unit_price' => 20.00, 'expiry_date' => '2026-12-30'],
    ['id' => 102, 'name' => 'Augmentin 625mg', 'batch_no' => 'AG-22', 'stock_qty' => 100, 'unit_price' => 450.00, 'expiry_date' => '2025-08-15'],
    ['id' => 103, 'name' => 'Disprin (Soluble)', 'batch_no' => 'DS-11', 'stock_qty' => 1000, 'unit_price' => 5.00, 'expiry_date' => '2027-01-01'],
    ['id' => 104, 'name' => 'Brufen 400mg', 'batch_no' => 'BR-55', 'stock_qty' => 8, 'unit_price' => 15.00, 'expiry_date' => '2025-05-20'], // Low Stock Example
    ['id' => 105, 'name' => 'Insulin Humulin', 'batch_no' => 'INS-01', 'stock_qty' => 50, 'unit_price' => 1200.00, 'expiry_date' => '2025-11-30'],
    ['id' => 106, 'name' => 'Cough Syrup (Hydryllin)', 'batch_no' => 'SY-88', 'stock_qty' => 200, 'unit_price' => 120.00, 'expiry_date' => '2026-06-10']
];
file_put_contents(FILE_INVENTORY, json_encode($inventory, JSON_PRETTY_PRINT));
echo "<p>✅ Created Pharmacy Inventory (6 Items).</p>";

// 4. CREATE PATIENTS
$patients = [
    ['id' => 'PAT-25-101', 'name' => 'Usman Ahmed', 'dob' => '1990-05-15', 'gender' => 'Male', 'phone' => '03001234567', 'address' => 'Model Town, Gujrat', 'created_at' => date('Y-m-d H:i:s')],
    ['id' => 'PAT-25-102', 'name' => 'Fatima Bibi', 'dob' => '1985-08-22', 'gender' => 'Female', 'phone' => '03217654321', 'address' => 'Railway Road, Jalalpur', 'created_at' => date('Y-m-d H:i:s')],
    ['id' => 'PAT-25-103', 'name' => 'Baby Ayesha', 'dob' => '2023-01-10', 'gender' => 'Female', 'phone' => '03339998887', 'address' => 'Village Tanda', 'created_at' => date('Y-m-d H:i:s')]
];
file_put_contents(FILE_PATIENTS, json_encode($patients, JSON_PRETTY_PRINT));
echo "<p>✅ Created Patients (3 Records).</p>";

// 5. CREATE VISITS (Appointments)
$visits = [
    ['id' => 'OPD-25-501', 'patient_id' => 'PAT-25-101', 'doctor_id' => 2, 'department' => 'Cardiology', 'status' => 'waiting', 'visit_date' => date('Y-m-d H:i:s')],
    ['id' => 'OPD-25-502', 'patient_id' => 'PAT-25-102', 'doctor_id' => 3, 'department' => 'Pediatrics', 'status' => 'waiting', 'visit_date' => date('Y-m-d H:i:s')]
];
file_put_contents(FILE_VISITS, json_encode($visits, JSON_PRETTY_PRINT));
echo "<p>✅ Created Active Appointments (2 Waiting).</p>";

// 6. INITIALIZE EMPTY FILES (To prevent errors)
if (!file_exists(DIR_DATA . 'prescriptions.json')) file_put_contents(DIR_DATA . 'prescriptions.json', json_encode([]));
if (!file_exists(FILE_LAB)) file_put_contents(FILE_LAB, json_encode([]));

echo "<hr><h3>🎉 System Ready!</h3>";
echo "<a href='index.php'>Go to Homepage</a> | <a href='doctor/login.php'>Staff Login</a>";
?>
-- 1. CLEANUP (Optional: Remove if you don't want to reset existing data)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS bill_payments;
DROP TABLE IF EXISTS invoice_payments;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS bills;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS transaction_lines;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS parties;
DROP TABLE IF EXISTS accounts;
SET FOREIGN_KEY_CHECKS = 1;

-- 2. CORE TABLES [cite: 18]
CREATE TABLE accounts (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  type ENUM('ASSET','LIABILITY','EQUITY','INCOME','EXPENSE') NOT NULL
);

CREATE TABLE parties (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(30), 
  whatsapp VARCHAR(30), 
  address TEXT,
  opening_balance DECIMAL(14,2) DEFAULT 0,
  opening_type ENUM('DR','CR') DEFAULT 'DR',
  credit_terms_days INT DEFAULT 0
);

CREATE TABLE transactions (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  date DATE NOT NULL,
  ref_no VARCHAR(50),
  doc_type ENUM('SALE','PURCHASE','RECEIPT','PAYMENT','JOURNAL','TRANSFER','INSTALLMENT') NOT NULL,
  party_id BIGINT NULL,
  notes TEXT,
  created_by BIGINT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transaction_lines (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  transaction_id BIGINT NOT NULL,
  account_id BIGINT NOT NULL,
  debit DECIMAL(14,2) DEFAULT 0,
  credit DECIMAL(14,2) DEFAULT 0,
  qty DECIMAL(14,3) NULL,
  price DECIMAL(14,2) NULL,
  tax DECIMAL(14,2) NULL,
  FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE
);

CREATE TABLE invoices (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  transaction_id BIGINT NOT NULL,
  due_date DATE NOT NULL,
  status ENUM('DRAFT','SENT','PARTIAL','PAID','OVERDUE') NOT NULL DEFAULT 'SENT',
  FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE
);

CREATE TABLE bills (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  transaction_id BIGINT NOT NULL,
  due_date DATE NOT NULL,
  status ENUM('OPEN','PARTIAL','PAID','OVERDUE') NOT NULL DEFAULT 'OPEN',
  FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE
);

CREATE TABLE payments (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  date DATE NOT NULL,
  party_id BIGINT NULL,
  amount DECIMAL(14,2) NOT NULL,
  type ENUM('RECEIPT','SUPPLIER_PAYMENT') NOT NULL
);

CREATE TABLE invoice_payments (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  invoice_id BIGINT NOT NULL,
  payment_id BIGINT NOT NULL,
  amount DECIMAL(14,2) NOT NULL,
  FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);

CREATE TABLE bill_payments (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  bill_id BIGINT NOT NULL,
  payment_id BIGINT NOT NULL,
  amount DECIMAL(14,2) NOT NULL,
  FOREIGN KEY (bill_id) REFERENCES bills(id) ON DELETE CASCADE
);

-- 3. SEED DATA (Essential for App Logic)
-- These accounts are required for the ReportsController and AgingController to function [cite: 1, 16]
INSERT INTO accounts (name, type) VALUES 
('Cash', 'ASSET'),
('Accounts Receivable', 'ASSET'),
('Accounts Payable', 'LIABILITY'),
('Sales Income', 'INCOME'),
('Service Income', 'INCOME'),
('Purchase Expense', 'EXPENSE'),
('General Expense', 'EXPENSE'),
('Owner Equity', 'EQUITY');
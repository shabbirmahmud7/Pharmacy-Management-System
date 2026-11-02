# Pharmacy Sales & Inventory Management System

A web-based pharmacy management application proposed to streamline medicine sales, inventory tracking, supplier receiving, expiry monitoring, and reporting. The system is intended to reduce manual record-keeping, improve stock accuracy, help prevent the sale of expired medicines, and speed up customer billing.

> **Project status:** This README is based on the course project proposal. Actual setup commands, configuration values, database scripts, and implemented features should be updated to match the source code as development progresses.

## Project Information

- **Programme:** ISDB-BISEW IT Scholarship Programme
- **Project:** Pharmacy Sales & Inventory Management System
- **Submitted by:** Shabbir Mahmud
- **Trainee ID:** 1297408
- **Batch & Track:** PWAD/73
- **Training Center (TSP):** US Software Limited

## Objectives

- Replace manual invoices with a Point-of-Sale (POS) checkout and billing interface.
- Track stock received, sold, expired, and currently available.
- Provide low-stock and expiry-date alerts.
- Protect records with role-based access for administrators, cashiers, and store staff.
- Generate summaries for daily revenue, stock turnover, and supplier audit trails.

## Technology Stack

| Component | Technology / Tool | Purpose |
|---|---|---|
| Backend | Core PHP 8.2+ / 8.3 | Request handling, sessions, business logic, and security |
| Database | MySQL / MariaDB | Relational data storage, queries, indexing, and data integrity |
| Frontend | HTML5, CSS3, JavaScript, Bootstrap | Responsive UI, dynamic forms, and modal dialogs |
| Local environment | XAMPP | Apache web server and MariaDB database environment |
| Code editor | Notepad++ or Visual Studio Code | Writing and managing project files |

## User Roles and Permissions

| Role | Main access | Responsibilities |
|---|---|---|
| Administrator | All modules | Manage user accounts and categories/types, approve receiving, override inventory, and review financial reports |
| Cashier | Sales POS and customer records | Process sales, calculate bills, accept payments, issue receipts, and track change |
| Store Staff | Inventory and receiving | Record supplier deliveries, track batch expiry dates, audit low-stock items, and maintain product lists |

## Main Modules

### 1. Sales (POS)
- Record reference number, customer, products, quantities, unit prices, totals, amount tendered, and change.
- Maintain a dynamic order list and calculate totals in real time.
- Generate printable cash receipts.
- Deduct stock automatically after a completed payment.

### 2. Inventory Log
- Track product name, stock in, stock out, expired quantity, available stock, and expiry date.
- Provide a central movement ledger, search filters, and pagination.
- Calculate current stock from inventory movements.

### 3. Receiving Stock
- Record receiving date, reference number, supplier, received items, total amount, and action.
- Store batch quantities, cost prices, and batch expiry dates.
- Update inventory when a receiving record is saved.

### 4. Medicine Master
- Maintain SKU, product name, category, type/form, measurement (for example, 500 mg), price, and prescription requirement.
- Support adding, editing, and archiving medicine items.

### 5. Category and Type
- Organize medicines by category and formulation type, such as capsule, tablet, liquid, inhaler, or drops.

### 6. Expired Products
- Track product ID, quantity, expiry date, confirmation status, and date logged.
- Isolate confirmed expired stock to help prevent accidental sale.

### 7. Supplier and Customer Management
- Maintain names, contact numbers, address details, and order/reference history.

### 8. User Management
- Manage full names, usernames, password hashes, and user roles.
- Apply session-based permission control.

## Database Design Overview

The proposal describes a relational database containing **10 tables**. The relationships specifically outlined in the proposal include:

- `product_list` links to `category_list` and `type_list` through `category_id` and `type_id`.
- `inventory` acts as the central stock ledger and uses `stock_from` (such as `receiving` or `Sales`) and `form_id` to associate movements with receiving or sales records.
- `receiving_list` relates receiving records to `supplier_list` through `supplier_id`.
- `sales_list` relates sales records to `customer_list` through `customer_id`.
- `expired_product` stores stock units confirmed as expired from batch records.

The proposal does not enumerate the complete schema for all 10 tables. Use the project's actual SQL schema as the source of truth once it is available.

## Core Workflows

### Stock Receiving

1. Staff create a new receiving entry and select a supplier.
2. They enter purchase reference details and add items with quantities, cost prices, and batch expiry dates.
3. On save, the system records the receiving transaction and adds positive stock movements to the inventory ledger.
4. Available stock figures update across the system.

### Sales Checkout

1. The cashier selects a customer or uses the walk-in customer option, then selects or scans products.
2. The system verifies available stock before adding an item.
3. The cashier enters the amount tendered; the system calculates the change.
4. On completion, the sales record is saved, inventory is reduced, and a printable receipt is generated.

## Security and Data Integrity

The proposal specifies the following safeguards:

- **SQL injection prevention:** Use PDO prepared statements with parameter binding for database operations.
- **Password storage:** Store passwords using PHP's `password_hash()` function; never store plaintext passwords.
- **Session and role checks:** Verify login status and role permissions before rendering protected pages or controllers.
- **Server-side validation:** Validate quantities, prices, and date values on the server.

## Development Environment

The proposed local environment uses XAMPP with Apache and MySQL/MariaDB, PHP, and a code editor such as Visual Studio Code or Notepad++.

High-level development setup:

1. Install a compatible XAMPP environment with PHP and MySQL/MariaDB.
2. Place the project files in the local web server's document root.
3. Create the project database and import the project's SQL schema when available.
4. Configure the application's database connection using local environment settings.
5. Start Apache and MySQL/MariaDB, then open the project through the local web server.

> The proposal does not specify a repository structure, database name, database credentials, SQL import filename, or exact URL. Use the values and commands defined by the actual project files rather than assuming defaults.

## Expected Deliverables

The intended result is a pharmacy sales and inventory application that reduces administrative work, helps prevent losses caused by unmonitored medicine expiry, accelerates checkout, and improves stock accountability.

## Future Documentation Updates

When the implementation is available, update this README with the actual folder structure, exact installation commands, database setup details, configuration file names, screenshots, and any implemented limitations.

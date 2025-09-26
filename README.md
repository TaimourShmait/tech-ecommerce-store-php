# Tech E-Commerce Store (PHP/MySQL)

PHP/MySQL e-commerce platform for electronics retail. Features product catalog with filtering, multi-image galleries, cart system, admin panel with CRUD operations, user authentication, and more. Built with Bootstrap 5 and secure coding practices. Demonstrates modern web development with normalized database design and responsive interface.

## Prerequisites

- XAMPP, WAMP, or MAMP (PHP 7.4+ and MySQL 5.7+)
- Web browser
- Text editor (optional)

## Installation & Setup

### 1. Download & Extract
- Clone or download this repository
- Extract to your web server directory:
  - **XAMPP**: `C:\xampp\htdocs\`
  - **WAMP**: `C:\wamp64\www\`
  - **MAMP**: `/Applications/MAMP/htdocs/`

### 2. Database Setup (IMPORTANT)
The `database/` folder contains the complete database schema and sample data.

1. Start your web server (Apache + MySQL)
2. Open phpMyAdmin (usually http://localhost/phpmyadmin)
3. Create a new database named `rps_db`
4. Import the database file:
   - Click "Import" tab
   - Choose file: `database/real_pixel_store_db.sql`
   - Click "Go"

### 3. Configure Database Connection
Edit `includes/db.php` with your database credentials:
```php

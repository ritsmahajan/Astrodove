<?php
// config.php - Database Configuration
// IMPORTANT: Update these with your actual database credentials

define('DB_HOST', 'localhost');
define('DB_USER', 'your_database_username');
define('DB_PASS', 'your_database_password');
define('DB_NAME', 'astrodove_db');

// Admin credentials (change these!)
define('ADMIN_USERNAME', 'astrodove_admin');
define('ADMIN_PASSWORD_HASH', password_hash('Astro@2026!', PASSWORD_DEFAULT));

// Create database connection
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        return $conn;
    } catch (Exception $e) {
        die("Database connection failed. Please check your configuration.");
    }
}

// Create products table if it doesn't exist
function initializeDatabase() {
    $conn = getDBConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        category VARCHAR(100) NOT NULL,
        badge VARCHAR(50),
        description TEXT NOT NULL,
        benefits TEXT,
        image LONGTEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === FALSE) {
        die("Error creating table: " . $conn->error);
    }
    
    $conn->close();
}

// Initialize database on first run
initializeDatabase();
?>

<?php
// Include the database connection file
include_once('../../db-connect.php');

// Admin user credentials
$adminId = 'admin';
$adminPassword = 'admin123';
$adminName = 'Administrator';
$adminPhone = '1234567890'; 
$adminSex = 'M'; // Adding sex value (typically 'M' or 'F')

// Hash the password
$hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

// Begin transaction
pg_query($link, "BEGIN");

try {
    // First, check if the admin user already exists
    $checkAdmin = pg_query_params($link, "SELECT userid FROM users WHERE userid = $1", array($adminId));
    
    if (pg_num_rows($checkAdmin) > 0) {
        echo "Admin user already exists in the users table.\n";
    } else {
        // Insert into users table
        $sqlUsers = "INSERT INTO users (userid, password, usertype) VALUES ($1, $2, $3)";
        $resultUsers = pg_query_params($link, $sqlUsers, array($adminId, $hashedPassword, 'admin'));
        
        if (!$resultUsers) {
            throw new Exception("Failed to create admin user in users table: " . pg_last_error($link));
        }
        
        echo "Admin user successfully added to users table.\n";
    }
    
    // Check if admin exists in admin table
    $checkAdminTable = pg_query_params($link, "SELECT id FROM admin WHERE id = $1", array($adminId));
    
    if (pg_num_rows($checkAdminTable) > 0) {
        echo "Admin user already exists in the admin table.\n";
    } else {
        // Insert into admin table - include all required columns including sex
        $sqlAdmin = "INSERT INTO admin (id, name, password, phone, email, dob, address, sex) 
                     VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
        $resultAdmin = pg_query_params($link, $sqlAdmin, array(
            $adminId, 
            $adminName, 
            $hashedPassword, 
            $adminPhone, // Phone number
            'admin@example.com', // Email
            date('Y-m-d'), // Current date for dob
            'Default Address', // Address
            $adminSex // Sex value
        ));
        
        if (!$resultAdmin) {
            throw new Exception("Failed to create admin user in admin table: " . pg_last_error($link));
        }
        
        echo "Admin user successfully added to admin table.\n";
    }
    
    // Commit the transaction
    pg_query($link, "COMMIT");
    echo "Transaction committed successfully. Admin user created with ID: $adminId\n";
    echo "You can now log in with username 'admin' and password 'admin123'\n";
    
} catch (Exception $e) {
    // Rollback the transaction if any query failed
    pg_query($link, "ROLLBACK");
    echo "Error: " . $e->getMessage() . "\n";
}
?>
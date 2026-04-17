<?php
/**
 * Admin Account Setup Script
 * Run this once to create the admin account in the uoc_football database
 */

// Database connection details
$host = 'localhost';
$db = 'uoc_football';
$user = 'root';
$password = '';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Admin credentials
    $nic = '0000000001';  // Unique NIC for admin
    $user_id = 'admin';
    $plain_password = 'admin';
    $hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);
    $first_name = 'Admin';
    $last_name = 'User';
    $email = 'admin@uocfootball.com';
    $phone_number = '+94-0-0000000';
    
    // Insert into users table
    $query = "INSERT INTO users (nic, user_id, password, first_name, last_name, email, phone_number) 
              VALUES (:nic, :user_id, :password, :first_name, :last_name, :email, :phone_number)";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':nic' => $nic,
        ':user_id' => $user_id,
        ':password' => $hashed_password,
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':phone_number' => $phone_number
    ]);
    
    // Insert into admins table
    $adminQuery = "INSERT INTO admins (nic) VALUES (:nic)";
    $adminStmt = $pdo->prepare($adminQuery);
    $adminStmt->execute([':nic' => $nic]);
    
    echo "<h2 style='color: green;'>✓ Admin account created successfully!</h2>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin</p>";
    echo "<p><strong>NIC:</strong> $nic</p>";
    echo "<p>You can now login with these credentials.</p>";
    echo "<p><strong>Note:</strong> For security, delete this setup file after use.</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>✗ Error creating admin account:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>

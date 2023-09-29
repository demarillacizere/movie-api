<?php
// Database configuration
$host = '127.0.0.1'; // e.g., 'localhost' or '127.0.0.1'
$dbname = 'movie_api';
$username = 'root';
$password = '';

try {
    // Create a PDO instance for database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Optional: Set PDO to throw exceptions on errors (recommended for debugging)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // You now have a database connection in the $pdo variable.
    // You can use this connection for various database operations.
} catch (PDOException $e) {
    // Handle database connection errors
    echo "Connection failed: " . $e->getMessage();
}
?>

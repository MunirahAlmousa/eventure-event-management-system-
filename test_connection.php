<?php
/**
 * Database Connection Test Script
 * Use this to verify your database connection is working
 * Access: http://localhost/EventManagmentSystem_La/backend/test_connection.php
 */

require_once 'config/database.php';

echo "<h1>Eventure Database Connection Test</h1>";
echo "<hr>";

$database = new Database();
$db = $database->connect();

if ($db) {
    echo "<h2 style='color: green;'>✅ Database connection successful!</h2>";
    echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
    echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
    echo "<hr>";

    try {
        // Test 1: Count users
        $stmt = $db->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "<p>✅ Total users in database: <strong>" . $result['count'] . "</strong></p>";

        // Test 2: Count events
        $stmt = $db->query("SELECT COUNT(*) as count FROM events");
        $result = $stmt->fetch();
        echo "<p>✅ Total events in database: <strong>" . $result['count'] . "</strong></p>";

        // Test 3: Count tickets
        $stmt = $db->query("SELECT COUNT(*) as count FROM user_tickets");
        $result = $stmt->fetch();
        echo "<p>✅ Total tickets in database: <strong>" . $result['count'] . "</strong></p>";

        // Test 4: List all tables
        echo "<hr>";
        echo "<h3>Database Tables:</h3>";
        $stmt = $db->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . $table . "</li>";
        }
        echo "</ul>";

        // Test 5: Show manager accounts
        echo "<hr>";
        echo "<h3>Manager Accounts:</h3>";
        $stmt = $db->query("SELECT id, name, email, type FROM users WHERE type = 'manager'");
        $managers = $stmt->fetchAll();

        if (count($managers) > 0) {
            echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Type</th></tr>";
            foreach ($managers as $manager) {
                echo "<tr>";
                echo "<td>" . $manager['id'] . "</td>";
                echo "<td>" . $manager['name'] . "</td>";
                echo "<td>" . $manager['email'] . "</td>";
                echo "<td>" . $manager['type'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>⚠️ No manager accounts found. Please run the SQL file to insert default managers.</p>";
        }

        echo "<hr>";
        echo "<h2 style='color: green;'>✅ All tests passed! Your database is ready to use.</h2>";

    } catch (PDOException $e) {
        echo "<h2 style='color: red;'>❌ Database query error:</h2>";
        echo "<p>" . $e->getMessage() . "</p>";
    }

} else {
    echo "<h2 style='color: red;'>❌ Database connection failed!</h2>";
    echo "<p><strong>Error:</strong> " . $database->getError() . "</p>";
    echo "<hr>";
    echo "<h3>Troubleshooting Steps:</h3>";
    echo "<ol>";
    echo "<li>Make sure XAMPP MySQL is running</li>";
    echo "<li>Check database credentials in <code>backend/config/database.php</code></li>";
    echo "<li>Verify database <code>" . DB_NAME . "</code> exists in phpMyAdmin</li>";
    echo "<li>Default XAMPP credentials: User='root', Password='' (empty)</li>";
    echo "</ol>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1 { color: #333; }
    code {
        background: #e0e0e0;
        padding: 2px 6px;
        border-radius: 3px;
    }
    table {
        background: white;
        margin: 10px 0;
    }
</style>

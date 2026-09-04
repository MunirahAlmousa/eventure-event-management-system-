<?php
/**
 * Database Configuration for Eventure Event Management System
 * XAMPP MySQL Connection
 */

// Database configuration
define('DB_HOST', 'localhost');      // MySQL host (default for XAMPP)
define('DB_USER', 'root');           // MySQL username (default for XAMPP)
define('DB_PASS', '');               // MySQL password (empty by default for XAMPP)
define('DB_NAME', 'eventure_db');    // Database name
define('DB_CHARSET', 'utf8mb4');

// Create database connection
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $charset = DB_CHARSET;
    private $conn;
    private $error;

    /**
     * Connect to database
     */
    public function connect() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);

        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            echo "Connection Error: " . $this->error;
        }

        return $this->conn;
    }

    /**
     * Get connection error
     */
    public function getError() {
        return $this->error;
    }
}
?>

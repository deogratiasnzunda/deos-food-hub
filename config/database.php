<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
class Database {
    private $host = "localhost";
    private $db_name = "deos_food_hub";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

// Helper functions
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function redirect($url, $message = "", $error = false) {
    $param = $message ? "?message=" . urlencode($message) : "";
    $param .= $error ? "&error=1" : "";
    header("Location: " . $url . $param);
    exit();
}
?>
<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.html');
    exit;
}

// Database connection
 $host = 'localhost';
 $db = 'contact_form';
 $user = 'root';
 $pass = '';
 $charset = 'utf8mb4';

 $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
 $options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die('Database connection failed');
}

// Get message ID
 $messageId = $_GET['id'] ?? null;

if (!$messageId) {
    header('Location: dashboard.php');
    exit;
}

// Delete message
 $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
 $stmt->execute([$messageId]);

header('Location: dashboard.php');
exit;
?>
<?php
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

// Validate CAPTCHA
if (isset($_POST['captcha'], $_POST['num1'], $_POST['num2'])) {
    $captchaAnswer = (int)$_POST['captcha'];
    $correctAnswer = (int)$_POST['num1'] + (int)$_POST['num2'];
    
    if ($captchaAnswer !== $correctAnswer) {
        die('CAPTCHA verification failed');
    }
}

// Validate inputs
 $name = trim($_POST['name'] ?? '');
 $email = trim($_POST['email'] ?? '');
 $subject = trim($_POST['subject'] ?? '');
 $message = trim($_POST['message'] ?? '');

// Server-side validation
if (strlen($name) < 3) {
    die('Invalid name');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email');
}

if (empty($subject)) {
    die('Subject is required');
}

if (strlen($message) < 10) {
    die('Message too short');
}

// Sanitize inputs
 $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
 $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
 $subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
 $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Store in database
try {
    $stmt = $pdo->prepare("INSERT INTO contact_messages (full_name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message, $_SERVER['REMOTE_ADDR']]);
    echo 'success';
} catch (PDOException $e) {
    die('Database error');
}
?>
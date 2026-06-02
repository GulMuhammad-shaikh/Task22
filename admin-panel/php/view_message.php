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

// Fetch message
 $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
 $stmt->execute([$messageId]);
 $message = $stmt->fetch();

if (!$message) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <div class="header-left">
                <i class="fas fa-shield-alt"></i>
                <h1>Message Details</h1>
            </div>
            <div class="header-right">
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </header>

        <div class="admin-content">
            <div class="message-detail">
                <div class="message-header">
                    <h2><?php echo htmlspecialchars($message['subject']); ?></h2>
                    <div class="message-meta">
                        <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($message['full_name']); ?></p>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($message['email']); ?></p>
                        <p><i class="fas fa-calendar"></i> <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></p>
                        <p><i class="fas fa-globe"></i> <?php echo htmlspecialchars($message['ip_address']); ?></p>
                    </div>
                </div>
                
                <div class="message-content">
                    <h3>Message</h3>
                    <div class="message-text">
                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                    </div>
                </div>
                
                <div class="message-actions">
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <a href="php/delete_message.php?id=<?php echo $message['id']; ?>" 
                       class="btn btn-danger" 
                       onclick="return confirm('Are you sure you want to delete this message?');">
                        <i class="fas fa-trash"></i> Delete Message
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
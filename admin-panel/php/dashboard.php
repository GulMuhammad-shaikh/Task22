<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.html');
    exit;
}

// Check session timeout (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header('Location: index.html');
    exit;
}
 $_SESSION['last_activity'] = time();

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

// Get messages
 $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
 $messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <div class="header-left">
                <i class="fas fa-shield-alt"></i>
                <h1>Admin Dashboard</h1>
            </div>
            <div class="header-right">
                <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></span>
                <a href="php/logout.php" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </header>

        <div class="admin-content">
            <div class="dashboard-header">
                <h2>Contact Messages</h2>
                <div class="stats">
                    <div class="stat-item">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo count($messages); ?> Messages</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-clock"></i>
                        <span><?php echo date('M d, Y'); ?></span>
                    </div>
                </div>
            </div>

            <div class="messages-container">
                <?php if (empty($messages)): ?>
                    <div class="no-messages">
                        <i class="fas fa-inbox"></i>
                        <p>No messages found</p>
                    </div>
                <?php else: ?>
                    <div class="messages-grid">
                        <?php foreach ($messages as $message): ?>
                            <div class="message-card">
                                <div class="message-header">
                                    <div class="message-meta">
                                        <h3><?php echo htmlspecialchars($message['subject']); ?></h3>
                                        <p class="message-date">
                                            <i class="fas fa-calendar"></i>
                                            <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?>
                                        </p>
                                    </div>
                                    <div class="message-actions">
                                        <a href="view_message.php?id=<?php echo $message['id']; ?>" class="btn btn-small">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="php/delete_message.php?id=<?php echo $message['id']; ?>" 
                                           class="btn btn-small btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this message?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="message-preview">
                                    <div class="message-sender">
                                        <i class="fas fa-user"></i>
                                        <span><?php echo htmlspecialchars($message['full_name']); ?></span>
                                    </div>
                                    <div class="message-email">
                                        <i class="fas fa-envelope"></i>
                                        <span><?php echo htmlspecialchars($message['email']); ?></span>
                                    </div>
                                    <div class="message-content">
                                        <?php echo htmlspecialchars(substr($message['message'], 0, 150)) . '...'; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 5 minutes
        setTimeout(() => {
            location.reload();
        }, 300000);
    </script>
</body>
</html>
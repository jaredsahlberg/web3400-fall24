<?php
// Include configuration and authentication
include 'config.php';
include 'templates/head.php';
include 'templates/nav.php';

// Ensure only admins can access this page
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['messages'][] = "You must be an administrator to access this resource.";
    header('Location: login.php');
    exit();
}

// Fetch all contact messages from the database
$stmt = $pdo->prepare('SELECT * FROM contact_us ORDER BY submitted_at DESC');
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="section">
    <div class="container">
        <h1 class="title">All Contact Messages</h1>
        
        <!-- Display messages in a table -->
        <table class="table is-striped is-fullwidth">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= htmlspecialchars($message['name']); ?></td>
                        <td><?= htmlspecialchars($message['email']); ?></td>
                        <td><?= htmlspecialchars($message['message']); ?></td>
                        <td><?= htmlspecialchars($message['submitted_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Back to Dashboard Button -->
        <a href="admin_dashboard.php" class="button is-link">Back to Dashboard</a>
    </div>
</section>

<?php include 'templates/footer.php'; ?>

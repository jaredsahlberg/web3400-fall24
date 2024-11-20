<?php
// Include configuration and start session
include 'config.php';

// Only allow admin access
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Retrieve tickets from the database
$sql = "SELECT * FROM tickets ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/head.php'; ?>
</head>
<body class="has-navbar-fixed-top">
    <?php include 'templates/nav.php'; ?>

    <section class="section">
        <div class="container">
            <h1 class="title">Manage Tickets</h1>
            <div class="buttons">
                <a href="ticket_create.php" class="button is-link">Create a new ticket</a>
            </div>
            <div class="columns is-multiline">
                <?php foreach ($tickets as $ticket) : ?>
                    <div class="column is-one-third">
                        <div class="card">
                            <header class="card-header">
                                <p class="card-header-title">
                                    <?= htmlspecialchars($ticket['title'], ENT_QUOTES) ?>
                                    &nbsp;
                                    <?php if ($ticket['priority'] == 'Low') : ?>
                                        <span class="tag"><?= $ticket['priority'] ?></span>
                                    <?php elseif ($ticket['priority'] == 'Medium') : ?>
                                        <span class="tag is-warning"><?= $ticket['priority'] ?></span>
                                    <?php elseif ($ticket['priority'] == 'High') : ?>
                                        <span class="tag is-danger"><?= $ticket['priority'] ?></span>
                                    <?php endif; ?>
                                </p>
                                <a href="ticket_detail.php?id=<?= $ticket['id'] ?>" class="card-header-icon">
                                    <span class="icon">
                                        <?php if ($ticket['status'] == 'Open') : ?>
                                            <i class="far fa-clock"></i>
                                        <?php elseif ($ticket['status'] == 'In Progress') : ?>
                                            <i class="fas fa-tasks"></i>
                                        <?php elseif ($ticket['status'] == 'Closed') : ?>
                                            <i class="fas fa-times"></i>
                                        <?php endif; ?>
                                    </span>
                                </a>
                            </header>
                            <div class="card-content">
                                <div class="content">
                                    <time datetime="<?= $ticket['created_at'] ?>">Created: <?= time_ago($ticket['created_at']) ?></time>
                                    <p><?= htmlspecialchars(substr($ticket['description'], 0, 40), ENT_QUOTES) ?>...</p>
                                </div>
                            </div>
                            <footer class="card-footer">
                                <a href="ticket_detail.php?id=<?= $ticket['id'] ?>" class="card-footer-item">View</a>
                                <a href="ticket_edit.php?id=<?= $ticket['id'] ?>" class="card-footer-item">Edit</a>
                                <a href="ticket_delete.php?id=<?= $ticket['id'] ?>" class="card-footer-item">Delete</a>
                            </footer>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include 'templates/footer.php'; ?>
</body>
</html>






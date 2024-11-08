<?php
// Include the configuration file for database access
include 'config.php';

// Start session to manage authentication and authorization
session_start();

// Secure and only allow 'admin' users to access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login page if not an admin
    exit();
}

// Check if $_GET['id'] exists; if it does, get the ticket record from the database
if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];
    
    // Prepare a statement to get the ticket details
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = :id");
    $stmt->bindParam(':id', $ticket_id, PDO::PARAM_INT);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no ticket is found, display an error
    if (!$ticket) {
        echo "The ticket with that ID does not exist.";
        exit();
    }
    
    // Fetch comments for the ticket
    $stmt = $pdo->prepare("SELECT * FROM ticket_comments WHERE ticket_id = :ticket_id ORDER BY created_at ASC");
    $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Update ticket status if a status link was clicked
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $updateStmt = $pdo->prepare("UPDATE tickets SET status = :status WHERE id = :id");
        $updateStmt->bindParam(':status', $status);
        $updateStmt->bindParam(':id', $ticket_id);
        $updateStmt->execute();
        
        // Redirect to avoid repeated status updates on page reload
        header("Location: ticket-detail.php?id=$ticket_id");
        exit();
    }

    // Check if the comment form has been submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['msg'])) {
        $comment = htmlspecialchars($_POST['msg'], ENT_QUOTES);
        $user_id = $_SESSION['user_id']; // Assuming `user_id` is stored in the session
        
        // Insert the ticket comment
        $insertStmt = $pdo->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment, created_at) 
                                     VALUES (:ticket_id, :user_id, :comment, NOW())");
        $insertStmt->bindParam(':ticket_id', $ticket_id);
        $insertStmt->bindParam(':user_id', $user_id);
        $insertStmt->bindParam(':comment', $comment);
        $insertStmt->execute();
        
        // Redirect to avoid form resubmission
        header("Location: ticket-detail.php?id=$ticket_id");
        exit();
    }
} else {
    echo "No ticket ID specified.";
    exit();
}
?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Ticket Detail</h1>
    <p class="subtitle">
        <a href="tickets.php">View all tickets</a>
    </p>
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
            <button class="card-header-icon">
                <a href="ticket_detail.php?id=<?= $ticket['id'] ?>">
                    <span class="icon">
                        <?php if ($ticket['status'] == 'Open') : ?>
                            <i class="far fa-clock fa-2x"></i>
                        <?php elseif ($ticket['status'] == 'In Progress') : ?>
                            <i class="fas fa-tasks fa-2x"></i>
                        <?php elseif ($ticket['status'] == 'Closed') : ?>
                            <i class="fas fa-times fa-2x"></i>
                        <?php endif; ?>
                    </span>
                </a>
            </button>
        </header>
        <div class="card-content">
            <div class="content">
                <time datetime="2016-1-1">Created: <?= date('F dS, G:ia', strtotime($ticket['created_at'])) ?></time>
                <br>
                <p><?= htmlspecialchars($ticket['description'], ENT_QUOTES) ?></p>
            </div>
        </div>
        <footer class="card-footer">
            <a href="ticket_detail.php?id=<?= $ticket['id'] ?>&status=Closed" class="card-footer-item">
                <span class="icon"><i class="fas fa-times fa-2x"></i></span>
                <span>&nbsp;Close</span>
            </a>
            <a href="ticket_detail.php?id=<?= $ticket['id'] ?>&status=In Progress" class="card-footer-item">
                <span><i class="fas fa-tasks fa_2x"></i></i></span>
                <span>&nbsp;In Progress</span>
            </a>
            <a href="ticket_detail.php?id=<?= $ticket['id'] ?>&status=Open" class="card-footer-item">
                <span><i class="far fa-clock fa-2x"></i></span>
                <span>&nbsp;Re-Open</span>
            </a>
        </footer>
    </div>
    <hr>
    <div class="block">
        <form action="" method="post">
            <div class="field">
                <label class="label"></label>
                <div class="control">
                    <textarea name="msg" class="textarea" placeholder="Enter your comment here..." required></textarea>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-link">Post Comment</button>
                </div>
            </div>
        </form>
        <hr>
        <div class="content">
            <h3 class="title is-4">Comments</h3>
            <?php foreach ($comments as $comment) : ?>
                <p class="box">
                    <span><i class="fas fa-comment"></i></span>
                    <?= date('F dS, G:ia', strtotime($comment['created_at'])) ?>
                    <br>
                    <?= nl2br(htmlspecialchars($comment['comment'], ENT_QUOTES)) ?>
                    <br>
                </p>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- END YOUR CONTENT -->
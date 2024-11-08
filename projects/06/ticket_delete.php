<?php
// Include the configuration file for database access
include 'config.php';

// Start session to manage authentication and authorization
session_start();

// Secure and only allow 'admin' users to access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not an admin
    exit();
}

// Check if the ticket ID is provided in the URL
if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];

    // Retrieve the ticket record from the database
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = :id");
    $stmt->bindParam(':id', $ticket_id, PDO::PARAM_INT);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no ticket with the specified ID is found, display an error
    if (!$ticket) {
        echo "A ticket with that ID did not exist.";
        exit();
    }

    // Check if deletion is confirmed
    if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
        // Begin a transaction to ensure both deletions happen together
        $pdo->beginTransaction();
        
        try {
            // Delete all comments associated with the ticket
            $deleteCommentsStmt = $pdo->prepare("DELETE FROM ticket_comments WHERE ticket_id = :ticket_id");
            $deleteCommentsStmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
            $deleteCommentsStmt->execute();

            // Delete the ticket itself
            $deleteTicketStmt = $pdo->prepare("DELETE FROM tickets WHERE id = :id");
            $deleteTicketStmt->bindParam(':id', $ticket_id, PDO::PARAM_INT);
            $deleteTicketStmt->execute();

            // Commit the transaction
            $pdo->commit();

            // Redirect to tickets.php with a success message
            $_SESSION['message'] = "The ticket was successfully deleted.";
            header("Location: tickets.php");
            exit();
        } catch (Exception $e) {
            // Roll back the transaction if an error occurs
            $pdo->rollBack();
            echo "An error occurred while deleting the ticket: " . $e->getMessage();
        }
    } else {
        // Redirect to tickets.php if the user cancels the deletion
        header("Location: tickets.php");
        exit();
    }
} else {
    echo "No ticket ID specified.";
    exit();
}
?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Delete Ticket</h1>
    <p class="subtitle">Are you sure you want to delete ticket: <?= htmlspecialchars_decode($ticket['title']) ?></p>
    <div class="buttons">
        <a href="?id=<?= $ticket['id'] ?>&confirm=yes" class="button is-success">Yes</a>
        <a href="tickets.php" class="button is-danger">No</a>
    </div>
</section>
<!-- END YOUR CONTENT -->
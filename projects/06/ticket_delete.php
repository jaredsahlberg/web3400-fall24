<?php
// Include the configuration file for database access and start the session if not already started
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Secure and only allow 'admin' users to access this page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
    header("Location: login.php");
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

    if (!$ticket) {
        echo "A ticket with that ID does not exist.";
        exit();
    }

    // Check if the deletion is confirmed
    if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
        // Begin a transaction to ensure atomicity
        $pdo->beginTransaction();

        try {
            // Delete comments associated with the ticket
            $deleteCommentsStmt = $pdo->prepare("DELETE FROM ticket_comments WHERE ticket_id = :ticket_id");
            $deleteCommentsStmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
            $deleteCommentsStmt->execute();

            // Delete the ticket
            $deleteTicketStmt = $pdo->prepare("DELETE FROM tickets WHERE id = :id");
            $deleteTicketStmt->bindParam(':id', $ticket_id, PDO::PARAM_INT);
            $deleteTicketStmt->execute();

            // Commit transaction
            $pdo->commit();

            // Set a success message and redirect to tickets page
            $_SESSION['messages'][] = "The ticket was successfully deleted.";
            header("Location: tickets.php");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "An error occurred while deleting the ticket: " . $e->getMessage();
            exit();
        }
    }
} else {
    echo "No ticket ID specified.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/head.php'; ?>
</head>
<body>
    <?php include 'templates/nav.php'; ?>

    <!-- BEGIN YOUR CONTENT -->
    <section class="section">
        <div class="container">
            <h1 class="title">Delete Ticket</h1>
            <p class="subtitle">Are you sure you want to delete ticket: <strong><?= htmlspecialchars($ticket['title'], ENT_QUOTES) ?></strong>?</p>
            <div class="buttons">
                <a href="ticket_delete.php?id=<?= $ticket['id'] ?>&confirm=yes" class="button is-danger">Yes</a>
                <a href="tickets.php" class="button is-link">No</a>
            </div>
        </div>
    </section>
    <!-- END YOUR CONTENT -->

    <?php include 'templates/footer.php'; ?>
</body>
</html>






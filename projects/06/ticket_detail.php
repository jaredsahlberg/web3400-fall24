<?php
// Include the configuration file for database access and session handling
include 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Secure and only allow 'admin' users to access this page
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
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

    // If no ticket with the specified ID is found, display an error
    if (!$ticket) {
        echo "A ticket with that ID does not exist.";
        exit();
    }
} else {
    echo "No ticket ID specified.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details - <?= htmlspecialchars($siteName) ?></title>
    <!-- Bulma CSS for styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
</head>
<body class="has-navbar-fixed-top">

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Ticket Details</h1>
    <div class="box">
        <p><strong>Title:</strong> <?= htmlspecialchars($ticket['title'], ENT_QUOTES) ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($ticket['description'], ENT_QUOTES) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($ticket['status'], ENT_QUOTES) ?></p>
        <p><strong>Priority:</strong> <?= htmlspecialchars($ticket['priority'], ENT_QUOTES) ?></p>
        <p><strong>Created At:</strong> <?= htmlspecialchars($ticket['created_at'], ENT_QUOTES) ?></p>
    </div>
    <a href="tickets.php" class="button is-link mt-3">Back to Tickets</a>
</section>
<!-- END YOUR CONTENT -->

</body>
</html>


<?php
// Include the configuration file to access the database and start the session if not already started
include 'config.php';

// Secure and only allow 'admin' users to access this page
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
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
        $_SESSION['messages'][] = "A ticket with that ID does not exist.";
        header("Location: tickets.php");
        exit();
    }

    // Handle form submission for editing the ticket
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and update ticket details
        $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
        $description = htmlspecialchars($_POST['description'], ENT_QUOTES);
        $priority = $_POST['priority'];
        
        $updateStmt = $pdo->prepare("UPDATE tickets SET title = :title, description = :description, priority = :priority WHERE id = :id");
        $updateStmt->bindParam(':title', $title);
        $updateStmt->bindParam(':description', $description);
        $updateStmt->bindParam(':priority', $priority);
        $updateStmt->bindParam(':id', $ticket_id, PDO::PARAM_INT);
        
        if ($updateStmt->execute()) {
            $_SESSION['messages'][] = "The ticket was successfully updated.";
            header("Location: tickets.php");
            exit();
        } else {
            echo "Error: Unable to update the ticket.";
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
            <h1 class="title">Edit Ticket</h1>
            <form action="" method="post">
                <div class="field">
                    <label class="label">Title</label>
                    <div class="control">
                        <input class="input" type="text" name="title" value="<?= htmlspecialchars($ticket['title'], ENT_QUOTES) ?>" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Description</label>
                    <div class="control">
                        <textarea class="textarea" name="description" required><?= htmlspecialchars($ticket['description'], ENT_QUOTES) ?></textarea>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Priority</label>
                    <div class="control">
                        <div class="select">
                            <select name="priority">
                                <option value="Low" <?= $ticket['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
                                <option value="Medium" <?= $ticket['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="High" <?= $ticket['priority'] == 'High' ? 'selected' : '' ?>>High</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field is-grouped">
                    <div class="control">
                        <button type="submit" class="button is-link">Update Ticket</button>
                    </div>
                    <div class="control">
                        <a href="tickets.php" class="button is-light">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <!-- END YOUR CONTENT -->

    <?php include 'templates/footer.php'; ?>
</body>
</html>

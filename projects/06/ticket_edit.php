<?php
// Include the configuration file for database access
include 'config.php';

// Start session for authentication and authorization
session_start();

// Secure and only allow 'admin' users to access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not an admin
    exit();
}

// Check if the update form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Gather and sanitize form inputs
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES);
    $priority = $_POST['priority'];
    $ticket_id = $_POST['id'];

    // Prepare an SQL statement to update the ticket details in the database
    $sql = "UPDATE tickets SET title = :title, description = :description, priority = :priority, updated_at = NOW() WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // Bind parameters and execute the update
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':priority', $priority);
    $stmt->bindParam(':id', $ticket_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        // Redirect back to tickets.php with a success message
        $_SESSION['message'] = "The ticket was successfully updated.";
        header("Location: tickets.php");
        exit();
    } else {
        echo "Error: Unable to update ticket.";
    }
} else {
    // Else, it's an initial page request; fetch the ticket record by id
    if (isset($_GET['id'])) {
        $ticket_id = $_GET['id'];
        
        // Prepare and execute a query to fetch the ticket details
        $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = :id");
        $stmt->bindParam(':id', $ticket_id, PDO::PARAM_INT);
        $stmt->execute();
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no ticket is found, display an error
        if (!$ticket) {
            echo "The ticket with that ID does not exist.";
            exit();
        }
    } else {
        echo "No ticket ID specified.";
        exit();
    }
}
?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Edit Ticket</h1>
    <form action="" method="post">
        <div class="field">
            <label class="label">Title</label>
            <div class="control">
                <input class="input" type="text" name="title" value="<?= htmlspecialchars_decode($ticket['title']) ?>" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Description</label>
            <div class="control">
                <textarea class="textarea" name="description" required><?= htmlspecialchars_decode($ticket['description']) ?></textarea>
            </div>
        </div>
        <div class="field">
            <label class="label">Priority</label>
            <div class="control">
                <div class="select">
                    <select name="priority">
                        <option value="Low" <?= ($ticket['priority'] == 'Low') ? 'selected' : '' ?>>Low</option>
                        <option value="Medium" <?= ($ticket['priority'] == 'Medium') ? 'selected' : '' ?>>Medium</option>
                        <option value="High" <?= ($ticket['priority'] == 'High') ? 'selected' : '' ?>>High</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-link">Update Ticket</button>
            </div>
            <div class="control">
                <a href="tickets.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
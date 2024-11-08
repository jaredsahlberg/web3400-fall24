<?php
// Include the configuration file for database access and session handling
include 'config.php';

// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Secure and only allow logged-in users to access this page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Initialize a message variable to store any feedback
$message = "";

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and gather form inputs
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES);
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES);
    $priority = $_POST['priority'];
    $user_id = $_SESSION['user_id']; // Assume user_id is stored in the session

    // Check that required fields are not empty
    if (empty($title) || empty($description) || empty($priority)) {
        $message = "All fields are required.";
    } else {
        // Prepare an SQL statement to insert the new ticket into the database
        $sql = "INSERT INTO tickets (user_id, title, description, priority, created_at) 
                VALUES (:user_id, :title, :description, :priority, NOW())";
        $stmt = $pdo->prepare($sql);

        // Bind parameters and execute the statement
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':priority', $priority, PDO::PARAM_STR);

        if ($stmt->execute()) {
            // Redirect back to tickets.php with a success message
            $_SESSION['message'] = "The ticket was successfully added.";
            header("Location: tickets.php");
            exit();
        } else {
            $message = "Error: Unable to add the ticket.";
        }
    }
}
?>

<!-- BEGIN HTML CONTENT -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Ticket</title>
    <!-- Include Bulma CSS for styling -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <!-- Include Font Awesome for icons (optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<section class="section">
    <div class="container">
        <h1 class="title">Create Ticket</h1>

        <?php if (!empty($message)) : ?>
            <div class="notification is-danger">
                <?= $message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="field">
                <label class="label">Title</label>
                <div class="control">
                    <input class="input" type="text" name="title" placeholder="Ticket title" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Description</label>
                <div class="control">
                    <textarea class="textarea" name="description" placeholder="Ticket description" required></textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">Priority</label>
                <div class="control">
                    <div class="select">
                        <select name="priority">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-link">Create Ticket</button>
                </div>
                <div class="control">
                    <a href="tickets.php" class="button is-link is-light">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</section>
</body>
</html>
<!-- END HTML CONTENT -->

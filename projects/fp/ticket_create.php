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
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $priority = $_POST['priority'];
    $user_id = $_SESSION['user_id']; // Assume user_id is stored in the session

    // Validate priority
    $valid_priorities = ['Low', 'Medium', 'High'];
    if (!in_array($priority, $valid_priorities, true)) {
        $message = "Invalid priority value.";
    } elseif (empty($title) || empty($description)) {
        $message = "All fields are required.";
    } else {
        // Try to insert the ticket into the database
        try {
            $sql = "INSERT INTO tickets (user_id, title, description, priority, created_at) 
                    VALUES (:user_id, :title, :description, :priority, NOW())";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':priority', $priority, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $_SESSION['message'] = "The ticket was successfully added.";
                header("Location: tickets.php");
                exit();
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/head.php'; ?>
</head>
<body>
    <?php include 'templates/nav.php'; ?>

    <section class="section">
        <div class="container">
            <h1 class="title">Create Ticket</h1>

            <?php if (!empty($message)) : ?>
                <div class="notification is-danger">
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <!-- Ticket Creation Form -->
            <form action="" method="post">
                <div class="field">
                    <label class="label">Title</label>
                    <div class="control">
                        <input class="input" type="text" name="title" placeholder="Enter ticket title" required>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Description</label>
                    <div class="control">
                        <textarea class="textarea" name="description" placeholder="Enter ticket description" required></textarea>
                    </div>
                </div>

                <div class="field">
                    <label class="label">Priority</label>
                    <div class="control">
                        <div class="select">
                            <select name="priority">
                                <option value="Low" selected>Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="buttons">
                    <button type="submit" class="button is-link">Create Ticket</button>
                    <button type="reset" class="button is-light">Cancel</button>
                </div>
            </form>
        </div>
    </section>

    <?php include 'templates/footer.php'; ?>
</body>
</html>





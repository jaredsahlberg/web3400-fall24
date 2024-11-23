<?php
// Step 1: Include config.php file
include 'config.php';

// Step 2: Secure and only allow 'admin' users to access this page
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect user to login page or display an error message
    $_SESSION['messages'][] = "You must be an administrator to access that resource.";
    header('Location: login.php');
    exit;
}

// Step 3: Check if the $_GET['id'] exists (isset); if it does, get the user the record from the database and store it in the associative array $user. If a user record with that ID does not exist, display the message "A user with that ID did not exist."
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Prepare and execute a query to fetch the user by id
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no user is found, show the error
    if (!$user) {
        $_SESSION['messages'][] = "A user with that ID did not exist.";
    }

}

// Step 4: Check if $_GET['confirm'] == 'yes'. This means they clicked the 'yes' button to confirm the removal of the record. Prepare and execute a SQL DELETE statement where the user id == the $_GET['id']. Else (meaning they clicked 'no'), return them to the users_manage.php page.
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Prepare and execute the DELETE statement
    $deleteStmt = $pdo->prepare("DELETE FROM `users` WHERE `id` = ?");
    $deleteStmt->execute([$user_id]);

    // Set a success message and redirect to the manage users page
    $_SESSION['messages'][] = "User account for {$user['full_name']} has been deleted.";
    header('Location: users_manage.php');
    exit;
} elseif (isset($_GET['confirm']) && $_GET['confirm'] === 'no') {
    // Redirect back to the manage users page if they clicked "No"
    header('Location: users_manage.php');
    exit;
}


?>
<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Delete User Account</h1>
    <p class="subtitle">Are you sure you want to delete the user: <?= $user['full_name'] ?></p>
    <div class="buttons">
        <a href="?id=<?= $user['id'] ?>&confirm=yes" class="button is-success">Yes</a>
        <a href="?id=<?= $user['id'] ?>&confirm=no" class="button is-danger">No</a>
    </div>
</section>
<!-- END YOUR CONTENT -->


<?php include 'templates/footer.php'; ?>
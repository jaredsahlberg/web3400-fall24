<?php
// Step 1: Include config.php file
include 'config.php';
// Step 2: Only allow 'admin' users to access page
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect user to login page or display an error message
    $_SESSION['messages'][] = "You must be an administrator to access that resource.";
    header('Location: login.php');
    exit;
}
// Step 3:Use `register.php`    
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract user input, and assign data to variables
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt password
    $phone = htmlspecialchars($_POST['phone']);
    $role = htmlspecialchars($_POST['role']);

    // Check if the email is unique
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `email` = ?");
    $stmt->execute([$email]);
    $userExists = $stmt->fetch();

    if ($userExists) {
        // Email already exists, prompt the user to choose another
        $_SESSION['messages'][] = "That email already exists. Please choose another.";
        header('Location: user_add.php');
        exit;
    } else {
        // Email is unique, proceed with inserting the new user record
        $insertStmt = $pdo->prepare("INSERT INTO `users`(`full_name`, `email`, `pass_hash`, `phone`, `role`) VALUES (?, ?, ?, ?, ?)");
        $insertStmt->execute([$full_name, $email, $password, $phone, $role]);

        // Create successful user creation message
        $_SESSION['messages'][] = "The user account for $full_name was created. They will need to login to activate their account.";
        header('Location: users_manage.php');
        exit;
    }
}
?>
<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>



<!-- BEGIN YOUR CONTENT -->
<main class="container">
<section class="section">
    <h1 class="title">Add User</h1>
    <form action="user_add.php" method="post">
        <!-- Full Name -->


        <div class="field">
            <label class="label">Full Name</label>
            <div class="control">
                <input class="input" type="text" name="full_name" required>
            </div>
        </div>
        <!-- Email -->


        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" name="email" required>
            </div>
        </div>
        <!-- Password -->


        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" name="password" required>
            </div>
        </div>
        <!-- Phone -->


        <div class="field">
            <label class="label">Phone</label>
            <div class="control">
                <input class="input" type="tel" name="phone">
            </div>
        </div>
        <!-- Role -->


        <div class="field">
            <label class="label">Role</label>
            <div class="control">
                <div class="select">
                    <select name="role">
                        <option value="admin">Admin</option>
                        <option value="editor">Editor</option>
                        <option value="user" selected>User</option>
                    </select>
                </div>
            </div>
        </div>
        <!-- Submit -->


        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-link">Add User</button>
            </div>
            <div class="control">
                <a href="users_manage.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
</main>
<!-- END YOUR CONTENT -->

<?php include 'templates/footer.php'; ?>



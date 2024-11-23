<?php
// Include configuration and session start
include 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Buffer output to prevent headers already sent issues
ob_start();

// Secure and only allow logged-in admins
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['messages'][] = "You must be an administrator to access that resource.";
    header("Location: login.php");
    exit();
}

// Initialize message variable
$message = "";

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $full_name = htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8');
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT); 
    $phone = htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'); 
    $role = htmlspecialchars($_POST['role'] ?? 'user', ENT_QUOTES, 'UTF-8'); 

    // Check for required fields
    if (empty($full_name) || empty($email) || empty($password)) {
        $message = "Full Name, Email, and Password are required.";
    } else {
        try {
            // Insert the user into the database
            $stmt = $pdo->prepare('INSERT INTO users (full_name, email, pass_hash, phone, role, created_on) VALUES (:full_name, :email, :pass_hash, :phone, :role, NOW())');
            $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':pass_hash', $password, PDO::PARAM_STR); 
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $_SESSION['messages'][] = "User successfully added.";
                header("Location: users_manage.php");
                exit();
            } else {
                $message = "Error: Unable to add the user.";
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
            <h1 class="title">Add User</h1>

            <?php if (!empty($message)) : ?>
                <div class="notification is-danger">
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form action="user_add.php" method="post">
                <div class="field">
                    <label class="label">Full Name</label>
                    <div class="control">
                        <input class="input" type="text" name="full_name" placeholder="Enter full name" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Email</label>
                    <div class="control">
                        <input class="input" type="email" name="email" placeholder="Enter email address" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Password</label>
                    <div class="control">
                        <input class="input" type="password" name="password" placeholder="Enter password" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Phone</label>
                    <div class="control">
                        <input class="input" type="tel" name="phone" placeholder="Enter phone number">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Role</label>
                    <div class="control">
                        <div class="select">
                            <select name="role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field is-grouped">
                    <div class="control">
                        <button type="submit" class="button is-link">Add User</button>
                    </div>
                    <div class="control">
                        <a href="users_manage.php" class="button is-light">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <?php include 'templates/footer.php'; ?>
</body>
</html>


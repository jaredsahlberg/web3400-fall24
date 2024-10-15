<?php include 'config.php'; 

//Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //process form elements
    $email = $_POST['email'];
    $password = $_POST['password'];

    //check to see if the user exists in the database
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `email` = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    //vars used to check the activations status of the user account
    $activation_code =$user['activation_code'];
    $full_name = $user['full_name'];

    //set the activation status for the user
    $accountActivated = substr($activation_code, 0, 9) === 'activated' ? true : false;

    //if user account exists and is activated and the password is verified then log them in
    if ($user && $accountActivated && password_verify($password, $user['pass_hash'])) {

        //update the last_login dat/time stamp
        $updateStmt = $pdo->prepare("UPDATE `users` SET `last_login` = NOW() WHERE `id` = ?");
        $updateResults = $updateStmt->execute([$user['id']]);

        //set session vars for the user session
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['messages'][] = "Welcome back, $full_name";

        //redirect the user to the profile page or admin dashboard based on their role
        if ($user['role'] === 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: profile.php');
        }
        exit;

    } elseif ($user && !$accountActivated) {
        // Generate activation link. This is instead of sending a verification Email and or SMS message
        $activation_link = "register.php?code=$activation_code";

        // Create an activation link message
        $_SESSION['messages'][] = "Welcome $full_name. Your account has not been activated. To activate your account, <a href='$activation_link'>click here</a>.";
        
    } else {
        //user account does not exist or password is invalid
        $_SESSION['messages'][] = "Invalid email or password. Please try again.";
        header('Location: login.php');
        exit;
    } 
}

include 'templates/head.php';
include 'templates/nav.php';
?>

<!-- Begin Login Dropdown Button and Form -->
<div style="position: relative; display: inline-block; float: right; margin-right: 20px;">
    <!-- Trigger Button -->
    <button onclick="toggleDropdown()" style="background-color: #3273dc; color: white; border: none; padding: 10px 20px; cursor: pointer; font-weight: bold; border-radius: 5px;">
        Login
    </button>

    <!-- Dropdown Form (Hidden by default) -->
    <div id="dropdownForm" style="display: none; position: absolute; right: 0; background-color: white; border: 1px solid #ddd; padding: 20px; width: 250px; top: 100%; box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2); border-radius: 5px;">
        <form action="login.php" method="post">
            <!-- Email Field -->
            <div style="margin-bottom: 10px;">
                <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold;">Email:</label>
                <input type="email" id="email" name="email" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <!-- Password Field -->
            <div style="margin-bottom: 10px;">
                <label for="password" style="display: block; margin-bottom: 5px; font-weight: bold;">Password:</label>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <!-- Submit Button -->
            <div>
                <button type="submit" style="background-color: #3273dc; color: white; border: none; padding: 8px 15px; cursor: pointer; font-weight: bold; border-radius: 5px;">
                    Login
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for Toggling the Dropdown -->
<script>
function toggleDropdown() {
    const form = document.getElementById('dropdownForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

window.onclick = function(event) {
    const form = document.getElementById('dropdownForm');
    if (!event.target.matches('button') && !form.contains(event.target)) {
        form.style.display = 'none';
    }
}
</script>

<?php include 'templates/footer.php'; ?>

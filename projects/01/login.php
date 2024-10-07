<?php include 'config.php'; 

//Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //precess form elements
    $email = $_POST['email'];
    $password = $_POST['password'];

    //check to see if the user exists in the database
    $stmt = $pdo->prepare("SELECT * FROM 'users' WHERE 'email' = ?");
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
        $updateStmt = $pdo->prepare("UPDATE 'users' SET 'last_login' = NOW() WHERE 'id' = ?");
        $updateResults = $updateStmt->execute([$user['id']]);


    } else {
        # code...
    }    
}
?>
<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Login</h1>
    <form class="box" action="login.php" method="post">
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
        <!-- Submit Button -->
        <div class="field">
            <div class="control">
                <button type="submit" class="button is-link">Login</button>
            </div>
        </div>
    </form>
    <a href="register.php" class="is-link"><strong>Create a new user account</strong></a>
</section>
<!-- END YOUR CONTENT -->
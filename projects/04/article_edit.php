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
// Step 3: Check if the update form was submitted. If so, update article details using an UPDATE SQL query.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check that all form fields are set and not empty
    if (isset($_POST['id'])) {  
        // Sanitize and validate the data input
        $article_id = $_POST['id'];
        $title = htmlspecialchars($_POST['title']);
        $content = htmlspecialchars($_POST['content']);
        // Update article details using an UPDATE SQL query
        $sql = "UPDATE articles SET title=?, content=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $content, $article_id]);
        header('Location: articles.php');
        exit;
    } else {
        $_SESSION['messages'][] = "Please fill in all required fields.";
    }
}
// Step 4: Else it's an initial page request, fetch the article's current data from the database by preparing and executing a SQL statement that uses the article id from the query string (ex. $_GET['id'])
else {
    // Check for the article ID in the query string
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        // Sanitize input data
        $article_id = htmlspecialchars($_GET['id']);

        // Get article details by executing a SQL statement with article ID
        $sql = "SELECT * FROM articles WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$article_id]);

        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        // message for error "no article found"
        if (!$article) {
            $_SESSION['messages'][] = "Article not found.";
            header('Location: articles.php');
            exit;
        }
    } else {
        // message for error "article ID not provided"
        $_SESSION['messages'][] = "Article ID not provided.";
        header('Location: articles.php');
        exit;
    }
}
?>

<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

<!-- BEGIN YOUR CONTENT -->
<section class="section">
    <h1 class="title">Edit Article</h1>
    <form action="" method="post">
        <!-- ID -->
        <input type="hidden" name="id" value="<?= $article['id'] ?>">
        <!-- Title -->
        <div class="field">
            <label class="label">Title</label>
            <div class="control">
                <input class="input" type="text" name="title" value="<?= $article['title'] ?>" required>
            </div>
        </div>
        <!-- Content -->
        <div class="field">
            <label class="label">Content</label>
            <div class="control">
                <textarea class="textarea" id="content" name="content" required><?= $article['content'] ?></textarea>
            </div>
        </div>
        <!-- Submit -->
        <div class="field is-grouped">
            <div class="control">
                <button type="submit" class="button is-link">Update Article</button>
            </div>
            <div class="control">
                <a href="articles.php" class="button is-link is-light">Cancel</a>
            </div>
        </div>
    </form>
</section>
<!-- END YOUR CONTENT -->
<?php include 'templates/footer.php'; ?>
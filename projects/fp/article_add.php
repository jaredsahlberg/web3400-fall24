<?php
// Step 1: Include config.php file
include 'config.php';

// Step 2: Secure and only allow 'admin' users to access this page
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['messages'][] = "You must be an administrator to access that resource.";
    header('Location: login.php');
    exit;
}

// Step 3: Implement form handling logic to insert the new article into the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize input
        $title = htmlspecialchars($_POST['title']);
        $content = htmlspecialchars($_POST['content']);
        $author_id = $_SESSION['user_id'];

        // Set default values for is_featured and is_published
        $is_featured = 0; 
        $is_published = 0;

        // Prepare SQL INSERT statement
        $stmt = $pdo->prepare('INSERT INTO articles (title, content, author_id, is_featured, is_published, created_at) VALUES (?, ?, ?, ?, ?, NOW())');

        // Execute the statement
        if ($stmt->execute([$title, $content, $author_id, $is_featured, $is_published])) {
            $_SESSION['messages'][] = "The article was successfully added.";
            header('Location: articles.php');
            exit;
        }
    } catch (PDOException $e) {
        // Handle database errors
        $_SESSION['messages'][] = "An error occurred: " . $e->getMessage();
    }
}
?>

<?php include 'templates/head.php'; ?>
<?php include 'templates/nav.php'; ?>

<section class="section">
    <div class="container">
        <h1 class="title">Write an article</h1>
        <form action="article_add.php" method="post">
            <div class="field">
                <label class="label">Title</label>
                <div class="control">
                    <input class="input" type="text" name="title" placeholder="Enter article title" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Content</label>
                <div class="control">
                    <textarea class="textarea" name="content" placeholder="Enter article content" required></textarea>
                </div>
            </div>
            <div class="buttons">
                <button type="submit" class="button is-link">Add Post</button>
                <button type="reset" class="button is-light">Cancel</button>
            </div>
        </form>
    </div>
</section>

<?php include 'templates/footer.php'; ?>








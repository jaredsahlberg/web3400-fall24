<?php
// Include the configuration file and templates
include 'config.php';
include 'templates/head.php';
include 'templates/nav.php';

// Only allow 'admin' users to access this page
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['messages'][] = "You must be an administrator to access that resource.";
    header('Location: login.php');
    exit;
}

// Prepare the SQL query to select all posts from the database
$stmt = $pdo->prepare('SELECT articles.*, users.full_name AS author 
                       FROM articles 
                       JOIN users ON articles.author_id = users.id 
                       ORDER BY created_at DESC');
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if there are no articles
if (!$articles) {
    $_SESSION['messages'][] = "There are no articles in the database.";
}

// Toggle published status
if (isset($_GET['id']) && isset($_GET['is_published'])) {
    $articleId = $_GET['id'];
    $currentPublished = $_GET['is_published'] == '1' ? 1 : 0;
    $isPublished = $currentPublished == 1 ? 0 : 1;

    $updateStmt = $pdo->prepare('UPDATE articles SET is_published = :is_published WHERE id = :id');
    $updateStmt->execute(['is_published' => $isPublished, 'id' => $articleId]);

    $_SESSION['messages'][] = $isPublished ? "The article has been published." : "The article has been unpublished.";
    header("Location: articles.php");
    exit;
}

// Toggle featured status
if (isset($_GET['id']) && isset($_GET['is_featured'])) {
    $articleId = $_GET['id'];
    $currentFeatured = $_GET['is_featured'] == '1' ? 1 : 0;
    $isFeatured = $currentFeatured == 1 ? 0 : 1;

    $updateStmt = $pdo->prepare('UPDATE articles SET is_featured = :is_featured WHERE id = :id');
    $updateStmt->execute(['is_featured' => $isFeatured, 'id' => $articleId]);

    $_SESSION['messages'][] = $isFeatured ? "The article has been featured." : "The article has been unfeatured.";
    header("Location: articles.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/head.php'; ?>
</head>
<body>
    <?php include 'templates/nav.php'; ?>

    <!-- BEGIN CONTENT -->
    <section class="section">
        <h1 class="title">Articles</h1>
        <div class="buttons">
            <a href="article_add.php" class="button is-link">Write an article</a>
        </div>

        <table class="table is-bordered is-striped is-hoverable is-fullwidth">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Author</th>
                    <th><small>Featured | Published | Edit | Del</small></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?= $article['id'] ?></td>
                        <td>
                            <a href="article.php?id=<?= $article['id'] ?>">
                                <?= mb_substr($article['title'], 0, 30) . (mb_strlen($article['title']) > 30 ? '...' : '') ?>
                            </a>
                        </td>
                        <td><?= mb_substr($article['content'], 0, 50) . (mb_strlen($article['content']) > 50 ? '...' : '') ?></td>
                        <td><?= $article['author'] ?></td>
                        <td>
                            <!-- Feature Link -->
                            <?php if ($article['is_featured'] == 1): ?>
                                <a href="articles.php?id=<?= $article['id'] ?>&is_featured=1" class="button is-warning">
                                    <i class="fas fa-lg fa-check-circle"></i>
                                </a>
                            <?php else: ?>
                                <a href="articles.php?id=<?= $article['id'] ?>&is_featured=0" class="button is-warning is-light">
                                    <i class="fas fa-lg fa-times-circle"></i>
                                </a>
                            <?php endif; ?>

                            <!-- Publish Link -->
                            <?php if ($article['is_published'] == 1): ?>
                                <a href="articles.php?id=<?= $article['id'] ?>&is_published=1" class="button is-primary">
                                    <i class="fas fa-lg fa-check-circle"></i>
                                </a>
                            <?php else: ?>
                                <a href="articles.php?id=<?= $article['id'] ?>&is_published=0" class="button is-primary is-light">
                                    <i class="fas fa-lg fa-times-circle"></i>
                                </a>
                            <?php endif; ?>

                            <!-- Edit Post Link -->
                            <a href="article_edit.php?id=<?= $article['id'] ?>" class="button is-info">
                                <i class="fas fa-lg fa-edit"></i>
                            </a>

                            <!-- Delete Post Link -->
                            <a href="article_delete.php?id=<?= $article['id'] ?>" class="button is-danger">
                                <i class="fas fa-lg fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <!-- END CONTENT -->

    <?php include 'templates/footer.php'; ?>
</body>
</html>

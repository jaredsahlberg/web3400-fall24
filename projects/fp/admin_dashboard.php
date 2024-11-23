<?php
// Step 1: Include config.php file
include 'config.php';



// Step 2: Secure and only allow 'admin' users to access this page
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect user to login page or display an error message
    $_SESSION['messages'][] = "You must be an administrator to access this resource.";
    header('Location: login.php');
    exit();
}
include 'templates/head.php';
include 'templates/nav.php';

// Define KPI Queries
$kpiQueries = [
    'total_articles_count' => 'SELECT COUNT(*) FROM articles',
    'unpublished_articles_count' => 'SELECT COUNT(*) FROM articles WHERE is_published = 0',
    'published_articles_count' => 'SELECT COUNT(*) FROM articles WHERE is_published = 1',
    'featured_articles_count' => 'SELECT COUNT(*) FROM articles WHERE is_featured = 1',
    'total_user_interactions' => 'SELECT COUNT(*) FROM user_interactions',
    'average_likes_per_article' => 'SELECT ROUND(AVG(likes_count), 2) FROM articles',
    'average_favs_per_article' => 'SELECT ROUND(AVG(favs_count), 2) FROM articles',
    'average_comments_per_article' => 'SELECT ROUND(AVG(comments_count), 2) FROM articles',
    'total_tickets_count' => 'SELECT COUNT(*) FROM tickets',
    'open_tickets_count' => 'SELECT COUNT(*) FROM tickets WHERE status = "Open"',
    'in_progress_tickets_count' => 'SELECT COUNT(*) FROM tickets WHERE status = "In Progress"',
    'closed_tickets_count' => 'SELECT COUNT(*) FROM tickets WHERE status = "Closed"',
    'total_user_count' => 'SELECT COUNT(*) FROM users WHERE role = "user"',
    'most_active_user' => "SELECT CONCAT(users.full_name, ': ', COUNT(user_interactions.id), ' interactions') 
                           FROM users 
                           JOIN user_interactions ON users.id = user_interactions.user_id 
                           WHERE users.role = 'user' 
                           GROUP BY users.full_name 
                           ORDER BY COUNT(user_interactions.id) DESC 
                           LIMIT 1",
];

// Execute KPI Queries
$kpiResults = [];
foreach ($kpiQueries as $kpi => $query) {
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $kpiResults[$kpi] = $stmt->fetchColumn();
}

// Fetch recent contact messages
$contactQuery = 'SELECT * FROM contact_us ORDER BY submitted_at DESC LIMIT 5';
$contactStmt = $pdo->prepare($contactQuery);
$contactStmt->execute();
$contactMessages = $contactStmt->fetchAll(PDO::FETCH_ASSOC);
?>



<section class="section">
    <div class="container">
        <h1 class="title">Administrator Dashboard</h1>

        <!-- Count Boxes -->
        <div class="columns is-multiline">
            <!-- Articles KPI -->
            <div class="column is-one-quarter">
                <div class="box">
                    <div class="heading"><a href="articles.php">Articles</a></div>
                    <div class="title">Count: <?= $kpiResults['total_articles_count'] ?></div>
                    <div class="level">
                        <div class="level-item">
                            <div>
                                <div class="heading">Unpublished</div>
                                <div class="title is-5"><?= $kpiResults['unpublished_articles_count'] ?></div>
                            </div>
                        </div>
                        <div class="level-item">
                            <div>
                                <div class="heading">Published</div>
                                <div class="title is-5"><?= $kpiResults['published_articles_count'] ?></div>
                            </div>
                        </div>
                        <div class="level-item">
                            <div>
                                <div class="heading">Featured</div>
                                <div class="title is-5"><?= $kpiResults['featured_articles_count'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets KPI -->
            <div class="column is-one-quarter">
                <div class="box">
                    <div class="heading"><a href="tickets.php">Tickets</a></div>
                    <div class="title">Count: <?= $kpiResults['total_tickets_count'] ?></div>
                    <div class="level">
                        <div class="level-item">
                            <div>
                                <div class="heading">Open</div>
                                <div class="title is-5"><?= $kpiResults['open_tickets_count'] ?></div>
                            </div>
                        </div>
                        <div class="level-item">
                            <div>
                                <div class="heading">In Progress</div>
                                <div class="title is-5"><?= $kpiResults['in_progress_tickets_count'] ?></div>
                            </div>
                        </div>
                        <div class="level-item">
                            <div>
                                <div class="heading">Closed</div>
                                <div class="title is-5"><?= $kpiResults['closed_tickets_count'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users KPI -->
            <div class="column is-one-quarter">
                <div class="box">
                    <div class="heading"><a href="users_manage.php">Users</a></div>
                    <div class="title">Count: <?= $kpiResults['total_user_count'] ?></div>
                    <div class="level">
                        <div class="level-item">
                            <div>
                                <div class="heading">Most Active User</div>
                                <div class="title is-5"><?= $kpiResults['most_active_user'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Interactions by Average -->
            <div class="column is-one-quarter">
                <div class="box">
                    <div class="heading">User Interactions by Average</div>
                    <div class="title">Count: <?= $kpiResults['total_user_interactions'] ?></div>
                    <div class="level">
                        <div class="level-item">
                            <div>
                                <div class="heading">Likes</div>
                                <div class="title is-5"><?= $kpiResults['average_likes_per_article'] ?></div>
                            </div>
                        </div>
                        <div class="level-item">
                            <div>
                                <div class="heading">Favs</div>
                                <div class="title is-5"><?= $kpiResults['average_favs_per_article'] ?></div>
                            </div>
                        </div>
                        <div class="level-item">
                            <div>
                                <div class="heading">Comments</div>
                                <div class="title is-5"><?= $kpiResults['average_comments_per_article'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact Us Messages and Quick Add Forms -->
        <div class="columns is-desktop">
            <!-- Contact Us Messages -->
            <div class="column is-half">
                <div class="box" style="border: 1px solid #ccc; border-radius: 8px; overflow: hidden;">
                    <!-- Green Header -->
                    <div class="has-background-success has-text-white has-text-left has-text-weight-bold" style="padding: 10px; font-size: 18px;">
                        Contact us messages
                    </div>
                    <table class="table is-fullwidth is-bordered" style="margin: 0;">
                        <thead>
                            <tr>
                                <th style="border: 1px solid #ddd;">Name</th>
                                <th style="border: 1px solid #ddd;">Email</th>
                                <th style="border: 1px solid #ddd;">Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contactMessages as $message) : ?>
                                <tr>
                                    <td style="border: 1px solid #ddd; white-space: pre-wrap;"><?= htmlspecialchars($message['name']) ?></td>
                                    <td style="border: 1px solid #ddd;"><?= htmlspecialchars($message['email']) ?></td>
                                    <td style="border: 1px solid #ddd;"><?= htmlspecialchars($message['message']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Full-Width Button -->
                    <a href="contact_messages.php" class="button is-fullwidth" style="background-color: white; color: #0056b3; border: 1px solid #0056b3; text-align: center; font-weight: normal;">
                        View all messages
                    </a>
                </div>
            </div>

            <!-- Article Quick Add -->
            <div class="column is-half">
                <div class="box">
                <p class="panel-heading" style="background-color: #007bff; color: white; padding: 10px; border-radius: 4px;">Article - Quick Add</p>
                    <form action="article_add.php" method="post">
                        <div class="field" style="width: 50%; display: inline-block;">
                            <label class="label">Title</label>
                            <div class="control">
                                <input class="input" type="text" name="title" required>
                            </div>
                        </div>
                        <div class="field" style="width: 50%; display: inline-block;">
                            <label class="label">Content</label>
                            <div class="control">
                                <textarea class="textarea" name="content" required></textarea>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="button is-link">Add Post</button>
                            <button type="reset" class="button" style="background-color: #e9ecef; color: #004085; border: 1px solid #ced4da;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="columns">
            <!-- Ticket Quick Add -->
            <div class="column is-half">
                <div class="box">
                <p class="panel-heading" style="background-color: #20c997; color: white; padding: 10px; border-radius: 4px;">Ticket - Quick Add</p>
                    <form action="ticket_create.php" method="post">
                    <div class="field" style="width: 50%; display: inline-block;">
                        <label class="label">Title</label>
                        <div class="control">
                            <input class="input" type="text" name="title" placeholder="Ticket title" required>
                        </div>
                    </div>

                    <div class="field" style="width: 50%; display: inline-block;">
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
                        <div class="form-footer">
                            <button type="submit" class="button is-link">Create Ticket</button>
                            <button type="reset" class="button" style="background-color: #e9ecef; color: #004085; border: 1px solid #ced4da;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Quick Add -->
            <div class="column is-half">
                <div class="box">
                <p class="panel-heading" style="background-color: #e9ecef; color: black; padding: 10px; border-radius: 4px; border: 1px solid #ced4da;">Users - Quick Add</p>
                    <form action="user_add.php" method="post">
                        <div class="field" style="width: 50%; display: inline-block;">
                            <label class="label">Full Name</label>
                            <div class="control">
                                <input class="input" type="text" name="full_name" >
                            </div>
                        </div>
                        <div class="field" style="width: 50%; display: inline-block;">
                            <label class="label">Email</label>
                            <div class="control">
                                <input class="input" type="email" name="email" >
                            </div>
                        </div>
                        <div class="field" style="width: 50%; display: inline-block;">
                            <label class="label">Password</label>
                            <div class="control">
                                <input class="input" type="password" name="password" >
                            </div>
                        </div>
                        <div class="field" style="width: 50%; display: inline-block;">
                            <label class="label">Phone</label>
                            <div class="control">
                                <input class="input" type="tel" name="phone" >
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Role</label>
                            <div class="control">
                                <div class="select">
                                    <select name="role">
                                        <option value="User">User</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="button is-link">Add User</button>
                            <button type="reset" class="button" style="background-color: #e9ecef; color: #004085; border: 1px solid #ced4da;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'templates/footer.php'; ?>









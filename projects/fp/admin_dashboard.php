<?php
include 'config.php';
include 'templates/head.php';
include 'templates/nav.php';

// Secure the page and allow only admin users
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['messages'][] = "You must be an administrator to access this resource.";
    header('Location: login.php');
    exit();
}

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

<style>
    .box {
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .panel-heading {
        background-color: #38a169;
        color: white;
        font-weight: bold;
        padding: 10px;
        border-radius: 8px 8px 0 0;
    }

    .button.is-link {
        margin-right: 10px;
    }

    .form-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }
</style>

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
                                <div class="heading">Favorites</div>
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
        <div class="columns">
            <!-- Contact Us Messages -->
            <div class="column is-half">
                <div class="box">
                    <p class="panel-heading">Contact Us Messages</p>
                    <table class="table is-fullwidth">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contactMessages as $message) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($message['name']) ?></td>
                                    <td><?= htmlspecialchars($message['email']) ?></td>
                                    <td><?= htmlspecialchars($message['message']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <a href="contact_messages.php" class="button is-link">View all messages</a>
                </div>
            </div>

            <!-- Article Quick Add -->
            <div class="column is-half">
                <div class="box">
                    <p class="panel-heading">Article - Quick Add</p>
                    <form action="article_add.php" method="post">
                        <div class="field">
                            <label class="label">Title</label>
                            <div class="control">
                                <input class="input" type="text" name="title" required>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Content</label>
                            <div class="control">
                                <textarea class="textarea" name="content" required></textarea>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="button is-link">Add Post</button>
                            <button type="reset" class="button">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="columns">
            <!-- Ticket Quick Add -->
            <div class="column is-half">
                <div class="box">
                    <p class="panel-heading">Ticket - Quick Add</p>
                    <form action="ticket_create.php" method="post">
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
                                        <option value="Low">Low</option>
                                        <option value="Medium">Medium</option>
                                        <option value="High">High</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="button is-link">Create Ticket</button>
                            <button type="reset" class="button">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Quick Add -->
            <div class="column is-half">
                <div class="box">
                    <p class="panel-heading">Users - Quick Add</p>
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
                                        <option value="User">User</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="button is-link">Add User</button>
                            <button type="reset" class="button is-light">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'templates/footer.php'; ?>





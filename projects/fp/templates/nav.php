<header class="container">
    <!-- BEGIN MAIN NAV -->
    <nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="index.php">
                <span class="icon-text">
                    <span class="icon">
                        <i class="fas fa-yin-yang fa-lg"></i>
                    </span>
                    <span>&nbsp;<?= $siteName ?></span>
                </span>
            </a>
            <a class="navbar-burger" aria-label="menu" aria-expanded="false">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>
        <div class="navbar-menu">
            <div class="navbar-start">
                <!-- BEGIN ADMIN MENU -->
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['user_role'] === 'admin') : ?>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link <?= $_SERVER['PHP_SELF'] === '/admin_dashboard.php' ? 'is-active' : '' ?>">
                            <span class="icon">
                                <i class="fas fa-user-cog"></i>
                            </span>
                            <span>Admin</span>
                        </a>
                        <div class="navbar-dropdown">
                            <a href="admin_dashboard.php" class="navbar-item <?= $_SERVER['PHP_SELF'] === '/admin_dashboard.php' ? 'is-active' : '' ?>">
                                Dashboard
                            </a>
                            <a href="users_manage.php" class="navbar-item <?= $_SERVER['PHP_SELF'] === '/users_manage.php' ? 'is-active' : '' ?>">
                                Manage Users
                            </a>
                            <a href="articles.php" class="navbar-item <?= $_SERVER['PHP_SELF'] === '/articles.php' ? 'is-active' : '' ?>">
                                Manage Articles
                            </a>
                            <a href="tickets.php" class="navbar-item <?= $_SERVER['PHP_SELF'] === '/tickets.php' ? 'is-active' : '' ?>">
                                Manage Tickets
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- END ADMIN MENU -->
            </div>
            <div class="navbar-end mr-5">
                <div class="navbar-item">
                    <div class="buttons">
                        <a class="button is-primary <?= $_SERVER['PHP_SELF'] === '/contact.php' ? 'is-active' : '' ?>" href="contact.php">Contact Us</a>
                        
                        <!-- Support button for logged-in users -->
                        <?php if (isset($_SESSION['loggedin'])) : ?>
                            <a href="ticket_create.php" class="button is-light <?= $_SERVER['PHP_SELF'] === '/ticket_create.php' ? 'is-active' : '' ?>">
                                <strong>Support</strong>
                            </a>
                        <?php endif; ?>

                        <!-- BEGIN USER MENU -->
                        <?php if (isset($_SESSION['loggedin'])) : ?>
                            <div class="navbar-item has-dropdown is-hoverable">
                                <a class="button navbar-link">
                                    <span class="icon">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </a>
                                <div class="navbar-dropdown">
                                    <a href="profile.php" class="navbar-item <?= $_SERVER['PHP_SELF'] === '/profile.php' ? 'is-active' : '' ?>">Profile</a>
                                    <hr class="navbar-divider">
                                    <a href="logout.php" class="navbar-item">Logout</a>
                                </div>
                            </div>
                        <?php else : ?>
                            <a href="login.php" class="button is-link <?= $_SERVER['PHP_SELF'] === '/login.php' ? 'is-active' : '' ?>">Login</a>
                        <?php endif; ?>
                        <!-- END USER MENU -->
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- END MAIN NAV -->

    <!-- Optional spacing for styling -->
    <section class="block">&nbsp;</section>
    <section class="block">&nbsp;</section>

    <!-- Hero Section for Index Page -->
    <?php if ($_SERVER['PHP_SELF'] === '/index.php') : ?>
        <section class="hero is-info">
            <div class="hero-body">
                <p class="title">
                    "Dolphins are more than just mammals"
                </p>
                <p class="subtitle">
                    They are a southern Florida mascot...
                </p>
                <a href="contact.php" class="button is-medium is-info is-light is-rounded <?= $_SERVER['PHP_SELF'] === '/contact.php' ? 'is-active' : '' ?>">
                    <span class="icon is-large">
                        <i class="fab fa-2x fa-pagelines"></i>
                    </span>
                    <span>DolFans are the best!</span>
                </a>
            </div>
        </section>
    <?php endif; ?>

    <!-- Notifications Section -->
    <?php if (!empty($_SESSION['messages'])) : ?>
        <section class="notification is-warning">
            <button class="delete"></button>
            <?php foreach ($_SESSION['messages'] as $message) : ?>
                <p><?= htmlspecialchars($message, ENT_QUOTES) ?></p>
            <?php endforeach; ?>
            <?php $_SESSION['messages'] = []; // Clear the messages ?>
        </section>
    <?php endif; ?>
</header>
<!-- END PAGE HEADER -->




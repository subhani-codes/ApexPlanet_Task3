<nav class="navbar">
    <div class="navbar-brand">
        <a href="/myProjectOfApexPlanet/">
            <div class="brand-logo">TF</div>
            <div class="brand-name">Task<span>Flow</span></div>
        </a>
    </div>

    <!-- CENTRAL SEARCH BAR SUBMISSION SEGMENT SYSTEM (Visible if user session is valid) -->
    <?php if(isset($_SESSION['user_id'])): ?>
    <div class="navbar-search-wrapper">
        <form method="GET" action="/myProjectOfApexPlanet/todos/view.php" class="nav-search-form">
            <span class="search-icon-symbol">🔍</span>
            <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Search system tasks...">
            <?php if(!empty($_GET['search'])): ?>
                <a href="/myProjectOfApexPlanet/todos/view.php" class="clear-search-btn">✕</a>
            <?php endif; ?>
        </form>
    </div>
    <?php endif; ?>

    <div class="navbar-menu">
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="/myProjectOfApexPlanet/todos/view.php" class="nav-link-item">Dashboard</a>
            <a href="/myProjectOfApexPlanet/todos/create.php" class="nav-link-item">Add task</a>
            <!-- Logout link safely separated on leftmost corner layouts -->
            <a href="/myProjectOfApexPlanet/logout.php" class="btn btn-red btn-sm logout-nav-btn">Logout</a>
        <?php else: ?>
            <a href="/myProjectOfApexPlanet/login.php">Login</a>
            <a href="/myProjectOfApexPlanet/register.php">Register</a>
        <?php endif; ?>
        
        <button class="dark-toggle" onclick="toggleDarkMode()" id="darkToggleBtn">Dark</button>
    </div>
</nav>

<!-- Reusable Mobile Layout Bottom Bar Navigation Component -->
<?php if(isset($_SESSION['user_id'])): ?>
<nav class="bottom-nav">
    <a href="/myProjectOfApexPlanet/todos/view.php" class="bnav-item active"><span>Home</span></a>
    <a href="/myProjectOfApexPlanet/todos/create.php" class="bnav-item"><span>Add</span></a>
    <a href="/myProjectOfApexPlanet/logout.php" class="bnav-item"><span>Logout</span></a>
</nav>
<?php endif; ?>
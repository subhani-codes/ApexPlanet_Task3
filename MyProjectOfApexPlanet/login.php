<?php
require 'db.php';
require 'includes/header.php';
require 'includes/navbar.php';

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if(empty($username) || empty($password)){
        $error = "All fields are required!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if($user && password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: todos/view.php");
            exit();
        } else {
            $error = "Invalid username or password!";
        }
    }
}
?>
<div class="auth-container">
    <div class="auth-box">
        <div class="auth-logo">TF</div>
        <h2>Welcome back</h2>
        <p class="auth-sub">Sign in to manage your tasks</p>

        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your username" required/>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required/>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Sign in</button>
        </form>

        <div class="auth-divider"></div>
        <p style="text-align:center;font-size:13px;color:var(--text2)">
            Don't have an account?
            <a href="register.php" style="color:var(--primary);font-weight:600;text-decoration:none">Register here</a>
        </p>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
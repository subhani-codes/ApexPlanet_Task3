<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
require '../db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: /myProjectOfApexPlanet/login.php");
    exit();
}

require '../db.php';

// Route Protection
// Always protect pages that need login!
if(!isset($_SESSION['user_id'])){
    header("Location: /myProjectOfApexPlanet/login.php");
    exit();
}

$error = "";

// POST → VALIDATE → SAVE → REDIRECT pattern!
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // trim() removes extra spaces
    // Why? User might type "  hello  " accidentally
    // trim() makes it "hello" → clean data!
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    // VALIDATE → Check if data is correct
    if(empty($title)){
        $error = "Title is required!";
    } elseif(strlen($title) < 3){
        $error = "Title must be at least 3 characters!";
    } else {
        // SAVE → Insert into database
        // Notice ? placeholders → prevents SQL injection!
        $stmt = $pdo->prepare("INSERT INTO todos 
                               (user_id, title, description, status) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $description,
            $status
        ]);

        // REDIRECT → Go back to view page
        // Why redirect? → Prevents duplicate form submission!
        // This is called PRG Pattern → Post Redirect Get!
        header("Location: view.php?success=Task added successfully!");
        exit();
    }
}
?>

<?php require '../includes/header.php'; ?>
<?php require '../includes/navbar.php'; ?>

<div class="container">
    <div class="card">
        <h2 style="margin-bottom:20px">➕ Add New Task</h2>

        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Task Title *</label>
                <input type="text" 
                       name="title" 
                       placeholder="Enter task title"
                       required/>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" 
                          placeholder="Enter task description"
                          rows="4"></textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <!-- Dropdown → same as select in HTML -->
                <select name="status">
                    <option value="pending">⏳ Pending</option>
                    <option value="completed">✅ Completed</option>
                </select>
            </div>

            <div style="display:flex; gap:10px">
                <button type="submit" class="btn btn-success">
                    ➕ Add Task
                </button>
                <a href="view.php" class="btn btn-danger">
                    ❌ Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
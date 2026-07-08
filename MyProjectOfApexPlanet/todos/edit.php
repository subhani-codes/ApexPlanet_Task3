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

if(!isset($_SESSION['user_id'])){
    header("Location: /myProjectOfApexPlanet/login.php");
    exit();
}

$error = "";

// GET the todo ID from URL
// Remember: edit.php?id=5
// $_GET['id'] gives us 5!
$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch existing todo data
// Why? → To pre-fill the form with current values!
$stmt = $pdo->prepare("SELECT * FROM todos 
                        WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$todo = $stmt->fetch();

// If todo not found → redirect
// Security: User cannot edit other users' todos!
if(!$todo){
    header("Location: view.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    if(empty($title)){
        $error = "Title is required!";
    } else {
        // UPDATE query → modifies existing record
        // Compare with INSERT → INSERT adds new record
        // UPDATE modifies existing record!
        $stmt = $pdo->prepare("UPDATE todos 
                               SET title=?, description=?, status=? 
                               WHERE id=? AND user_id=?");
        $stmt->execute([
            $title,
            $description,
            $status,
            $id,
            $user_id
        ]);

        header("Location: view.php?success=Task updated successfully!");
        exit();
    }
}
?>

<?php require '../includes/header.php'; ?>
<?php require '../includes/navbar.php'; ?>

<div class="container">
    <div class="card">
        <h2 style="margin-bottom:20px">✏️ Edit Task</h2>

        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Task Title *</label>
                <!-- value="..." pre-fills form with existing data! -->
                <input type="text" 
                       name="title" 
                       value="<?php echo htmlspecialchars($todo['title']); ?>"
                       required/>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4">
                    <?php echo htmlspecialchars($todo['description']); ?>
                </textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="pending" 
                        <?php echo $todo['status']=='pending' ? 'selected' : ''; ?>>
                        ⏳ Pending
                    </option>
                    <option value="completed" 
                        <?php echo $todo['status']=='completed' ? 'selected' : ''; ?>>
                        ✅ Completed
                    </option>
                </select>
            </div>

            <div style="display:flex; gap:10px">
                <button type="submit" class="btn btn-primary">
                    💾 Update Task
                </button>
                <a href="view.php" class="btn btn-danger">
                    ❌ Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
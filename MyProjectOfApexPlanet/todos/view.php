<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require '../db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: /myProjectOfApexPlanet/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination Configuration
$limit = 5; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { $page = 1; }
$offset = ($page - 1) * $limit;

// Gather Unfiltered Statistics 
$statsStmt = $pdo->prepare("SELECT * FROM todos WHERE user_id = ?");
$statsStmt->execute([$user_id]);
$allUserTodos = $statsStmt->fetchAll();

$total = count($allUserTodos);
$completed = count(array_filter($allUserTodos, fn($t) => $t['status'] == 'completed'));
$pending = $total - $completed;
$percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

// Execute Paginated Fetch Query Securely
if ($search !== '') {
    $countSql = "SELECT COUNT(*) FROM todos WHERE user_id = ? AND (title LIKE ? OR description LIKE ?)";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute([$user_id, "%$search%", "%$search%"]);
    $totalRecords = $countStmt->fetchColumn();

    $querySql = "SELECT * FROM todos WHERE user_id = ? AND (title LIKE ? OR description LIKE ?) ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($querySql);
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(3, "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(4, $limit, PDO::PARAM_INT);
    $stmt->bindValue(5, $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {
    $countSql = "SELECT COUNT(*) FROM todos WHERE user_id = ?";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute([$user_id]);
    $totalRecords = $countStmt->fetchColumn();

    $querySql = "SELECT * FROM todos WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($querySql);
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
}

$todos = $stmt->fetchAll();
$totalPages = ceil($totalRecords / $limit);

require '../includes/header.php';
require '../includes/navbar.php';
?>

<div class="hero">
    <h1>Hey, <span><?php echo htmlspecialchars($_SESSION['username']); ?></span> 👋</h1>
    <p id="motivationalQuote" class="hero-quote">Loading your focus mantra...</p>
    <p class="hero-status">Remaining Tasks: <strong><?php echo $pending; ?></strong> operational items.</p>
    <div class="hero-btns">
        <a href="create.php" class="btn btn-primary">+ Add New Task</a>
    </div>
</div>

<div class="container">
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-layout">
        
        <div class="sidebar-right">
            <div class="stats-sidebar-grid">
                <div class="stat-card total-card">
                    <div class="stat-num"><?php echo $total; ?></div>
                    <div class="stat-label">Total Volume</div>
                </div>
                <div class="stat-card done-card">
                    <div class="stat-num"><?php echo $completed; ?></div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-card pending-card">
                    <div class="stat-num"><?php echo $pending; ?></div>
                    <div class="stat-label">Pending Status</div>
                </div>
            </div>

            <div class="progress-wrap sidebar-progress-card">
                <div class="progress-top">
                    <span>Target Progression</span>
                    <span><?php echo $percentage; ?>% Complete</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width:<?php echo $percentage; ?>%"></div>
                </div>
            </div>
        </div>

        <div class="main-content-left">
            <div class="section-header">
                <div class="section-title">My Tasks</div>
                <a href="create.php" class="btn btn-primary btn-sm">+ Add</a>
            </div>

            <?php if(count($todos) > 0): ?>
                <?php foreach($todos as $todo): ?>
                <div class="todo-item <?php echo $todo['status']=='completed' ? 'completed' : ''; ?>">
                    <div style="flex:1">
                        <div class="todo-title <?php echo $todo['status']=='completed' ? 'done' : ''; ?>">
                            <?php echo htmlspecialchars($todo['title']); ?>
                        </div>
                        <?php if($todo['description']): ?>
                        <div class="todo-meta">
                            <?php echo htmlspecialchars($todo['description']); ?>
                        </div>
                        <?php endif; ?>
                        <div class="todo-bottom">
                            <span class="badge badge-<?php echo $todo['status']; ?>">
                                <?php echo $todo['status'] == 'completed' ? 'Completed' : 'Pending'; ?>
                            </span>
                            <div style="display:flex;gap:4px">
                                <a href="edit.php?id=<?php echo $todo['id']; ?>" class="btn btn-amber btn-sm">Edit</a>
                                <a href="/myProjectOfApexPlanet/todos/delete.php?id=<?php echo $todo['id']; ?>" class="btn btn-red btn-sm" onclick="return confirmDelete()">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if($totalPages > 1): ?>
                <div class="pagination-container">
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <?php 
                            $urlParams = ['page' => $i];
                            if($search !== '') { $urlParams['search'] = $search; }
                            $queryString = http_build_query($urlParams);
                        ?>
                        <a href="view.php?<?php echo $queryString; ?>" class="btn <?php echo $page === $i ? 'btn-primary' : 'btn-outline'; ?> btn-sm">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="empty-state">
                    <h3>No tasks found!</h3>
                    <p><?php echo $search !== '' ? 'No entries match your search query.' : 'Get started by creating your first milestone entry.'; ?></p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require '../includes/footer.php'; ?>
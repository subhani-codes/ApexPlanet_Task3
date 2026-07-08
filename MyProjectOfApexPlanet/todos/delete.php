<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
require '../db.php';

// Check if logged in
if(!isset($_SESSION['user_id'])){
    header("Location: /myProjectOfApexPlanet/login.php");
    exit();
}

// Check if ID exists in URL
if(!isset($_GET['id'])){
    header("Location: /myProjectOfApexPlanet/todos/view.php");
    exit();
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Delete todo
$stmt = $pdo->prepare("DELETE FROM todos WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);

// Redirect back to view page
header("Location: /myProjectOfApexPlanet/todos/view.php?success=Task deleted successfully!");
exit();
?>
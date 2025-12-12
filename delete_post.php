<?php
include "functions.php";
requireLogin();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$post_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Fetch post
$stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
stmt->bind_param("i", $post_id);
stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    $_SESSION['error'] = "Post not found!";
    header("Location: index.php");
    exit;
}

// Permission check (owner OR admin)
if ($user_id != $post['user_id'] && $user_role !== 'admin') {
    $_SESSION['error'] = "You don't have permission to delete this post!";
    header("Location: index.php");
    exit;
}

// Delete image if exists
if (!empty($post['image']) && file_exists("uploads/" . $post['image'])) {
    unlink("uploads/" . $post['image']);
}

// Delete post
$stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();

$_SESSION['success'] = "Post deleted successfully!";
header("Location: index.php");
exit;
?>

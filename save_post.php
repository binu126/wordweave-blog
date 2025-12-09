<?php
include "functions.php";
requireLogin();

$user_id = $_SESSION['user_id'];

$title = trim($_POST['title']);
$content = trim($_POST['content']);
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validate required fields
if (empty($title) || empty($content)) {
    setFlash("error", "Title and content cannot be empty.");
    redirect("editor.php" . ($id ? "?id=$id" : ""));
}

// ===============================
// IF EDITING EXISTING POST
// ===============================
if ($id > 0) {

    // Verify post belongs to logged in user
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $post = $stmt->get_result()->fetch_assoc();

    if (!$post || $post['user_id'] != $user_id) {
        die("Unauthorized access.");
    }

    // Handle optional image upload
    $newImage = uploadImage($_FILES['image']);

    if ($newImage) {
        // delete old file if exists
        if (!empty($post['image']) && file_exists("uploads/" . $post['image'])) {
            unlink("uploads/" . $post['image']);
        }

        $stmt = $conn->prepare("UPDATE blog_posts SET title=?, content=?, image=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("sssi", $title, $content, $newImage, $id);
    } else {
        // no new image
        $stmt = $conn->prepare("UPDATE blog_posts SET title=?, content=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("ssi", $title, $content, $id);
    }

    $stmt->execute();
    redirect("view.php?id=$id");
    exit;
}

// ===============================
// INSERT NEW POST
// ===============================
$newImage = uploadImage($_FILES['image']);

$stmt = $conn->prepare("INSERT INTO blog_posts (user_id, title, content, image) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $user_id, $title, $content, $newImage);
$stmt->execute();

$newId = $conn->insert_id;

// redirect to new post
redirect("view.php?id=$newId");
exit;

<?php
include "functions.php";
requireLogin();

$user_id = $_SESSION['user_id'];

$title = trim($_POST['title']);
$content = trim($_POST['content']);
$category = isset($_POST['category']) ? trim($_POST['category']) : "";
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Validate required fields
if (empty($title) || empty($content)) {
    setFlash("error", "Title, content, and category cannot be empty.");
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

        // Delete old image
        if (!empty($post['image']) && file_exists("uploads/" . $post['image'])) {
            unlink("uploads/" . $post['image']);
        }

        $stmt = $conn->prepare("UPDATE blog_posts 
            SET title=?, content=?, category=?, image=?, updated_at=NOW() 
            WHERE id=?");
        $stmt->bind_param("ssssi", $title, $content, $category, $newImage, $id);

    } else {

        $stmt = $conn->prepare("UPDATE blog_posts 
            SET title=?, content=?, category=?, updated_at=NOW() 
            WHERE id=?");
        $stmt->bind_param("sssi", $title, $content, $category, $id);
    }

    $stmt->execute();
    redirect("view.php?id=$id");
    exit;
}

// ===============================
// INSERT NEW POST
// ===============================
$newImage = uploadImage($_FILES['image']);

$stmt = $conn->prepare("INSERT INTO blog_posts (user_id, title, content, category, image) 
                        VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $title, $content, $category, $newImage);
$stmt->execute();

$newId = $conn->insert_id;

// Redirect to new post
redirect("view.php?id=$newId");
exit;

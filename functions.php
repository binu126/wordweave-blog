<?php
session_start();
include "db.php"; // Database connection

/* ============================================================
   AUTHENTICATION HELPERS
   ============================================================ */

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect("login.php");
    }
}

function getUser($conn, $user_id) {
    // FIXED TABLE NAME → users
    $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/* ============================================================
   SECURITY HELPERS
   ============================================================ */

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/* ============================================================
   FLASH MESSAGE SYSTEM
   ============================================================ */

function setFlash($key, $message) {
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][$key] = $message;
}

function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]); // remove after reading
        return $msg;
    }
    return null;
}

/* ============================================================
   REDIRECTION HELPER
   ============================================================ */

function redirect($url) {
    header("Location: $url");
    exit();
}

/* ============================================================
   FILE UPLOAD HELPER
   ============================================================ */

function uploadImage($file, $uploadDir = "uploads/") {

    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Allowed extensions
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return false; // Invalid format
    }

    // Validate MIME types
    $mime = mime_content_type($file['tmp_name']);
    $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    if (!in_array($mime, $allowedMime)) {
        return false;
    }

    // Ensure directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Unique name
    $newName = uniqid("img_", true) . "." . $ext;
    $target = $uploadDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $newName;
    }

    return null;
}

/* ============================================================
   DATE FORMATTER
   ============================================================ */

function formatDate($datetime, $format = "F j, Y g:i A") {
    if (!$datetime) return "Unknown date";

    $timestamp = strtotime($datetime);

    if ($timestamp === false) {
        return "Invalid date";
    }

    return date($format, $timestamp);
}

/* ============================================================
   JSON RESPONSE HELPER (Useful for AJAX features)
   ============================================================ */

function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header("Content-Type: application/json");
    echo json_encode($data);
    exit;
}

?>

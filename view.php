<?php
include "functions.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

// Fetch the blog post
$stmt = $conn->prepare("
    SELECT b.*, u.username 
    FROM blog_posts b
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$blog = $stmt->get_result()->fetch_assoc();

if (!$blog) {
    header("Location: index.php");
    exit;
}

// Get like count
$likeStmt = $conn->prepare("SELECT COUNT(*) AS total FROM likes WHERE post_id=?");
$likeStmt->bind_param("i", $id);
$likeStmt->execute();
$likeCount = $likeStmt->get_result()->fetch_assoc()['total'];

// Check if current user has liked this post
$userHasLiked = false;
if (isset($_SESSION['user_id'])) {
    $checkLike = $conn->prepare("SELECT id FROM likes WHERE post_id=? AND user_id=?");
    $checkLike->bind_param("ii", $id, $_SESSION['user_id']);
    $checkLike->execute();
    $userHasLiked = $checkLike->get_result()->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($blog['title']); ?> - Wordweave</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f5f7ff]">

<?php include "header.php"; ?>

<main class="py-10 px-6">
    <article class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-lg">
        
        <!-- Category Badge -->
        <?php if (!empty($blog['category'])): ?>
            <span class="inline-block px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full mb-4">
                🔖 <?php echo e($blog['category']); ?>
            </span>
        <?php endif; ?>

        <!-- Blog Header -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">
            <?php echo e($blog['title']); ?>
        </h1>

        <!-- Meta Information -->
        <div class="flex items-center gap-4 text-gray-600 mb-8 pb-6 border-b border-gray-200">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-blue-600 text-white flex items-center justify-center rounded-full text-sm font-bold">
                    <?= strtoupper($blog['username'][0]); ?>
                </div>
                <div>
                    <p class="font-semibold text-gray-800"><?php echo e($blog['username']); ?></p>
                    <p class="text-sm text-gray-500"><?php echo date('F j, Y', strtotime($blog['created_at'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Blog Image -->
        <?php if (!empty($blog['image'])): ?>
            <div class="mb-8">
                <img src="uploads/<?php echo e($blog['image']); ?>" 
                     class="w-full rounded-lg shadow-md"
                     alt="<?php echo e($blog['title']); ?>">
            </div>
        <?php endif; ?>

        <!-- Blog Content -->
        <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed mb-10">
            <?php echo nl2br(e($blog['content'])); ?>
        </div>

        <!-- Like Section -->
        <div class="border-t border-gray-200 pt-6 mt-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" action="like.php">
                            <input type="hidden" name="post_id" value="<?php echo $blog['id']; ?>">
                            <button class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition <?php echo $userHasLiked ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                                <span class="text-xl"><?php echo $userHasLiked ? '❤️' : '🤍'; ?></span>
                                <?php echo $userHasLiked ? 'Liked' : 'Like'; ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition">
                            <span class="text-xl">🤍</span>
                            Like
                        </a>
                    <?php endif; ?>
                    
                    <span class="text-gray-600 font-medium">
                        ❤️ <?php echo $likeCount; ?> <?php echo $likeCount == 1 ? 'like' : 'likes'; ?>
                    </span>
                </div>

                <!-- Edit/Delete buttons for post owner -->
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $blog['user_id']): ?>
                    <div class="flex gap-3">
                        <a href="editor.php?id=<?php echo $blog['id']; ?>" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                            ✏️ Edit
                        </a>
                        <form method="POST" action="delete_post.php" onsubmit="return confirm('Are you sure you want to delete this post?');">
                            <input type="hidden" name="post_id" value="<?php echo $blog['id']; ?>">
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">
                                🗑️ Delete
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Back to Blog -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <a href="index.php" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium transition">
                ← Back to all articles
            </a>
        </div>

    </article>
</main>

<!-- FOOTER -->
<footer class="bg-gradient-to-r from-[#0a1a3a] via-[#0d2b5a] to-[#1e3a8a] text-white mt-20 shadow-inner">
    <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
        <!-- Logo -->
        <div class="text-center md:text-left">
            <h2 class="text-3xl font-bold bg-gradient-to-r from-white via-yellow-300 to-yellow-500 bg-clip-text text-transparent">
                Wordweave
            </h2>
            <p class="text-gray-200 mt-2 text-sm">
                Create. Inspire. Share your story with the world.
            </p>
        </div>
        <!-- Links -->
        <div class="flex space-x-6 text-gray-200 text-sm">
            <a href="index.php" class="hover:text-yellow-300 transition">Home</a>
            <a href="about.php" class="hover:text-yellow-300 transition">About</a>
        </div>
    </div>
    <div class="border-t border-white/20 py-4 text-center">
        <p class="text-gray-200 text-sm">
            © <?php echo date('Y'); ?> Wordweave. All rights reserved.
        </p>
    </div>
</footer>

</body>
</html><?php
include "functions.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

// Fetch the blog post
$stmt = $conn->prepare("
    SELECT b.*, u.username 
    FROM blog_posts b
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$blog = $stmt->get_result()->fetch_assoc();

if (!$blog) {
    header("Location: index.php");
    exit;
}

// Get like count
$likeStmt = $conn->prepare("SELECT COUNT(*) AS total FROM likes WHERE post_id=?");
$likeStmt->bind_param("i", $id);
$likeStmt->execute();
$likeCount = $likeStmt->get_result()->fetch_assoc()['total'];

// Check if current user has liked this post
$userHasLiked = false;
if (isset($_SESSION['user_id'])) {
    $checkLike = $conn->prepare("SELECT id FROM likes WHERE post_id=? AND user_id=?");
    $checkLike->bind_param("ii", $id, $_SESSION['user_id']);
    $checkLike->execute();
    $userHasLiked = $checkLike->get_result()->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($blog['title']); ?> - Wordweave</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f5f7ff]">

<?php include "header.php"; ?>

<main class="py-10 px-6">
    <article class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-lg">
        
        <!-- Category Badge -->
        <?php if (!empty($blog['category'])): ?>
            <span class="inline-block px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full mb-4">
                🔖 <?php echo e($blog['category']); ?>
            </span>
        <?php endif; ?>

        <!-- Blog Header -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">
            <?php echo e($blog['title']); ?>
        </h1>

        <!-- Meta Information -->
        <div class="flex items-center gap-4 text-gray-600 mb-8 pb-6 border-b border-gray-200">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-blue-600 text-white flex items-center justify-center rounded-full text-sm font-bold">
                    <?= strtoupper($blog['username'][0]); ?>
                </div>
                <div>
                    <p class="font-semibold text-gray-800"><?php echo e($blog['username']); ?></p>
                    <p class="text-sm text-gray-500"><?php echo date('F j, Y', strtotime($blog['created_at'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Blog Image -->
        <?php if (!empty($blog['image'])): ?>
            <div class="mb-8">
                <img src="uploads/<?php echo e($blog['image']); ?>" 
                     class="w-full rounded-lg shadow-md"
                     alt="<?php echo e($blog['title']); ?>">
            </div>
        <?php endif; ?>

        <!-- Blog Content -->
        <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed mb-10">
            <?php echo nl2br(e($blog['content'])); ?>
        </div>

        <!-- Like Section -->
        <div class="border-t border-gray-200 pt-6 mt-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" action="like.php">
                            <input type="hidden" name="post_id" value="<?php echo $blog['id']; ?>">
                            <button class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition <?php echo $userHasLiked ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                                <span class="text-xl"><?php echo $userHasLiked ? '❤️' : '🤍'; ?></span>
                                <?php echo $userHasLiked ? 'Liked' : 'Like'; ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition">
                            <span class="text-xl">🤍</span>
                            Like
                        </a>
                    <?php endif; ?>
                    
                    <span class="text-gray-600 font-medium">
                        ❤️ <?php echo $likeCount; ?> <?php echo $likeCount == 1 ? 'like' : 'likes'; ?>
                    </span>
                </div>

                <!-- Edit/Delete buttons for post owner -->
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $blog['user_id']): ?>
                    <div class="flex gap-3">
                        <a href="editor.php?id=<?php echo $blog['id']; ?>" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                            ✏️ Edit
                        </a>
                        <form method="POST" action="delete_post.php" onsubmit="return confirm('Are you sure you want to delete this post?');">
                            <input type="hidden" name="post_id" value="<?php echo $blog['id']; ?>">
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">
                                🗑️ Delete
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Back to Blog -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <a href="index.php" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium transition">
                ← Back to all articles
            </a>
        </div>

    </article>
</main>

<!-- FOOTER -->
<footer class="bg-gradient-to-r from-[#0a1a3a] via-[#0d2b5a] to-[#1e3a8a] text-white mt-20 shadow-inner">
    <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
        <!-- Logo -->
        <div class="text-center md:text-left">
            <h2 class="text-3xl font-bold bg-gradient-to-r from-white via-yellow-300 to-yellow-500 bg-clip-text text-transparent">
                Wordweave
            </h2>
            <p class="text-gray-200 mt-2 text-sm">
                Create. Inspire. Share your story with the world.
            </p>
        </div>
        <!-- Links -->
        <div class="flex space-x-6 text-gray-200 text-sm">
            <a href="index.php" class="hover:text-yellow-300 transition">Home</a>
            <a href="about.php" class="hover:text-yellow-300 transition">About</a>
        </div>
    </div>
    <div class="border-t border-white/20 py-4 text-center">
        <p class="text-gray-200 text-sm">
            © <?php echo date('Y'); ?> Wordweave. All rights reserved.
        </p>
    </div>
</footer>

</body>
</html><?php
include "functions.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

// Fetch the blog post
$stmt = $conn->prepare("
    SELECT b.*, u.username 
    FROM blog_posts b
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$blog = $stmt->get_result()->fetch_assoc();

if (!$blog) {
    header("Location: index.php");
    exit;
}

// Get like count
$likeStmt = $conn->prepare("SELECT COUNT(*) AS total FROM likes WHERE post_id=?");
$likeStmt->bind_param("i", $id);
$likeStmt->execute();
$likeCount = $likeStmt->get_result()->fetch_assoc()['total'];

// Check if current user has liked this post
$userHasLiked = false;
if (isset($_SESSION['user_id'])) {
    $checkLike = $conn->prepare("SELECT id FROM likes WHERE post_id=? AND user_id=?");
    $checkLike->bind_param("ii", $id, $_SESSION['user_id']);
    $checkLike->execute();
    $userHasLiked = $checkLike->get_result()->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($blog['title']); ?> - Wordweave</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f5f7ff]">

<?php include "header.php"; ?>

<main class="py-10 px-6">
    <article class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-lg">
        
        <!-- Category Badge -->
        <?php if (!empty($blog['category'])): ?>
            <span class="inline-block px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full mb-4">
                🔖 <?php echo e($blog['category']); ?>
            </span>
        <?php endif; ?>

        <!-- Blog Header -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">
            <?php echo e($blog['title']); ?>
        </h1>

        <!-- Meta Information -->
        <div class="flex items-center gap-4 text-gray-600 mb-8 pb-6 border-b border-gray-200">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-blue-600 text-white flex items-center justify-center rounded-full text-sm font-bold">
                    <?= strtoupper($blog['username'][0]); ?>
                </div>
                <div>
                    <p class="font-semibold text-gray-800"><?php echo e($blog['username']); ?></p>
                    <p class="text-sm text-gray-500"><?php echo date('F j, Y', strtotime($blog['created_at'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Blog Image -->
        <?php if (!empty($blog['image'])): ?>
            <div class="mb-8">
                <img src="uploads/<?php echo e($blog['image']); ?>" 
                     class="w-full rounded-lg shadow-md"
                     alt="<?php echo e($blog['title']); ?>">
            </div>
        <?php endif; ?>

        <!-- Blog Content -->
        <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed mb-10">
            <?php echo nl2br(e($blog['content'])); ?>
        </div>

        <!-- Like Section -->
        <div class="border-t border-gray-200 pt-6 mt-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" action="like.php">
                            <input type="hidden" name="post_id" value="<?php echo $blog['id']; ?>">
                            <button class="flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition <?php echo $userHasLiked ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                                <span class="text-xl"><?php echo $userHasLiked ? '❤️' : '🤍'; ?></span>
                                <?php echo $userHasLiked ? 'Liked' : 'Like'; ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition">
                            <span class="text-xl">🤍</span>
                            Like
                        </a>
                    <?php endif; ?>
                    
                    <span class="text-gray-600 font-medium">
                        ❤️ <?php echo $likeCount; ?> <?php echo $likeCount == 1 ? 'like' : 'likes'; ?>
                    </span>
                </div>

                <!-- Edit/Delete buttons for post owner -->
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $blog['user_id']): ?>
                    <div class="flex gap-3">
                        <a href="editor.php?id=<?php echo $blog['id']; ?>" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                            ✏️ Edit
                        </a>
                        <form method="POST" action="delete_post.php" onsubmit="return confirm('Are you sure you want to delete this post?');">
                            <input type="hidden" name="post_id" value="<?php echo $blog['id']; ?>">
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition">
                                🗑️ Delete
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Back to Blog -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <a href="index.php" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium transition">
                ← Back to all articles
            </a>
        </div>

    </article>
</main>

<!-- FOOTER -->
<footer class="bg-gradient-to-r from-[#0a1a3a] via-[#0d2b5a] to-[#1e3a8a] text-white mt-20 shadow-inner">
    <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
        <!-- Logo -->
        <div class="text-center md:text-left">
            <h2 class="text-3xl font-bold bg-gradient-to-r from-white via-yellow-300 to-yellow-500 bg-clip-text text-transparent">
                Wordweave
            </h2>
            <p class="text-gray-200 mt-2 text-sm">
                Create. Inspire. Share your story with the world.
            </p>
        </div>
        <!-- Links -->
        <div class="flex space-x-6 text-gray-200 text-sm">
            <a href="index.php" class="hover:text-yellow-300 transition">Home</a>
            <a href="about.php" class="hover:text-yellow-300 transition">About</a>
        </div>
    </div>
    <div class="border-t border-white/20 py-4 text-center">
        <p class="text-gray-200 text-sm">
            © <?php echo date('Y'); ?> Wordweave. All rights reserved.
        </p>
    </div>
</footer>

</body>
</html>s
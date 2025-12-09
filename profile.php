<?php
include "functions.php";
requireLogin();

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'];
$user = getUser($conn, $user_id);

// User blogs
$stmt = $conn->prepare("SELECT * FROM blog_posts WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$posts = $stmt->get_result();

// Total Likes
$likeStmt = $conn->prepare("
    SELECT COUNT(*) AS total_likes 
    FROM likes l 
    JOIN blog_posts b ON l.post_id = b.id 
    WHERE b.user_id = ?
");
$likeStmt->bind_param("i", $user_id);
$likeStmt->execute();
$totalLikes = $likeStmt->get_result()->fetch_assoc()['total_likes'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Technet</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200">

<?php include "header.php"; ?>

<main class="max-w-6xl mx-auto mt-12 px-4">

    <!-- 🌟 Profile Card -->
    <section class="bg-white rounded-2xl shadow-lg p-8 flex flex-col md:flex-row items-center gap-8">

        <!-- Profile Avatar -->
        <div class="w-32 h-32 bg-gradient-to-br from-blue-600 to-purple-600 text-white
                    rounded-full flex items-center justify-center text-4xl font-bold shadow-lg">
            <?= strtoupper(substr($user['username'], 0, 1)); ?>
        </div>

        <!-- Profile Info -->
        <div class="flex-1">
            <h2 class="text-4xl font-bold text-gray-800"><?= e($user['username']); ?></h2>
            <p class="text-gray-600 text-lg mt-1"><?= e($user['email']); ?></p>

            <div class="mt-5 flex gap-6 text-gray-700">

                <div class="text-center">
                    <p class="text-3xl font-bold"><?= $posts->num_rows; ?></p>
                    <p class="text-sm">Posts</p>
                </div>

                <div class="text-center">
                    <p class="text-3xl font-bold"><?= $totalLikes; ?></p>
                    <p class="text-sm">Total Likes</p>
                </div>

                <div class="text-center">
                    <p class="text-3xl font-bold"><?= date('Y', strtotime($user['created_at'] ?? 'now')); ?></p>
                    <p class="text-sm">Member Since</p>
                </div>

            </div>

            <a href="editor.php" 
               class="inline-block mt-6 bg-blue-600 text-white px-6 py-2 rounded-xl
                      hover:bg-blue-700 transition shadow-md">
                + Create New Blog
            </a>
        </div>

    </section>

    <!-- 📝 My Blogs -->
    <section class="mt-12">

        <h3 class="text-3xl font-semibold text-gray-800 mb-6">My Blogs</h3>

        <div class="grid md:grid-cols-2 gap-8">

        <?php while ($row = $posts->fetch_assoc()): ?>
            <div class="bg-white rounded-2xl shadow hover:shadow-xl transition overflow-hidden">

                <?php if (!empty($row['image'])): ?>
                    <div class="h-52 overflow-hidden">
                        <img src="uploads/<?= e($row['image']); ?>" 
                             class="w-full h-full object-cover hover:scale-105 transition duration-700">
                    </div>
                <?php else: ?>
                    <div class="h-52 bg-gray-200 flex items-center justify-center text-gray-500">
                        No Image
                    </div>
                <?php endif; ?>

                <div class="p-6">

                    <h2 class="text-xl font-bold text-gray-800 hover:text-blue-600 transition">
                        <a href="view.php?id=<?= $row['id']; ?>">
                            <?= e($row['title']); ?>
                        </a>
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Created on <?= date('F j, Y', strtotime($row['created_at'])); ?>
                    </p>

                    <div class="flex gap-3 mt-5">

                        <a href="editor.php?id=<?= $row['id']; ?>"
                           class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                            ✏ Edit
                        </a>

                        <a href="delete_post.php?id=<?= $row['id']; ?>"
                           onclick="return confirm('Delete this post?');"
                           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            🗑 Delete
                        </a>

                    </div>

                </div>
            </div>
        <?php endwhile; ?>

        </div>

    </section>

</main>

</body>
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

</html>

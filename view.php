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
    echo "<p class='not-found-msg'>Blog not found!</p>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($blog['title']); ?> - MyBlog</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<?php include "header.php"; ?>

<main class="py-10">
    <section class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow">

        <!-- Blog Header -->
        <h1 class="text-4xl font-bold text-gray-800 mb-4">
            <?php echo e($blog['title']); ?>
        </h1>

        <p class="text-gray-600 mb-6">
            By <span class="font-semibold text-gray-700"><?php echo e($blog['username']); ?></span>
            on <?php echo date('F j, Y', strtotime($blog['created_at'])); ?>
        </p>

        <!-- Blog Image -->
        <?php if (!empty($blog['image'])): ?>
            <div class="mb-6">
                <img src="uploads/<?php echo e($blog['image']); ?>" 
                     class="w-full rounded-lg shadow"
                     alt="Blog Image">
            </div>
        <?php endif; ?>

        <!-- Blog Content -->
        <div class="prose max-w-none text-gray-800 leading-relaxed mb-10">
            <?php echo nl2br(e($blog['content'])); ?>
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

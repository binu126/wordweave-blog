<?php
include "functions.php";

// Search & Sort
$search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%";
$sort = (isset($_GET['sort']) && $_GET['sort'] === "oldest") ? "ASC" : "DESC";

$stmt = $conn->prepare("
    SELECT b.*, u.username 
    FROM blog_posts b 
    JOIN users u ON b.user_id = u.id 
    WHERE b.title LIKE ? OR b.content LIKE ? 
    ORDER BY b.created_at $sort
");
$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$result = $stmt->get_result();

// Trending
$trend = $conn->query("
    SELECT b.*, (SELECT COUNT(*) FROM likes l WHERE l.post_id=b.id) AS likes
    FROM blog_posts b 
    ORDER BY likes DESC 
    LIMIT 3
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wordweave Tech - Latest Tech News</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .card { transition: 0.3s; }
        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 22px 45px rgba(0,0,0,0.09);
        }
    </style>
</head>

<body class="bg-[#f5f7ff] text-gray-900">

<?php include "header.php"; ?>

<!-- HERO SECTION -->
<section class="bg-gradient-to-r from-[#0a1a3a] via-[#0d2b5a] to-[#1e3a8a] py-20 text-white shadow-xl">
    <div class="max-w-5xl mx-auto text-center px-4">
        <h1 class="text-5xl font-extrabold tracking-tight">
            Your Daily Dose of Tech, Innovation & Future.
        </h1>
        <p class="mt-4 text-lg text-gray-200">
            Breaking tech news, gadget reviews, AI breakthroughs, and industry insights - all in one place.
        </p>
    </div>
</section>

<main class="max-w-7xl mx-auto px-4 py-12">

    <!-- SEARCH + SORT -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-4">

        <!-- Search -->
        <form method="GET" 
            class="w-full md:w-3/4 flex items-center bg-white border rounded-2xl px-5 py-3 shadow">
            <input type="text"
                name="search"
                placeholder="Search tech news, gadgets, AI, cybersecurity..."
                value="<?php echo isset($_GET['search']) ? e($_GET['search']) : ''; ?>"
                class="w-full text-gray-800 bg-transparent focus:outline-none text-lg">
            <button class="px-5 py-2 bg-[#1e3a8a] text-white rounded-xl hover:bg-[#0d2b5a] transition">
                Search
            </button>
        </form>

        <!-- Sort -->
        <form method="GET">
            <select name="sort"
                onchange="this.form.submit()"
                class="px-5 py-3 border bg-white rounded-2xl shadow text-lg">
                <option value="newest" <?php if ($sort === "DESC") echo "selected"; ?>>Newest First</option>
                <option value="oldest" <?php if ($sort === "ASC") echo "selected"; ?>>Oldest First</option>
            </select>
        </form>
    </div>

    <!-- TRENDING -->
    <h2 class="text-3xl font-bold mb-6">🔥 Trending </h2>

    <div class="grid md:grid-cols-3 gap-8 mb-20">

        <?php while ($t = $trend->fetch_assoc()): ?>
        <div class="bg-white border rounded-2xl p-6 card">

            <a href="view.php?id=<?php echo $t['id']; ?>">
                <h3 class="text-xl font-semibold hover:text-blue-700">
                    <?php echo e($t['title']); ?>
                </h3>
            </a>

            <p class="text-gray-600 text-sm mt-2">❤️ <?php echo $t['likes']; ?> likes</p>

            <p class="text-gray-700 mt-3 text-sm">
                <?php echo substr(e($t['content']), 0, 110); ?>...
            </p>

        </div>
        <?php endwhile; ?>

    </div>

    <!-- ALL POSTS -->
    <h2 class="text-3xl font-bold mb-6">📡 Latest Tech Articles</h2>

    <div class="grid gap-12 md:grid-cols-2">

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>

            <div class="bg-white border rounded-2xl overflow-hidden card shadow">

                <!-- IMAGE -->
                <?php if (!empty($row['image'])): ?>
                <div class="h-64 overflow-hidden">
                    <img src="uploads/<?php echo e($row['image']); ?>"
                        class="w-full h-full object-cover hover:scale-110 transition duration-700">
                </div>
                <?php endif; ?>

                <div class="p-6">

                    <!-- TITLE -->
                    <h2 class="text-2xl font-semibold hover:text-blue-700">
                        <a href="view.php?id=<?php echo $row['id']; ?>">
                            <?php echo e($row['title']); ?>
                        </a>
                    </h2>

                    <!-- META -->
                    <p class="text-gray-500 text-sm mt-1">
                        By <?php echo e($row['username']); ?> • <?php echo date('M j, Y', strtotime($row['created_at'])); ?>
                    </p>

                    <!-- EXCERPT -->
                    <p class="text-gray-700 mt-4 leading-relaxed">
                        <?php echo substr(e($row['content']), 0, 160); ?>...
                    </p>

                    <!-- TAGS (auto-generated) -->
                    <div class="flex gap-2 mt-4 flex-wrap">
                        <?php
                            $tags = array_slice(explode(" ", strtolower($row['title'])), 0, 3);
                            foreach ($tags as $tag):
                        ?>
                        <span class="text-xs px-3 py-1 bg-gray-200 rounded-full font-medium">
                            #<?php echo e($tag); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>

                    <!-- LIKES -->
                    <?php
                        $likeStmt = $conn->prepare("SELECT COUNT(*) AS total FROM likes WHERE post_id=?");
                        $likeStmt->bind_param("i", $row['id']);
                        $likeStmt->execute();
                        $likeCount = $likeStmt->get_result()->fetch_assoc()['total'];
                    ?>

                    <div class="flex items-center justify-between mt-6">
                        <form method="POST" action="like.php">
                            <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                            <button class="text-red-500 hover:text-red-600 font-medium text-lg">
                                ❤️ Like
                            </button>
                        </form>

                        <span class="text-gray-700 text-sm font-medium">
                            ❤️ <?php echo $likeCount; ?> likes
                        </span>
                    </div>

                </div>
            </div>

            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-600 text-lg">No tech articles found.</p>
        <?php endif; ?>

    </div>

</main>

</body>
</html>

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

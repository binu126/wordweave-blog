<?php
include "functions.php";
requireLogin();

// If editing an existing post
$editing = false;
$post = [
    "id" => "",
    "title" => "",
    "content" => "",
    "image" => "",
    "category" => ""
];

if (isset($_GET['id'])) {
    $editing = true;

    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $_GET['id'], $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
    } else {
        // User doesn't own this post or it doesn't exist
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $editing ? "Edit Blog" : "Create Blog"; ?> - Wordweave</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f5f7ff]">

<?php include "header.php"; ?>

<main class="max-w-4xl mx-auto mt-10 px-6 pb-12">

    <div class="bg-white rounded-2xl shadow-lg p-8">

        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <?= $editing ? "Edit Your Blog" : "Create a New Blog"; ?>
        </h1>
        
        <p class="text-gray-600 mb-6">
            <?= $editing ? "Update your blog post below" : "Share your thoughts with the world"; ?>
        </p>

        <form action="save_post.php" method="post" enctype="multipart/form-data" class="space-y-6">

            <input type="hidden" name="id" value="<?= e($post['id']); ?>">

            <!-- Title -->
            <div>
                <label for="title" class="block font-semibold mb-1 text-gray-700">Blog Title</label>
                <input 
                    type="text"
                    id="title"
                    name="title"
                    required
                    value="<?= e($post['title']); ?>"
                    placeholder="Enter an engaging title..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
            </div>

            <!-- Category -->
            <div>
                <label for="category" class="block font-semibold mb-1 text-gray-700">Category</label>

                <?php 
                    $categories = ["General", "AI", "Web Development", "Operating Systems", "Security", "Gadgets"];
                ?>

                <select 
                    id="category"
                    name="category" 
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >
                    <option value="">Select a category</option>

                    <?php foreach ($categories as $cat): ?>
                        <option 
                            value="<?= e($cat) ?>" 
                            <?= ($post['category'] == $cat) ? "selected" : "" ?>
                        >
                            <?= e($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block font-semibold mb-1 text-gray-700">Content</label>
                <textarea 
                    id="content"
                    name="content" 
                    rows="12" 
                    required
                    placeholder="Write your blog content here..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                ><?= e($post['content']); ?></textarea>
            </div>

            <!-- Image Upload -->
            <div>
                <label for="image" class="block font-semibold mb-1 text-gray-700">Blog Image</label>
                
                <p class="text-sm text-gray-500 mb-2">
                    <?= $editing && !empty($post['image']) ? "Upload a new image to replace the current one (optional)" : "Add a featured image to your blog post (optional)" ?>
                </p>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/*"
                    class="block w-full border border-gray-300 p-2 rounded-lg cursor-pointer focus:ring-2 focus:ring-blue-500 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    onchange="previewImage(event)"
                >

                <!-- Live Image Preview -->
                <div class="mt-4">
                    <img 
                        id="preview"
                        src="<?= $editing && !empty($post['image']) ? 'uploads/' . e($post['image']) : '' ?>"
                        alt="Preview"
                        class="max-w-md w-full h-auto object-cover rounded-lg shadow-md <?= empty($post['image']) ? 'hidden' : '' ?>"
                    >
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 pt-4">
                <button 
                    type="submit"
                    class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">
                    <?= $editing ? "✓ Update Blog" : "✍️ Publish Blog"; ?>
                </button>
                
                <a 
                    href="index.php"
                    class="px-8 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition text-center">
                    Cancel
                </a>
            </div>

        </form>

    </div>

</main>

<!-- Live Preview JS -->
<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const preview = document.getElementById("preview");
        preview.src = URL.createObjectURL(file);
        preview.classList.remove("hidden");
    }
}
</script>

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
</html>
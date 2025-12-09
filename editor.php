<?php
include "functions.php";

// If editing an existing post
$editing = false;
$post = [
    "id" => "",
    "title" => "",
    "content" => "",
    "image" => ""
];

if (isset($_GET['id'])) {
    $editing = true;

    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $post = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $editing ? "Edit Blog" : "Create Blog"; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<?php include "header.php"; ?>

<div class="max-w-4xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg">

    <h2 class="text-3xl font-bold text-gray-900 mb-6">
        <?= $editing ? "Edit Your Blog" : "Create a New Blog"; ?>
    </h2>

    <form action="save_post.php" method="post" enctype="multipart/form-data" class="space-y-6">

        <input type="hidden" name="id" value="<?= e($post['id']); ?>">

        <!-- Title -->
        <div>
            <label class="block font-semibold mb-1">Blog Title</label>
            <input 
                type="text"
                name="title"
                required
                value="<?= e($post['title']); ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            >
        </div>

        <!-- Content -->
        <div>
            <label class="block font-semibold mb-1">Content</label>
            <textarea 
                name="content" 
                rows="10" 
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
            ><?= e($post['content']); ?></textarea>
        </div>

        <!-- Image Upload -->
        <div>
            <label class="block font-semibold mb-1">Blog Image</label>

            <input
                type="file"
                name="image"
                accept="image/*"
                class="block w-full border border-gray-300 p-2 rounded-lg cursor-pointer focus:ring-2 focus:ring-blue-500"
                onchange="previewImage(event)"
            >

            <!-- Live Image Preview -->
            <div class="mt-4">
                <img 
                    id="preview"
                    src="<?= $editing && !empty($post['image']) ? 'uploads/' . e($post['image']) : '' ?>"
                    class="w-48 h-32 object-cover rounded-lg shadow <?= empty($post['image']) ? 'hidden' : '' ?>"
                >
            </div>
        </div>

        <!-- Submit -->
        <button 
            type="submit"
            class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
            <?= $editing ? "Update Blog" : "Publish Blog"; ?>
        </button>

    </form>
</div>

<!-- Live Preview JS -->
<script>
function previewImage(event) {
    const preview = document.getElementById("preview");
    preview.src = URL.createObjectURL(event.target.files[0]);
    preview.classList.remove("hidden");
}
</script>

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

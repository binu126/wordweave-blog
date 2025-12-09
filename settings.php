<?php
include "functions.php";
requireLogin();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'];
$user = getUser($conn, $user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Update Profile
    if (isset($_POST['update_profile'])) {

        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        if (!empty($username) && !empty($email)) {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
            $stmt->bind_param("ssi", $username, $email, $user_id);
            $stmt->execute();

            echo "<script>alert('Profile updated successfully!');</script>";
        }
    }

    // Change Password
    if (isset($_POST['change_password'])) {

        $current = $_POST['current_password'];
        $new = $_POST['new_password'];

        $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result && password_verify($current, $result['password'])) {
            $newHash = password_hash($new, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $update->bind_param("si", $newHash, $user_id);
            $update->execute();

            echo "<script>alert('Password changed successfully!');</script>";
        } else {
            echo "<script>alert('Current password is incorrect!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Settings - MyBlog</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<?php include "header.php"; ?>

<div class="max-w-4xl mx-auto mt-12">

    <!-- Page Header -->
    <div class="mb-10">
        <h2 class="text-4xl font-bold text-gray-900">Account Settings</h2>
        <p class="text-gray-600 mt-1">Manage your profile information & security</p>
    </div>

    <!-- Profile Section -->
    <div class="bg-white p-8 rounded-2xl shadow-lg mb-10 border border-gray-200">
        
        <div class="flex items-center gap-4 mb-8">
            <div class="w-16 h-16 bg-blue-600 text-white flex items-center justify-center rounded-full text-3xl font-bold">
                <?= strtoupper($user['username'][0]); ?>
            </div>
            <div>
                <h3 class="text-2xl font-semibold text-gray-800">Profile Information</h3>
                <p class="text-gray-500 text-sm">Update your account details</p>
            </div>
        </div>

        <form method="post" class="space-y-6">

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="text-gray-700 font-medium">Username</label>
                    <input type="text" 
                           name="username" 
                           value="<?= e($user['username']); ?>" 
                           required
                           class="w-full mt-1 p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="text-gray-700 font-medium">Email Address</label>
                    <input type="email" 
                           name="email" 
                           value="<?= e($user['email']); ?>" 
                           required
                           class="w-full mt-1 p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <button type="submit"
                    name="update_profile"
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                Save Changes
            </button>

        </form>
    </div>

    <!-- Password Section -->
    <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-200">
        
        <h3 class="text-2xl font-semibold text-gray-800 mb-2">Security</h3>
        <p class="text-gray-500 mb-6 text-sm">Change your password to keep your account secure</p>

        <form method="post" class="space-y-5">

            <div>
                <label class="text-gray-700 font-medium">Current Password</label>
                <input type="password"
                       name="current_password"
                       placeholder="Enter current password"
                       required
                       class="w-full mt-1 p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="text-gray-700 font-medium">New Password</label>
                <input type="password"
                       name="new_password"
                       placeholder="Enter new password"
                       required
                       class="w-full mt-1 p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <button type="submit"
                    name="change_password"
                    class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-medium">
                Update Password
            </button>

        </form>
    </div>

</div>

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

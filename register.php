<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        header("Location: login.php?registered=1");
        exit;
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - WordWeave</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#0a1a3a] via-[#0d2b5a] to-[#1e3a8a]">

    <div class="backdrop-blur-lg bg-white/10 p-10 rounded-xl shadow-2xl w-full max-w-md border border-white/20">
        
        <h2 class="text-3xl font-bold text-white text-center mb-6">Join to wordweave</h2>

        <?php if (!empty($error)): ?>
            <p class="text-red-400 bg-red-900/30 px-3 py-2 text-center rounded mb-4">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>

        <form method="POST" class="space-y-5">

            <div>
                <label class="text-white text-sm mb-1 block">Username</label>
                <input 
                    type="text" 
                    name="username" 
                    required
                    class="w-full px-4 py-2 rounded-lg bg-white/20 text-white placeholder-gray-300 
                           border border-white/30 focus:border-blue-300 focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="text-white text-sm mb-1 block">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    required
                    class="w-full px-4 py-2 rounded-lg bg-white/20 text-white placeholder-gray-300 
                           border border-white/30 focus:border-blue-300 focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="text-white text-sm mb-1 block">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    required
                    class="w-full px-4 py-2 rounded-lg bg-white/20 text-white placeholder-gray-300 
                           border border-white/30 focus:border-blue-300 focus:ring-2 focus:ring-blue-400">
            </div>

            <button 
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg shadow-lg transition">
                Register
            </button>

            <p class="text-center text-gray-200 mt-3">
                Already have an account? 
                <a href="login.php" class="text-yellow-300 hover:underline">Login</a>
            </p>

        </form>
    </div>

</body>
</html>

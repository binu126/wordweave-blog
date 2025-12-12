<?php
session_start();
include "db.php";

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Wordweave</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#0a1a3a] via-[#0d2b5a] to-[#1e3a8a] px-4">
    
    <div class="w-full max-w-md">
        
        <!-- Logo/Brand -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-white via-yellow-300 to-yellow-500 bg-clip-text text-transparent">
                Wordweave
            </h1>
            <p class="text-gray-200 mt-2">Share your story with the world</p>
        </div>

        <!-- Login Card -->
        <div class="backdrop-blur-lg bg-white/10 p-8 md:p-10 rounded-2xl shadow-2xl border border-white/20">
            
            <h2 class="text-2xl font-bold text-white text-center mb-6">Welcome Back!</h2>
            
            <?php if (!empty($error)): ?>
                <div class="bg-red-900/40 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg mb-6 text-center">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php" class="space-y-5">
                
                <div>
                    <label for="email" class="text-white text-sm font-medium mb-2 block">Email Address</label>
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        required 
                        placeholder="your@email.com"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        class="w-full px-4 py-3 rounded-lg bg-white/20 text-white placeholder-gray-300 
                               border border-white/30 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400 focus:outline-none transition">
                </div>
                
                <div>
                    <label for="password" class="text-white text-sm font-medium mb-2 block">Password</label>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        required 
                        placeholder="Enter your password"
                        class="w-full px-4 py-3 rounded-lg bg-white/20 text-white placeholder-gray-300 
                               border border-white/30 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400 focus:outline-none transition">
                </div>
                
                <button 
                    type="submit"
                    class="w-full bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold py-3 rounded-lg shadow-lg transition transform hover:scale-[1.02]">
                    Login
                </button>
                
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/20"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-transparent text-gray-300">New to Wordweave?</span>
                </div>
            </div>

            <!-- Register Link -->
            <div class="text-center">
                <a href="register.php" 
                   class="inline-block w-full px-6 py-3 bg-white/20 border border-white/30 text-white font-semibold rounded-lg 
                          backdrop-blur-md hover:bg-white/30 transition">
                    Create Account
                </a>
            </div>

            <!-- Back to Home -->
            <div class="mt-6 text-center">
                <a href="index.php" class="text-yellow-300 hover:text-yellow-200 text-sm transition">
                    ← Back to Homepage
                </a>
            </div>

        </div>

    </div>

</body>
</html>
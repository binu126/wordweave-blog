<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- NAVBAR -->
<header class="sticky top-0 z-50 bg-gradient-to-r from-[#0a1a3a] via-[#0d2b5a] to-[#1e3a8a] shadow-lg">

    <div class="max-w-7xl mx-auto px-4">

        <div class="flex justify-between items-center py-4">

            <!-- LOGO -->
            <a href="index.php" class="text-2xl font-bold bg-gradient-to-r from-white via-yellow-300 to-yellow-500 bg-clip-text text-transparent">
                Wordweave
            </a>

            <!-- Hamburger Button -->
            <button id="hamburgerBtn" class="md:hidden flex flex-col gap-1.5">
                <span class="w-7 h-1 bg-white rounded transition"></span>
                <span class="w-7 h-1 bg-white rounded transition"></span>
                <span class="w-7 h-1 bg-white rounded transition"></span>
            </button>

            <!-- NAV MENU -->
            <nav id="navMenu" class="hidden md:flex space-x-3 items-center">
                <ul class="flex space-x-2 text-white font-medium">

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <li>
                            <a class="px-4 py-2 hover:bg-white/20 rounded-md" href="index.php">🏠 Home</a>
                        </li>
                        <li>
                            <a class="px-4 py-2 hover:bg-white/20 rounded-md" href="profile.php">👤 My Profile</a>
                        </li>
                        <li>
                            <a class="px-4 py-2 hover:bg-white/20 rounded-md" href="editor.php">✍️ Create Blog</a>
                        </li>
                        <li>
                            <a class="px-4 py-2 hover:bg-white/20 rounded-md" href="settings.php">⚙️ Settings</a>
                        </li>
                        <li>
                            <a class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-md" href="logout.php">🚪 Logout</a>
                        </li>

                    <?php else: ?>

                        <li>
                            <a class="px-4 py-2 hover:bg-white/20 rounded-md" href="index.php">🏠 Home</a>
                        </li>
                        <li>
                            <a class="px-4 py-2 hover:bg-white/20 rounded-md" href="register.php">📝 Sign Up</a>
                        </li>
                        <li>
                            <a class="px-4 py-2 hover:bg-white/20 rounded-md" href="login.php">🔑 Login</a>
                        </li>

                    <?php endif; ?>
                </ul>
            </nav>

        </div>

        <!-- MOBILE MENU (Hidden by default) -->
        <nav id="mobileMenu" class="hidden md:hidden pb-4">
            <ul class="flex flex-col gap-2 text-white font-medium">

                <?php if (isset($_SESSION['user_id'])): ?>

                    <li><a class="block px-4 py-2 hover:bg-white/20 rounded-md" href="index.php">🏠 Home</a></li>
                    <li><a class="block px-4 py-2 hover:bg-white/20 rounded-md" href="profile.php">👤 My Profile</a></li>
                    <li><a class="block px-4 py-2 hover:bg-white/20 rounded-md" href="editor.php">✍️ Create Blog</a></li>
                    <li><a class="block px-4 py-2 hover:bg-white/20 rounded-md" href="settings.php">⚙️ Settings</a></li>
                    <li><a class="block px-4 py-2 bg-red-500 hover:bg-red-600 rounded-md" href="logout.php">🚪 Logout</a></li>

                <?php else: ?>

                    <li><a class="block px-4 py-2 hover:bg-white/20 rounded-md" href="index.php">🏠 Home</a></li>
                    <li><a class="block px-4 py-2 hover:bg-white/20 rounded-md" href="register.php">📝 Sign Up</a></li>
                    <li><a class="block px-4 py-2 hover:bg-white/20 rounded-md" href="login.php">🔑 Login</a></li>

                <?php endif; ?>
            </ul>
        </nav>

    </div>
</header>

<!-- TOGGLE SCRIPT -->
<script>
    const hamburger = document.getElementById("hamburgerBtn");
    const mobileMenu = document.getElementById("mobileMenu");

    hamburger.addEventListener("click", () => {
        mobileMenu.classList.toggle("hidden");

        // Animate hamburger into X
        const bars = hamburger.querySelectorAll("span");
        bars[0].classList.toggle("rotate-45");
        bars[0].classList.toggle("translate-y-2");

        bars[1].classList.toggle("opacity-0");

        bars[2].classList.toggle("-rotate-45");
        bars[2].classList.toggle("-translate-y-2");
    });
</script>
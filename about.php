<?php include "header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - Wordweave Tech</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-[#f1f5ff] via-[#e5ecff] to-[#dce4ff] text-gray-800">

<!-- Hero Section -->
<section class="py-20 text-center bg-gradient-to-r from-[#0a1a3a] via-[#0d2b5a] to-[#1e3a8a] text-white shadow-lg">
    <h1 class="text-5xl font-bold">About Wordweave Tech</h1>
    <p class="mt-4 text-lg text-gray-200 max-w-2xl mx-auto">
        Your trusted source for the latest tech news, trends, reviews, and deep-dive analyses.
    </p>
</section>

<!-- Main Content -->
<main class="max-w-5xl mx-auto px-6 py-16">

    <!-- Section 1 -->
    <div class="mb-16">
        <h2 class="text-3xl font-bold text-[#0a1a3a] mb-4">Our Mission</h2>
        <p class="text-gray-700 text-lg leading-relaxed">
            At Wordweave Tech, our mission is to decode the fast-moving world of technology. 
            From breakthrough innovations to industry-shaping trends, we deliver clear, insightful, 
            and reliable coverage to keep our readers informed and ahead of the curve.
        </p>
    </div>

    <!-- Section 2 -->
    <div class="mb-16 grid md:grid-cols-2 gap-10 items-center">
        <div>
            <h2 class="text-3xl font-bold text-[#0a1a3a] mb-4">What We Cover</h2>
            <p class="text-gray-700 text-lg leading-relaxed">
                Our platform explores everything shaping the modern tech landscape including 
                consumer gadgets, AI breakthroughs, cybersecurity, startups, gaming, 
                software updates, and emerging industries.
                <br><br>
                We simplify complexity and bring you the stories that matter.
            </p>
        </div>
        <div>
            <img src="https://images.unsplash.com/photo-1518770660439-4636190af475" 
                 class="rounded-2xl shadow-lg">
        </div>
    </div>

    <!-- Section 3 -->
    <div class="mb-16">
        <h2 class="text-3xl font-bold text-[#0a1a3a] mb-4">Our Approach</h2>
        <p class="text-gray-700 text-lg leading-relaxed">
            Technology changes fast and so do we.  
            Our team combines journalistic expertise with real industry experience to deliver:
        </p>

        <ul class="mt-4 space-y-2 text-lg text-gray-700 list-disc pl-6">
            <li>Real-time updates on major tech developments</li>
            <li>In-depth reviews and hands-on testing</li>
            <li>Data-driven insights and trend analysis</li>
            <li>Interviews with founders and industry leaders</li>
            <li>Clear explanations for complex topics like AI, cloud, and cybersecurity</li>
        </ul>
    </div>

    <!-- Section 4 – Team Box -->
    <div class="bg-white border rounded-2xl shadow-xl p-10">
        <h2 class="text-3xl font-bold text-[#0a1a3a] mb-4 text-center">The Team Behind Wordweave Tech</h2>
        <p class="text-gray-700 text-lg text-center max-w-3xl mx-auto leading-relaxed">
            We are a team of tech journalists, analysts, developers, and creators who share a passion 
            for innovation. Our goal is to deliver accurate, unbiased, and engaging tech content that 
            empowers readers to understand the future as it unfolds.
        </p>
    </div>

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

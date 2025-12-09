📘 Wordweave – Modern PHP Blog Platform
A clean and minimal blog application built using PHP, MySQL, and Tailwind CSS.
Features include user authentication, blog posting, comments, likes, categories, and image uploads.

🚀 Features

✅ User System
•	Register & Login
•	Secure password hashing

📝 Blog Features

•	Create, edit, delete blog posts
•	Upload images for posts
•	View single post page

💬 Interaction

•	Like posts
•	Real-time updates after actions

🎨 UI / UX

•	Tailwind CSS styling
•	Fully responsive
•	Minimal, modern theme


📂 Project Structure
/project-folder
│── about.php
│── index.php
│── login.php
│── register.php
│── view_blog.php
│── post_blog.php
│── post_comment.php
│── header.php
│── footer.php
│── functions.php
│── /uploads
└── README.md

🗄️ Database Setup

Run this SQL in phpMyAdmin (or your MySQL client):
CREATE DATABASE IF NOT EXISTS blog_app;
USE blog_app;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    UNIQUE(post_id,user_id),
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

⚙️ Installation

1. Clone Repository

git clone https://github.com/binu126/wordweave-blogsite.git

2. Move project to your server

For XAMPP:
htdocs/wordweave
For InfinityFree:
/htdocs (public_html)

3. Configure Database

Edit functions.php:
$conn = new mysqli("localhost", "root", "", "blog_app");
On InfinityFree, your DB host will be something like:
sqlxxx.epizy.com


📌 Future Improvements

•	Categories system
•	Admin dashboard
•	Profile pages
•	Search system
•	Tag support
•	Markdown blog editor

🧑‍💻 Contributing
Feel free to fork this repo and submit pull requests.

📄 License
This project is open-source under the MIT License

⭐ If you like this project, give it a star!



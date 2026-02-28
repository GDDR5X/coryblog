<?php
// install.php
require_once 'includes/config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Initialize SQLite Database
        $db = new PDO('sqlite:' . DB_FILE);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create tables if not exists
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                email TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        $db->exec("
            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL
            )
        ");
        
        $db->exec("
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                content TEXT NOT NULL,
                category_id INTEGER DEFAULT 0,
                status TEXT DEFAULT 'published',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id)
            )
        ");
        
        $db->exec("
            CREATE TABLE IF NOT EXISTS comments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                post_id TEXT NOT NULL,
                author_name TEXT NOT NULL,
                content TEXT NOT NULL,
                is_approved INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (post_id) REFERENCES posts(slug)
            )
        ");
        
        // Check if data already exists
        $stmt = $db->query("SELECT COUNT(*) FROM users");
        $user_count = $stmt->fetchColumn();
        
        if ($user_count == 0) {
            // Initialize default user
            $db->exec("INSERT INTO users (username, password_hash, email) VALUES ('admin', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'admin@example.com')");
            $message .= "用户数据已初始化 (管理员: admin / password)<br>";
        } else {
            $message .= "用户数据已存在。<br>";
        }
        
        // Initialize categories if empty
        $stmt = $db->query("SELECT COUNT(*) FROM categories");
        if ($stmt->fetchColumn() == 0) {
            $db->exec("INSERT INTO categories (name, slug) VALUES ('未分类', 'uncategorized')");
            $db->exec("INSERT INTO categories (name, slug) VALUES ('动漫评论', 'anime-reviews')");
            $db->exec("INSERT INTO categories (name, slug) VALUES ('技术杂谈', 'tech')");
            $message .= "分类数据已初始化。<br>";
        } else {
            $message .= "分类数据已存在。<br>";
        }
        
        // Initialize default post if empty
        $stmt = $db->query("SELECT COUNT(*) FROM posts");
        if ($stmt->fetchColumn() == 0) {
            $db->exec("INSERT INTO posts (title, slug, content, category_id, status) VALUES ('你好，世界！', 'hello-world', '欢迎来到你的新博客！这是一篇测试文章。', 1, 'published')");
            $message .= "默认文章已初始化。<br>";
        } else {
            $message .= "文章数据已存在。<br>";
        }
        
        $message .= "<strong>安装成功！</strong> 数据已存储在 <code>data/db/blog.db</code> 中。";
        
    } catch (Exception $e) {
        $message = "错误: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>安装博客 (SQLite版)</title>
    <style>
        body { font-family: "Microsoft YaHei", sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f8ff; }
        .card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; max-width: 400px; width: 100%; }
        h1 { color: #ff69b4; margin-bottom: 1rem; }
        button { background: #87cefa; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 1rem; }
        button:hover { background: #00bfff; }
        .message { margin-top: 1rem; padding: 1rem; background: #e6fffa; color: #2c7a7b; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>博客安装程序</h1>
        <p>点击下方按钮初始化 SQLite 数据库文件。</p>
        <form method="post">
            <button type="submit">开始安装</button>
        </form>
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
    </div>
</body>
</html>

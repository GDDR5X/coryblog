<?php
// install.php
require_once 'includes/config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Initialize Users
        if (!file_exists(FILE_USERS)) {
            $users = [
                [
                    'id' => 1,
                    'username' => 'admin',
                    'password_hash' => password_hash('password', PASSWORD_DEFAULT),
                    'email' => 'admin@example.com',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            write_json(FILE_USERS, $users);
            $message .= "用户数据已初始化 (管理员: admin / password)<br>";
        } else {
            $message .= "用户数据文件已存在。<br>";
        }

        // 2. Initialize Categories
        if (!file_exists(FILE_CATEGORIES)) {
            $categories = [
                ['id' => 1, 'name' => '未分类', 'slug' => 'uncategorized'],
                ['id' => 2, 'name' => '动漫评论', 'slug' => 'anime-reviews'],
                ['id' => 3, 'name' => '技术杂谈', 'slug' => 'tech']
            ];
            write_json(FILE_CATEGORIES, $categories);
            $message .= "分类数据已初始化。<br>";
        }

        // 3. Initialize Posts
        if (!file_exists(FILE_POSTS)) {
            $posts = [
                [
                    'id' => 1,
                    'title' => '你好，世界！',
                    'slug' => 'hello-world',
                    'content' => '欢迎来到你的新二次元博客！这是一篇由 JSON 存储驱动的测试文章。你可以在后台编辑或删除它。支持 **Markdown** 哦！',
                    'category_id' => 1,
                    'status' => 'published',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ];
            write_json(FILE_POSTS, $posts);
            $message .= "文章数据已初始化。<br>";
        }

        // 4. Initialize Comments
        if (!file_exists(FILE_COMMENTS)) {
            write_json(FILE_COMMENTS, []);
            $message .= "评论数据已初始化。<br>";
        }

        $message .= "<strong>安装成功！</strong> 数据已存储在 <code>data/*.json</code> 中。";

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
    <title>安装博客 (JSON版)</title>
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
        <p>点击下方按钮初始化 JSON 数据文件。</p>
        <form method="post">
            <button type="submit">开始安装</button>
        </form>
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
    </div>
</body>
</html>

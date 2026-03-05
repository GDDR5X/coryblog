<?php
// includes/db.php

// 检查SQLite扩展是否可用
if (!extension_loaded('pdo_sqlite')) {
    // 如果没有SQLite扩展，不定义任何函数，直接返回
    return;
}

// 数据库文件路径 - 放在web根目录外更安全
define('DB_DIR', __DIR__ . '/../data/db/');
define('DB_FILE', DB_DIR . 'blog.db');

// 确保数据库目录存在
if (!is_dir(DB_DIR)) {
    mkdir(DB_DIR, 0755, true);
}

// 数据库初始化函数
function init_db() {
    try {
        $db = new PDO('sqlite:' . DB_FILE);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 创建用户表
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                email TEXT,
                email_verified INTEGER DEFAULT 0,
                email_verification_token TEXT,
                email_verification_token_expires DATETIME,
                email_verified_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // 创建分类表
        $db->exec("
            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL
            )
        ");
        
        // 创建文章表
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
        
        // 创建评论表
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
        
        // 检查是否需要初始化默认数据
        $stmt = $db->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() == 0) {
            // 初始化默认用户
            $db->exec("INSERT INTO users (username, password_hash, email) VALUES ('admin', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'admin@example.com')");
            
            // 初始化默认分类
            $db->exec("INSERT INTO categories (name, slug) VALUES ('未分类', 'uncategorized')");
            $db->exec("INSERT INTO categories (name, slug) VALUES ('动漫评论', 'anime-reviews')");
            $db->exec("INSERT INTO categories (name, slug) VALUES ('技术杂谈', 'tech')");
            
            // 初始化默认文章
            $db->exec("INSERT INTO posts (title, slug, content, category_id, status) VALUES ('你好，世界！', 'hello-world', '欢迎来到你的新博客！这是一篇测试文章。', 1, 'published')");
        }
        
        return $db;
    } catch (PDOException $e) {
        die("数据库初始化失败: " . $e->getMessage());
    }
}

// 获取数据库连接
function get_db() {
    static $db = null;
    if ($db === null) {
        if (!file_exists(DB_FILE)) {
            $db = init_db();
        } else {
            try {
                $db = new PDO('sqlite:' . DB_FILE);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("数据库连接失败: " . $e->getMessage());
            }
        }
    }
    return $db;
}

// --- 用户相关函数 ---

function get_user_by_username($username) {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ? $user : null;
}

function get_user_by_id($id) {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ? $user : null;
}

function add_user($username, $password, $email = '') {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO users (username, password_hash, email, email_verified, email_verification_token, email_verification_token_expires, email_verified_at) VALUES (?, ?, ?, 0, '', NULL, NULL)");
    try {
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $email]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// --- 分类相关函数 ---

function get_all_categories() {
    $db = get_db();
    $stmt = $db->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_category_name($id) {
    $db = get_db();
    $stmt = $db->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    return $category ? $category['name'] : '未分类';
}

// --- 文章相关函数 ---

function get_all_posts($status = null) {
    $db = get_db();
    
    $query = "SELECT * FROM posts";
    if ($status) {
        $query .= " WHERE status = ?";
    }
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $db->prepare($query);
    if ($status) {
        $stmt->execute([$status]);
    } else {
        $stmt->execute();
    }
    
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 读取Markdown文件作为补充
    $md_posts = get_all_posts_md($status);
    $md_slugs = array_column($md_posts, 'slug');
    
    // 合并数据库和Markdown文件的数据
    $result = [];
    foreach ($posts as $post) {
        $result[$post['slug']] = $post;
    }
    
    foreach ($md_posts as $post) {
        if (!isset($result[$post['slug']])) {
            $result[$post['slug']] = $post;
        }
    }
    
    return array_values($result);
}

function get_post_by_slug($slug) {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM posts WHERE slug = ?");
    $stmt->execute([$slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($post) {
        return $post;
    }
    
    // 如果数据库中没有，尝试从Markdown文件读取
    return get_post_by_slug_md($slug);
}

function get_post_by_id($id) {
    return get_post_by_slug($id);
}

function save_post($post_data) {
    $db = get_db();
    
    // 检查是否已存在
    $existing = get_post_by_slug($post_data['slug']);
    
    if ($existing) {
        // 更新现有文章
        $stmt = $db->prepare("
            UPDATE posts 
            SET title = ?, content = ?, category_id = ?, status = ?, updated_at = CURRENT_TIMESTAMP
            WHERE slug = ?
        ");
        $stmt->execute([
            $post_data['title'],
            $post_data['content'],
            $post_data['category_id'],
            $post_data['status'],
            $post_data['slug']
        ]);
    } else {
        // 插入新文章
        $stmt = $db->prepare("
            INSERT INTO posts (title, slug, content, category_id, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $post_data['title'],
            $post_data['slug'],
            $post_data['content'],
            $post_data['category_id'],
            $post_data['status']
        ]);
    }
    
    // 同时保存到Markdown文件
    return save_post_md($post_data);
}

function delete_post($slug) {
    $db = get_db();
    
    // 从数据库删除
    $stmt = $db->prepare("DELETE FROM posts WHERE slug = ?");
    $stmt->execute([$slug]);
    
    // 删除Markdown文件
    return delete_post_md($slug);
}

// --- 评论相关函数 ---

function get_comments_by_post($post_id, $approved_only = true) {
    $db = get_db();
    
    $query = "SELECT * FROM comments WHERE post_id = ?";
    if ($approved_only) {
        $query .= " AND is_approved = 1";
    }
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$post_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function add_comment($post_id, $author, $content) {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO comments (post_id, author_name, content, is_approved) VALUES (?, ?, ?, 0)");
    return $stmt->execute([$post_id, $author, $content]);
}

// --- 迁移相关函数 ---

function migrate_from_json() {
    global $db;
    
    // 迁移用户
    if (file_exists(FILE_USERS)) {
        $users = json_decode(file_get_contents(FILE_USERS), true);
        if ($users) {
            foreach ($users as $user) {
                $db->exec("INSERT OR REPLACE INTO users (id, username, password_hash, email, created_at) VALUES (" . 
                    $user['id'] . ", '" . $user['username'] . "', '" . $user['password_hash'] . "', '" . 
                    ($user['email'] ?? '') . "', '" . ($user['created_at'] ?? '') . "')");
            }
        }
    }
    
    // 迁移分类
    if (file_exists(FILE_CATEGORIES)) {
        $categories = json_decode(file_get_contents(FILE_CATEGORIES), true);
        if ($categories) {
            foreach ($categories as $cat) {
                $db->exec("INSERT OR REPLACE INTO categories (id, name, slug) VALUES (" . 
                    $cat['id'] . ", '" . $cat['name'] . "', '" . ($cat['slug'] ?? '') . "')");
            }
        }
    }
    
    // 迁移文章
    if (file_exists(FILE_POSTS)) {
        $posts = json_decode(file_get_contents(FILE_POSTS), true);
        if ($posts) {
            foreach ($posts as $post) {
                $db->exec("INSERT OR REPLACE INTO posts (id, title, slug, content, category_id, status, created_at, updated_at) VALUES (" . 
                    $post['id'] . ", '" . str_replace("'", "''", $post['title']) . "', '" . $post['slug'] . "', '" . 
                    str_replace("'", "''", $post['content']) . "', " . ($post['category_id'] ?? 0) . ", '" . 
                    ($post['status'] ?? 'published') . "', '" . ($post['created_at'] ?? '') . "', '" . 
                    ($post['updated_at'] ?? '') . "')");
            }
        }
    }
    
    // 迁移评论
    if (file_exists(FILE_COMMENTS)) {
        $comments = json_decode(file_get_contents(FILE_COMMENTS), true);
        if ($comments) {
            foreach ($comments as $comment) {
                $db->exec("INSERT INTO comments (id, post_id, author_name, content, is_approved, created_at) VALUES (" . 
                    $comment['id'] . ", '" . $comment['post_id'] . "', '" . $comment['author_name'] . "', '" . 
                    str_replace("'", "''", $comment['content']) . "', " . ($comment['is_approved'] ?? 0) . ", '" . 
                    ($comment['created_at'] ?? '') . "')");
            }
        }
    }
}

?>

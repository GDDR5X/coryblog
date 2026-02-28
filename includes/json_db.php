<?php
// includes/json_db.php

// 如果有SQLite扩展，不定义任何函数（直接使用db.php中的函数）
if (extension_loaded('pdo_sqlite')) {
    return;
}

// Define data file paths (for backward compatibility)
define('DATA_DIR', __DIR__ . '/../data/');
define('FILE_POSTS', DATA_DIR . 'posts.json');
define('FILE_USERS', DATA_DIR . 'users.json');
define('FILE_CATEGORIES', DATA_DIR . 'categories.json');
define('FILE_COMMENTS', DATA_DIR . 'comments.json');

// Ensure data directory exists
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

// Helper to read JSON file
function read_json($file) {
    if (!file_exists($file)) {
        return [];
    }
    $json = file_get_contents($file);
    return json_decode($json, true) ?? [];
}

// Helper to write JSON file
function write_json($file, $data) {
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Helper to get next ID
function get_next_id($data) {
    if (empty($data)) {
        return 1;
    }
    $ids = array_column($data, 'id');
    return max($ids) + 1;
}

// Data Access Functions - JSON storage implementation

// --- Users ---
function get_user_by_username($username) {
    $users = read_json(FILE_USERS);
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}

function get_user_by_id($id) {
    $users = read_json(FILE_USERS);
    foreach ($users as $user) {
        if ($user['id'] == $id) {
            return $user;
        }
    }
    return null;
}

function add_user($username, $password, $email = '') {
    $users = read_json(FILE_USERS);
    $new_user = [
        'id' => get_next_id($users),
        'username' => $username,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'email' => $email,
        'created_at' => date('Y-m-d H:i:s')
    ];
    $users[] = $new_user;
    write_json(FILE_USERS, $users);
    return $new_user;
}

// --- Categories ---
function get_all_categories() {
    return read_json(FILE_CATEGORIES);
}

function get_category_name($id) {
    $categories = read_json(FILE_CATEGORIES);
    foreach ($categories as $category) {
        if ($category['id'] == $id) {
            return $category['name'];
        }
    }
    return '未分类';
}

// --- Posts (Migrated to MD) ---
function get_all_posts($status = null) {
    return get_all_posts_md($status);
}

function get_post_by_slug($slug) {
    return get_post_by_slug_md($slug);
}

function get_post_by_id($id) {
    return get_post_by_slug($id);
}

function save_post($post_data) {
    return save_post_md($post_data);
}

function delete_post($id) {
    return delete_post_md($id);
}

// --- Comments ---
function get_comments_by_post($post_id, $approved_only = true) {
    $comments = read_json(FILE_COMMENTS);
    $result = [];
    foreach ($comments as $comment) {
        if ($comment['post_id'] === $post_id) {
            if (!$approved_only || $comment['is_approved']) {
                $result[] = $comment;
            }
        }
    }
    return $result;
}

function add_comment($post_id, $author, $content) {
    $comments = read_json(FILE_COMMENTS);
    $new_comment = [
        'id' => get_next_id($comments),
        'post_id' => $post_id,
        'author_name' => $author,
        'content' => $content,
        'is_approved' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ];
    $comments[] = $new_comment;
    write_json(FILE_COMMENTS, $comments);
    return $new_comment;
}
?>

<?php
// includes/json_db.php

// Define data file paths
define('DATA_DIR', __DIR__ . '/../data/');
define('FILE_POSTS', DATA_DIR . 'posts.json');
define('FILE_USERS', DATA_DIR . 'users.json');
define('FILE_CATEGORIES', DATA_DIR . 'categories.json');
define('FILE_COMMENTS', DATA_DIR . 'comments.json');

// Ensure data directory exists
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
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

// Data Access Functions

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
    
    // Check if username exists
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return false; // Username exists
        }
    }

    $new_user = [
        'id' => get_next_id($users),
        'username' => $username,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'email' => $email,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $users[] = $new_user;
    write_json(FILE_USERS, $users);
    return true;
}

// --- Categories ---
function get_all_categories() {
    return read_json(FILE_CATEGORIES);
}

function get_category_name($id) {
    $categories = get_all_categories();
    foreach ($categories as $cat) {
        if ($cat['id'] == $id) {
            return $cat['name'];
        }
    }
    return 'Uncategorized';
}

// --- Posts (Migrated to MD) ---
function get_all_posts($status = null) {
    return get_all_posts_md($status);
}

function get_post_by_slug($slug) {
    return get_post_by_slug_md($slug);
}

function get_post_by_id($id) {
    return get_post_by_id_md($id);
}

function save_post($post_data) {
    // Handle slug change (rename)
    // $post_data['id'] holds the old slug (if editing)
    if (isset($post_data['id']) && $post_data['id'] && $post_data['id'] !== $post_data['slug']) {
        // Delete old MD file
        delete_post_md($post_data['id']);
        
        // Update comments linked to old slug
        $comments = read_json(FILE_COMMENTS);
        $updated = false;
        foreach ($comments as &$c) {
            if ($c['post_id'] == $post_data['id']) {
                $c['post_id'] = $post_data['slug'];
                $updated = true;
            }
        }
        if ($updated) {
            write_json(FILE_COMMENTS, $comments);
        }
    }
    
    return save_post_md($post_data);
}

function delete_post($id) {
    // $id is the slug
    if (delete_post_md($id)) {
        // Also delete comments for this post
        $comments = read_json(FILE_COMMENTS);
        $comments = array_filter($comments, function($c) use ($id) {
            return $c['post_id'] != $id;
        });
        write_json(FILE_COMMENTS, array_values($comments));
        return true;
    }
    return false;
}

// --- Comments ---
function get_comments_by_post($post_id, $approved_only = true) {
    $comments = read_json(FILE_COMMENTS);
    $result = [];
    foreach ($comments as $c) {
        // post_id is now a string (slug)
        if ($c['post_id'] == $post_id) {
            if ($approved_only && $c['is_approved'] != 1) {
                continue;
            }
            $result[] = $c;
        }
    }
    // Sort by created_at desc
    usort($result, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    return $result;
}

function add_comment($post_id, $author, $content) {
    $comments = read_json(FILE_COMMENTS);
    $new_comment = [
        'id' => get_next_id($comments),
        'post_id' => $post_id, // This will be the slug
        'author_name' => $author,
        'content' => $content,
        'is_approved' => 0, // Default pending
        'created_at' => date('Y-m-d H:i:s')
    ];
    $comments[] = $new_comment;
    write_json(FILE_COMMENTS, $comments);
    return true;
}
?>

<?php
// includes/md_db.php

// Define directory for markdown posts
define('POSTS_DIR', __DIR__ . '/../posts/');

if (!is_dir(POSTS_DIR)) {
    mkdir(POSTS_DIR, 0777, true);
}

// Parse Front Matter and Content
function parse_md_file($filepath) {
    if (!file_exists($filepath)) {
        return null;
    }
    
    $content = file_get_contents($filepath);
    
    // Check for Front Matter
    if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $content, $matches)) {
        $front_matter_raw = $matches[1];
        $body = $matches[2];
        
        $meta = [];
        $lines = explode("\n", $front_matter_raw);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                // Remove quotes if present
                $value = trim($value, '"\'');
                $meta[$key] = $value;
            }
        }
        
        return array_merge($meta, ['content' => $body]);
    } else {
        // No front matter, treat whole file as content
        return ['content' => $content];
    }
}

// Get all posts from MD files
function get_all_posts_md($status = null) {
    $files = glob(POSTS_DIR . '*.md');
    $posts = [];
    
    foreach ($files as $file) {
        $slug = basename($file, '.md');
        $data = parse_md_file($file);
        
        if ($data) {
            $data['slug'] = $slug;
            // Default fields if missing
            $data['title'] = $data['title'] ?? ucfirst(str_replace('-', ' ', $slug));
            $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s', filemtime($file));
            $data['category_id'] = $data['category_id'] ?? 0;
            $data['status'] = $data['status'] ?? 'published';
            $data['id'] = $slug; // ID is now the slug for MD files
            
            if ($status && $data['status'] !== $status) {
                continue;
            }
            
            $posts[] = $data;
        }
    }
    
    // Sort by created_at desc
    usort($posts, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    return $posts;
}

function get_post_by_slug_md($slug) {
    // Sanitize slug to prevent directory traversal
    $slug = basename($slug);
    $file = POSTS_DIR . $slug . '.md';
    
    if (file_exists($file)) {
        $data = parse_md_file($file);
        if ($data) {
            $data['slug'] = $slug;
            $data['id'] = $slug;
            $data['title'] = $data['title'] ?? ucfirst(str_replace('-', ' ', $slug));
            $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s', filemtime($file));
            $data['category_id'] = $data['category_id'] ?? 0;
            $data['status'] = $data['status'] ?? 'published';
            return $data;
        }
    }
    return null;
}

// Used for admin editing where we might look up by ID (which is slug now)
function get_post_by_id_md($id) {
    return get_post_by_slug_md($id);
}

function save_post_md($post_data) {
    $slug = $post_data['slug'];
    // Ensure unique slug if new? MD files overwrite if name same.
    // If user changes slug, we might need to delete old file?
    // For simplicity, we assume slug is the identifier.
    
    $file = POSTS_DIR . $slug . '.md';
    
    $meta = [
        'title' => $post_data['title'],
        'created_at' => $post_data['created_at'] ?? date('Y-m-d H:i:s'),
        'category_id' => $post_data['category_id'],
        'status' => $post_data['status']
    ];
    
    // Build File Content
    $content = "---\n";
    foreach ($meta as $key => $value) {
        $content .= "$key: $value\n";
    }
    $content .= "---\n\n";
    $content .= $post_data['content'];
    
    return file_put_contents($file, $content) !== false;
}

function delete_post_md($slug) {
    $file = POSTS_DIR . $slug . '.md';
    if (file_exists($file)) {
        return unlink($file);
    }
    return false;
}
?>

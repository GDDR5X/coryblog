<?php
// includes/config.php

// Basic site configuration
define('SITE_NAME', 'Coryzenの博客'); // Translated Site Name
define('SITE_URL', 'http://localhost:8000'); // Adjust this for production

// 根据SQLite扩展状态加载不同的数据库层
if (extension_loaded('pdo_sqlite')) {
    // 有SQLite扩展，使用SQLite存储
    require_once __DIR__ . '/db.php';
} else {
    // 没有SQLite扩展，使用JSON存储
    require_once __DIR__ . '/json_db.php';
}

// Include MD Database Layer (for Markdown files)
require_once __DIR__ . '/md_db.php';
// Include Parsedown
require_once __DIR__ . '/Parsedown.php';

// Helper function to get base URL
function get_base_url() {
    return SITE_URL;
}

// Helper for escaping HTML
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Helper for rendering Markdown
function render_markdown($text) {
    $Parsedown = new Parsedown();
    return $Parsedown->text($text);
}
?>

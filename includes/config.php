<?php
// includes/config.php

// Basic site configuration
define('SITE_NAME', 'Coryzenの博客'); // Translated Site Name

// 自动检测环境并设置正确的URL
function get_site_url() {
    // 生产环境配置 - 请根据实际情况修改
    $production_url = 'https://yourdomain.com'; // 替换为您的实际域名
    
    // 检测是否为生产环境
    $is_production = isset($_SERVER['HTTP_HOST']) && 
                   $_SERVER['HTTP_HOST'] !== 'localhost' && 
                   $_SERVER['HTTP_HOST'] !== '127.0.0.1' &&
                   strpos($_SERVER['HTTP_HOST'], '.ngrok') === false;
    
    if ($is_production) {
        return $production_url;
    }
    
    // 本地开发环境
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
    return $protocol . '://' . $host;
}

define('SITE_URL', get_site_url()); // 自动适应环境

// SMTP 服务器配置 
define('SMTP_HOST', 'smtp.qq.com');         // SMTP服务器地址 
define('SMTP_PORT', 465);                 // SMTP端口（SSL: 465, TLS: 587, 非加密: 25） 
define('SMTP_USERNAME', 'czhdqqyx6044@qq.com');  // 发件人邮箱 
define('SMTP_PASSWORD', 'nmffdqnmoaxeebgd');     // 邮箱授权码（不是登录密码） 
define('SMTP_ENCRYPTION', 'ssl');         // 加密方式：ssl、tls 或空字符串（非加密） 
define('SMTP_AUTH', true);               // 是否启用SMTP认证 

// 发件人信息 
define('MAIL_FROM_EMAIL', 'czhdqqyx6044@qq.com'); 
define('MAIL_FROM_NAME', 'Coryzen');

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

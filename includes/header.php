<?php
// includes/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? h($page_title) . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c:wght@300;400;500;700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Mermaid CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.css">
    
    <!-- KaTeX CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
    
    <!-- Highlight.js CSS -->
    <link rel="stylesheet" href="assets/libs/highlight-monokai.min.css">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="index.php" class="logo">
                    <i class="fa-solid fa-cloud-moon"></i> <?php echo SITE_NAME; ?>
                </a>
                <ul class="nav-links">
                    <li><a href="index.php">首页</a></li>
                    <li><a href="archive.php">归档</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="admin/index.php">控制台</a></li>
                        <li><a href="admin/logout.php">退出</a></li>
                    <?php else: ?>
                        <li><a href="admin/login.php">登录</a></li>
                        <li><a href="admin/register.php">注册</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <?php if (isset($custom_layout) && $custom_layout): ?>
    <main>
    <?php else: ?>
    <main class="container">
    <?php endif; ?>

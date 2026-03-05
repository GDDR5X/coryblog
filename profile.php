<?php
// profile.php - User profile page
session_start();

// 处理邮件验证提交
if (isset($_POST['resend_verification'])) {
    require_once 'includes/config.php';
    require_once 'includes/functions.php';
    
    if (!is_logged_in()) {
        header('Location: admin/login.php');
        exit;
    }
    
    $user = get_logged_in_user();
    $result = send_email_verification($user['email'], $user['username']);
    $_SESSION['message'] = $result['success'] ? '验证邮件已发送，请检查您的邮箱' : '发送失败：' . $result['message'];
    $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';
    
    // 确保没有输出
    ob_clean();
    header('Location: profile.php');
    exit;
}

// 处理注销请求
if (isset($_POST['logout'])) {
    require_once 'includes/config.php';
    require_once 'includes/functions.php';
    
    if (!is_logged_in()) {
        header('Location: admin/login.php');
        exit;
    }
    
    // 清除会话
    session_destroy();
    
    // 确保没有输出
    ob_clean();
    header('Location: admin/login.php');
    exit;
}

// 处理邮件验证确认
if (isset($_GET['verify_email'])) {
    require_once 'includes/config.php';
    require_once 'includes/functions.php';
    
    if (!is_logged_in()) {
        header('Location: admin/login.php');
        exit;
    }
    
    $token = $_GET['verify_email'];
    $result = verify_email($token);
    $_SESSION['message'] = $result['success'] ? '邮箱验证成功！' : '验证失败：' . $result['message'];
    $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';
    
    // 确保没有输出
    ob_clean();
    header('Location: profile.php');
    exit;
}

// 正常页面加载
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: admin/login.php');
    exit;
}

$user = get_logged_in_user();
$user_id = $user['id'];

// 获取用户评论
$comments = get_user_comments($user_id);

// 获取用户文章（如果有）
$articles = get_user_articles($user_id);

// 从会话中获取消息
$message = $_SESSION['message'] ?? null;
$message_type = $_SESSION['message_type'] ?? 'error';

// 清除会话中的消息
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人中心 - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <div class="profile-section">
            <h2>个人中心</h2>
            
            <?php if (isset($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo h($message); ?>
                </div>
            <?php endif; ?>
            
            <div class="profile-info">
                <h3>个人信息</h3>
                <ul>
                    <li><strong>用户名：</strong><?php echo h($user['username']); ?></li>
                    <li><strong>邮箱：</strong><?php echo h($user['email']); ?></li>
                    <li><strong>邮箱状态：</strong>
                        <?php if ($user['email_verified']): ?>
                            <span class="status verified">已验证</span>
                        <?php else: ?>
                            <span class="status unverified">未验证</span>
                        <?php endif; ?>
                    </li>
                    <li><strong>注册时间：</strong><?php echo h($user['created_at']); ?></li>
                </ul>
                
                <?php if (!$user['email_verified']): ?>
                    <form method="post" class="verify-form">
                        <button type="submit" name="resend_verification" class="btn primary">
                            <i class="fas fa-envelope"></i> 发送验证邮件
                        </button>
                    </form>
                <?php endif; ?>
                
                <form method="post" class="logout-form" onsubmit="return confirm('确定要退出登录吗？');">
                    <button type="submit" name="logout" class="btn danger">
                        <i class="fas fa-sign-out-alt"></i> 退出登录
                    </button>
                </form>
            </div>
            
            <div class="profile-activity">
                <h3>我的活动</h3>
                
                <div class="activity-section">
                    <h4>我的评论</h4>
                    <?php if (empty($comments)): ?>
                        <p>您还没有发表过评论</p>
                    <?php else: ?>
                        <ul class="comment-list">
                            <?php foreach ($comments as $comment): ?>
                                <li>
                                    <a href="post.php?id=<?php echo h($comment['post_id']); ?>">
                                        <?php echo h($comment['content']); ?>
                                    </a>
                                    <span class="comment-date"><?php echo h($comment['created_at']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                
                <div class="activity-section">
                    <h4>我的文章</h4>
                    <?php if (empty($articles)): ?>
                        <p>您还没有发表过文章</p>
                    <?php else: ?>
                        <ul class="article-list">
                            <?php foreach ($articles as $article): ?>
                                <li>
                                    <a href="post.php?id=<?php echo h($article['id']); ?>">
                                        <?php echo h($article['title']); ?>
                                    </a>
                                    <span class="article-date"><?php echo h($article['created_at']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
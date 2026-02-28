<?php
// admin/login.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

session_start();

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "无效的请求。";
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if ($username && $password) {
            // Use JSON DB function
            $user = get_user_by_username($username);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                redirect('index.php');
            } else {
                $error = "用户名或密码错误。";
            }
        } else {
            $error = "请填写所有字段。";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-card { width: 100%; max-width: 400px; padding: 2rem; }
    </style>
</head>
<body>
    <div class="card login-card">
        <h2 style="text-align: center; color: var(--primary-dark); margin-bottom: 20px;">管理员登录</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom: 15px;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label>用户名</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>密码</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" style="width: 100%;">登录</button>
        </form>
        <p style="text-align: center; margin-top: 15px; font-size: 0.9rem;">
            还没有账号？ <a href="register.php" style="color: var(--secondary-color); font-weight: bold;">点击注册</a>
        </p>
        <p style="text-align: center; margin-top: 10px; font-size: 0.9rem;">
            <a href="../index.php">返回首页</a>
        </p>
    </div>
</body>
</html>

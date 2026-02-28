<?php
// admin/register.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

session_start();

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = trim($_POST['email']);

    if ($username && $password && $confirm_password) {
        if ($password !== $confirm_password) {
            $error = "两次输入的密码不一致。";
        } elseif (strlen($password) < 6) {
            $error = "密码长度至少需要6位。";
        } else {
            // Attempt to register
            if (get_user_by_username($username)) {
                $error = "用户名已存在。";
            } else {
                if (add_user($username, $password, $email)) {
                    $success = "注册成功！你现在可以登录了。";
                } else {
                    $error = "注册失败，请重试。";
                }
            }
        }
    } else {
        $error = "请填写所有必填项。";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册 - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-card { width: 100%; max-width: 400px; padding: 2rem; }
    </style>
</head>
<body>
    <div class="card login-card">
        <h2 style="text-align: center; color: var(--primary-dark); margin-bottom: 20px;">注册账号</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom: 15px;"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success" style="margin-bottom: 15px;">
                <?php echo $success; ?>
                <div style="margin-top: 10px; text-align: center;">
                    <a href="login.php" style="font-weight: bold; text-decoration: underline;">前往登录</a>
                </div>
            </div>
        <?php else: ?>
            <form method="post">
                <div class="form-group">
                    <label>用户名 <span style="color:red">*</span></label>
                    <input type="text" name="username" required value="<?php echo isset($username) ? h($username) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>邮箱 (可选)</label>
                    <input type="email" name="email" value="<?php echo isset($email) ? h($email) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>密码 <span style="color:red">*</span></label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>确认密码 <span style="color:red">*</span></label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" style="width: 100%;">注册</button>
            </form>
        <?php endif; ?>
        
        <p style="text-align: center; margin-top: 15px; font-size: 0.9rem;">
            已有账号？ <a href="login.php" style="color: var(--secondary-color); font-weight: bold;">点击登录</a>
        </p>
        <p style="text-align: center; margin-top: 10px; font-size: 0.9rem;">
            <a href="../index.php">返回首页</a>
        </p>
    </div>
</body>
</html>

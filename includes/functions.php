<?php
// includes/functions.php

function format_date($date_string) {
    return date("F j, Y", strtotime($date_string));
}

function get_excerpt($content, $length = 150) {
    $text = strip_tags($content);
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        redirect('../admin/login.php');
    }
}

// CSRF Token Functions
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . h($token) . '">';
}

function get_logged_in_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    $user_id = $_SESSION['user_id'];
    $users_file = __DIR__ . '/../data/users.json';
    $users = json_decode(file_get_contents($users_file), true);
    
    foreach ($users as $user) {
        if ($user['id'] == $user_id) {
            return $user;
        }
    }
    
    return null;
}

function get_user_comments($user_id) {
    $comments_file = __DIR__ . '/../data/comments.json';
    $comments = json_decode(file_get_contents($comments_file), true);
    $user_comments = [];
    
    foreach ($comments as $comment) {
        if (isset($comment['user_id']) && $comment['user_id'] == $user_id) {
            $user_comments[] = $comment;
        }
    }
    
    return array_reverse($user_comments);
}

function get_user_articles($user_id) {
    // 这里可以根据实际的文章存储方式实现
    // 暂时返回空数组
    return [];
}

function send_email_verification($email, $username) {
    require_once 'assets/libs/phpmailer/PHPMailer.php';
    require_once 'assets/libs/phpmailer/SMTP.php';
    require_once 'assets/libs/phpmailer/Exception.php';
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = SMTP_AUTH;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port = SMTP_PORT;
        
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($email, $username);
        
        $token = bin2hex(random_bytes(32));
        $token_expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $verify_url = SITE_URL . '/profile.php?verify_email=' . $token;
        
        // 更新用户的验证令牌和过期时间
        $users_file = __DIR__ . '/../data/users.json';
        $users = json_decode(file_get_contents($users_file), true);
        foreach ($users as &$user) {
            if ($user['email'] == $email) {
                $user['email_verification_token'] = $token;
                $user['email_verification_token_expires'] = $token_expires;
                break;
            }
        }
        file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
        
        $mail->isHTML(true);
        $mail->Subject = '=?UTF-8?B?'.base64_encode('邮箱验证 - ' . SITE_NAME).'?=';
        
        // 美化的邮件模板
        $mail->Body = '
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-wrapper {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #ffb6c1 0%, #87cefa 100%);
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #fff;
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #87cefa 0%, #ffb6c1 100%);
            color: white;
            padding: 15px 40px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(135, 206, 250, 0.4);
            transition: all 0.3s ease;
        }
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(135, 206, 250, 0.6);
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #87cefa;
            padding: 15px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .info-box p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #e9ecef;
        }
        .footer a {
            color: #87cefa;
            text-decoration: none;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="header">
                <span class="icon">✉️</span>
                <h1>邮箱验证</h1>
            </div>
            <div class="content">
                <p class="greeting">亲爱的 <strong>' . h($username) . '</strong>：</p>
                <p class="message">
                    感谢您注册 <strong>' . SITE_NAME . '</strong>！<br>
                    为了确保账户安全，我们需要验证您的邮箱地址。请点击下方按钮完成验证：
                </p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="' . h($verify_url) . '" class="verify-button">
                        <i class="fas fa-check-circle"></i> 验证邮箱
                    </a>
                </div>
                <div class="info-box">
                    <p><strong>📋 重要提示：</strong></p>
                    <p>• 此验证链接将在 <strong>24小时</strong> 后失效</p>
                    <p>• 如果您没有注册 ' . SITE_NAME . '，请忽略此邮件</p>
                    <p>• 请勿将此验证链接分享给他人</p>
                </div>
                <p class="message">
                    如果您无法点击上方按钮，请复制以下链接到浏览器地址栏：<br>
                    <a href="' . h($verify_url) . '" style="color: #87cefa; word-break: break-all;">' . h($verify_url) . '</a>
                </p>
            </div>
            <div class="footer">
                <p>此邮件由系统自动发送，请勿回复</p>
                <p>© ' . date('Y') . ' ' . SITE_NAME . ' - 保留所有权利</p>
            </div>
        </div>
    </div>
</body>
</html>';
        
        $mail->send();
        return ['success' => true, 'message' => '验证邮件已发送'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function verify_email($token) {
    $users_file = __DIR__ . '/../data/users.json';
    $users = json_decode(file_get_contents($users_file), true);
    $user_found = false;
    $token_expired = false;
    
    foreach ($users as &$user) {
        if ($user['email_verification_token'] == $token) {
            // 检查token是否过期
            if (!empty($user['email_verification_token_expires'])) {
                $expires_time = strtotime($user['email_verification_token_expires']);
                $current_time = time();
                
                if ($current_time > $expires_time) {
                    $token_expired = true;
                    break;
                }
            }
            
            // token未过期，执行验证
            $user['email_verified'] = true;
            $user['email_verification_token'] = '';
            $user['email_verification_token_expires'] = null;
            $user['email_verified_at'] = date('Y-m-d H:i:s');
            $user_found = true;
            break;
        }
    }
    
    if ($token_expired) {
        return ['success' => false, 'message' => '验证链接已过期，请重新发送验证邮件'];
    } elseif ($user_found) {
        file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
        return ['success' => true, 'message' => '邮箱验证成功'];
    } else {
        return ['success' => false, 'message' => '无效的验证令牌'];
    }
}

function is_email_verified() {
    $user = get_logged_in_user();
    return $user && $user['email_verified'];
}
?>

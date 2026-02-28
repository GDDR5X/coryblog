<?php
// router.php - 路由文件，用于阻止直接访问敏感文件

// 检查请求的文件路径
$requested_file = $_SERVER['SCRIPT_FILENAME'];
$script_name = $_SERVER['SCRIPT_NAME'];

// 禁止直接访问敏感目录
$sensitive_patterns = [
    '/data\/.*\.(json|db|sqlite)$/i',
    '/posts\/.*\.md/i',
    '/\.git/i',
    '/\.env/i',
];

foreach ($sensitive_patterns as $pattern) {
    if (preg_match($pattern, $script_name) || preg_match($pattern, $requested_file)) {
        header("HTTP/1.1 403 Forbidden");
        echo "<h1>403 - 访问被禁止</h1>";
        echo "<p>您无权访问此资源。</p>";
        exit;
    }
}

// 如果请求的是已存在的文件且不是PHP文件，则直接返回
if (file_exists($script_name) && pathinfo($script_name, PATHINFO_EXTENSION) !== 'php') {
    return false;
}

// 默认返回false，让PHP内置服务器继续处理
return false;
?>

<?php
// admin/index.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

session_start();
require_login();

// Fetch All Posts (published and draft)
$posts = get_all_posts();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>控制台 - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="../index.php" class="logo">管理后台</a>
                <ul class="nav-links">
                    <li><a href="../index.php" target="_blank">浏览站点</a></li>
                    <li><a href="logout.php">退出登录</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>文章管理</h1>
            <a href="edit.php" class="btn" style="background: var(--primary-color); color: white; padding: 10px 20px; border-radius: 20px;"><i class="fas fa-plus"></i> 写文章</a>
        </div>

        <div class="card">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px;">标题</th>
                        <th style="padding: 10px;">分类</th>
                        <th style="padding: 10px;">日期</th>
                        <th style="padding: 10px;">状态</th>
                        <th style="padding: 10px; text-align: right;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($posts) > 0): ?>
                        <?php foreach ($posts as $post): ?>
                            <tr style="border-bottom: 1px solid #f9f9f9;">
                                <td style="padding: 10px;">
                                    <a href="edit.php?id=<?php echo $post['id']; ?>" style="font-weight: bold; color: var(--primary-dark);">
                                        <?php echo h($post['title']); ?>
                                    </a>
                                </td>
                                <td style="padding: 10px;"><?php echo h(get_category_name($post['category_id'])); ?></td>
                                <td style="padding: 10px; color: #888;"><?php echo format_date($post['created_at']); ?></td>
                                <td style="padding: 10px;">
                                    <span style="padding: 4px 10px; border-radius: 10px; font-size: 0.8rem; background: <?php echo $post['status'] == 'published' ? '#d4edda' : '#fff3cd'; ?>; color: <?php echo $post['status'] == 'published' ? '#155724' : '#856404'; ?>">
                                        <?php echo $post['status'] == 'published' ? '已发布' : '草稿'; ?>
                                    </span>
                                </td>
                                <td style="padding: 10px; text-align: right;">
                                    <a href="edit.php?id=<?php echo $post['id']; ?>" style="color: var(--secondary-color); margin-right: 10px;"><i class="fas fa-edit"></i></a>
                                    <a href="delete.php?id=<?php echo $post['id']; ?>" style="color: #ff6b6b;" onclick="return confirm('确定要删除这篇文章吗？');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="padding: 20px; text-align: center; color: #999;">暂无文章。</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>

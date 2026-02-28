<?php
// admin/edit.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

session_start();
require_login();

$id = $_GET['id'] ?? 0;
$post = [
    'id' => '',
    'title' => '',
    'slug' => '',
    'content' => '',
    'category_id' => '',
    'status' => 'published'
];

$categories = get_all_categories();

if ($id) {
    $fetched_post = get_post_by_id($id);
    if ($fetched_post) {
        $post = $fetched_post;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "无效的请求。";
    } else {
        $title = trim($_POST['title']);
        $slug = trim($_POST['slug']);
        if (!$slug) {
            $slug = strtolower(str_replace(' ', '-', $title));
        }
        $content = $_POST['content'];
        $category_id = $_POST['category_id'];
        $status = $_POST['status'];

        if ($title && $content) {
            $data = [
                'id' => $id ? $id : null,
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'category_id' => $category_id,
                'status' => $status
            ];

            if (save_post($data)) {
                redirect('index.php');
            } else {
                $error = "保存文章失败。";
            }
        } else {
            $error = "标题和内容不能为空。";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? '编辑' : '新建'; ?> 文章 - 管理后台</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .editor-container { max-width: 800px; margin: 40px auto; }
        textarea { min-height: 400px; font-family: monospace; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="index.php" class="logo">管理后台</a>
                <ul class="nav-links">
                    <li><a href="index.php">仪表盘</a></li>
                    <li><a href="logout.php">退出</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container editor-container">
        <div class="card">
            <h1 style="margin-bottom: 20px;"><?php echo $id ? '编辑文章' : '新建文章'; ?></h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label>标题</label>
                    <input type="text" name="title" value="<?php echo h($post['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>别名 (URL Slug)</label>
                    <input type="text" name="slug" value="<?php echo h($post['slug']); ?>" placeholder="留空则自动根据标题生成">
                </div>

                <div class="form-group" style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <label>分类</label>
                        <select name="category_id">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $post['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo h($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label>状态</label>
                        <select name="status">
                            <option value="published" <?php echo $post['status'] == 'published' ? 'selected' : ''; ?>>发布</option>
                            <option value="draft" <?php echo $post['status'] == 'draft' ? 'selected' : ''; ?>>草稿</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>内容 (支持 Markdown)</label>
                    <textarea name="content" required><?php echo h($post['content']); ?></textarea>
                    <p style="font-size: 0.8rem; color: #999; margin-top: 5px;">你可以使用 Markdown 语法来编写内容。</p>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <a href="index.php" class="btn" style="background: #ccc; color: #333; padding: 10px 20px; border-radius: 20px;">取消</a>
                    <button type="submit">保存文章</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

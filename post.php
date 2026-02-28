<?php
// post.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

// Fetch Post from JSON
$post = get_post_by_slug($slug);

if (!$post || $post['status'] !== 'published') {
    die("文章不存在。");
}

// Handle Comment Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $author = trim($_POST['author'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $post_id = $_POST['post_id'] ?? 0;
    
    if ($author && $content && $post_id == $post['id']) {
        if (add_comment($post_id, $author, $content)) {
            $success = "评论已提交，等待审核！";
        } else {
            $error = "提交评论时出错。";
        }
    } else {
        $error = "请填写所有字段。";
    }
}

// Fetch Comments from JSON
$comments = get_comments_by_post($post['id'], true);
$category_name = get_category_name($post['category_id']);

$page_title = $post['title'];
$custom_layout = true; // Use custom layout for full width
require_once 'includes/header.php';
?>

<!-- Add TOC CSS -->
<link rel="stylesheet" href="assets/css/toc.css">

<!-- Container is removed here to allow full width control via post-container -->
<div class="post-container">
    <!-- Sidebar (TOC) -->
    <aside class="post-sidebar">
            <div class="toc-card">
                <div class="toc-title"><i class="fas fa-list-ul"></i> 目录</div>
                <ul class="toc-list">
                    <!-- JS will populate this -->
                </ul>
            </div>
        </aside>

        <!-- Main Content Column -->
        <div class="post-main">
            <article class="card post-content">
                <header class="post-header">
                    <div class="post-meta">
                        <span class="date"><i class="far fa-calendar-alt"></i> <?php echo format_date($post['created_at']); ?></span>
                        <span class="category"><i class="far fa-folder-open"></i> <?php echo h($category_name); ?></span>
                    </div>
                    <h1 class="post-title" style="font-size: 2.5rem; margin-top: 10px;"><?php echo h($post['title']); ?></h1>
                </header>
                
                <div class="content-body" style="margin-top: 30px; font-size: 1.1rem; line-height: 1.8;">
                    <?php echo render_markdown($post['content']); ?>
                </div>
                
                <div class="post-footer" style="margin-top: 40px; padding-top: 20px; border-top: 1px dashed #ddd;">
                    <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> 返回首页</a>
                </div>
            </article>

            <!-- Comments Section -->
            <div class="card comments-section" style="margin-top: 40px;">
                <h3><i class="far fa-comments"></i> 评论 (<?php echo count($comments); ?>)</h3>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Comment Form -->
                <div class="comment-form" style="margin-top: 20px; background: #f9f9f9; padding: 20px; border-radius: 10px;">
                    <h4>发表评论</h4>
                    <form method="post" action="">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <div class="form-group">
                            <label>昵称</label>
                            <input type="text" name="author" required placeholder="你的名字">
                        </div>
                        <div class="form-group">
                            <label>内容</label>
                            <textarea name="content" rows="4" required placeholder="分享你的想法..."></textarea>
                        </div>
                        <button type="submit" name="submit_comment">提交评论</button>
                    </form>
                </div>

                <!-- Comment List -->
                <div class="comment-list" style="margin-top: 30px;">
                    <?php if (count($comments) > 0): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment" style="border-bottom: 1px solid #eee; padding: 15px 0;">
                                <div class="comment-header" style="display: flex; justify-content: space-between;">
                                    <strong style="color: var(--primary-dark);"><?php echo h($comment['author_name']); ?></strong>
                                    <span style="font-size: 0.8rem; color: #999;"><?php echo format_date($comment['created_at']); ?></span>
                                </div>
                                <p style="margin-top: 5px; color: #555;"><?php echo nl2br(h($comment['content'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #999; text-align: center; margin-top: 20px;">暂无评论。快来抢沙发吧！</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add TOC JS -->
<script src="assets/js/toc.js"></script>
<script src="assets/js/highlight.js"></script>
</div>
<?php require_once 'includes/footer.php'; ?>

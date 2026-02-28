<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = "首页";
require_once 'includes/header.php';

// Get posts from JSON
$posts = get_all_posts('published');
// Limit to 10 for home page
$posts = array_slice($posts, 0, 10);
?>

<div class="hero" style="text-align: center; margin-bottom: 40px; padding: 40px 0;">
    <h1 style="color: var(--primary-dark); font-size: 2.5rem; margin-bottom: 10px;">欢迎来到我的博客~</h1>
    <p style="color: #777;">分享想法，以及生活~</p>
</div>

<div class="posts-grid">
    <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
            <?php $category_name = get_category_name($post['category_id']); ?>
            <article class="card">
                <div class="post-meta">
                    <span class="date"><i class="far fa-calendar-alt"></i> <?php echo format_date($post['created_at']); ?></span>
                    <span class="category"><i class="far fa-folder-open"></i> <?php echo h($category_name); ?></span>
                </div>
                <h2 class="post-title">
                    <a href="post.php?slug=<?php echo h($post['slug']); ?>"><?php echo h($post['title']); ?></a>
                </h2>
                <div class="post-excerpt">
                    <?php echo get_excerpt(render_markdown($post['content'])); ?>
                </div>
                <a href="post.php?slug=<?php echo h($post['slug']); ?>" class="read-more">阅读更多 <i class="fas fa-arrow-right"></i></a>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card" style="text-align: center;">
            <p>暂无文章。<a href="install.php">运行安装程序</a> 以生成数据。</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

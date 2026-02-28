<?php
// archive.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = "归档";
require_once 'includes/header.php';

// Fetch Categories from JSON
$categories = get_all_categories();

// Fetch All Published Posts from JSON
$posts = get_all_posts('published');
?>

<div class="container">
    <h1 style="color: var(--primary-dark); margin-bottom: 30px; text-align: center;">文章归档</h1>

    <div class="card">
        <h2 style="margin-bottom: 20px; border-bottom: 2px solid var(--secondary-color); padding-bottom: 10px;">分类</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <?php foreach ($categories as $cat): ?>
                <span style="background: var(--bg-color); padding: 5px 15px; border-radius: 15px; border: 1px solid #ddd;">
                    <i class="fas fa-tag" style="color: var(--primary-color);"></i> <?php echo h($cat['name']); ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="posts-list" style="margin-top: 40px;">
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <?php $category_name = get_category_name($post['category_id']); ?>
                <div class="card" style="padding: 15px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <span style="color: #999; font-size: 0.9rem; margin-right: 15px;"><?php echo format_date($post['created_at']); ?></span>
                        <a href="post.php?slug=<?php echo h($post['slug']); ?>" style="font-weight: bold; font-size: 1.1rem; color: #444; hover: color: var(--primary-dark);">
                            <?php echo h($post['title']); ?>
                        </a>
                    </div>
                    <span style="font-size: 0.8rem; background: var(--secondary-color); color: white; padding: 2px 8px; border-radius: 4px;">
                        <?php echo h($category_name); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #999;">暂无文章。</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

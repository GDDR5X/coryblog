<?php
// post.php
require_once 'includes/config.php';
require_once 'includes/functions.php';
session_start();

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
    // Validate CSRF token
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "无效的请求。";
    } elseif (is_logged_in() && !is_email_verified()) {
        $error = "请先验证您的邮箱，然后再发布评论。";
    } else {
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
                        <?php echo csrf_field(); ?>
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

<!-- Highlight.js -->
<script src="assets/libs/highlight.min.js"></script>
<script src="assets/libs/powershell.min.js"></script>

<!-- Mermaid JS -->
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script>
    mermaid.initialize({startOnLoad: true});
</script>

<!-- KaTeX JS -->
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Render math in content body (for inline and block math)
        var contentBody = document.querySelector('.content-body');
        if (contentBody && typeof renderMathInElement !== 'undefined') {
            renderMathInElement(contentBody, {
                delimiters: [
                    {left: '$$', right: '$$', display: true},
                    {left: '$', right: '$', display: false},
                    {left: '\\(', right: '\\)', display: false},
                    {left: '\\[', right: '\\]', display: true}
                ]
            });
        }
        
        // Also render math in math blocks (LaTeX code blocks)
        var mathBlocks = document.querySelectorAll('.math-block');
        mathBlocks.forEach(function(block) {
            if (typeof renderMathInElement !== 'undefined') {
                renderMathInElement(block, {
                    delimiters: [
                        {left: '$$', right: '$$', display: true},
                        {left: '$', right: '$', display: false},
                        {left: '\\(', right: '\\)', display: false},
                        {left: '\\[', right: '\\]', display: true}
                    ]
                });
            }
        });
    });
</script>

<!-- Code Toggle JS for HTML/CSS/JS Preview -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.code-toggle-btn').forEach(button => {
            button.addEventListener('click', function() {
                const toggleContainer = this.closest('.code-toggle');
                const mode = this.getAttribute('data-mode');
                
                // Update active button
                toggleContainer.querySelectorAll('.code-toggle-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Show appropriate content
                if (mode === 'code') {
                    toggleContainer.querySelector('.code-content').style.display = 'block';
                    toggleContainer.querySelector('.preview-content').style.display = 'none';
                } else {
                    toggleContainer.querySelector('.code-content').style.display = 'none';
                    toggleContainer.querySelector('.preview-content').style.display = 'block';
                }
            });
        });
    });
</script>

<!-- Highlight.js Initialization -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Highlight.js loaded:', typeof hljs !== 'undefined');
        console.log('Highlight.js version:', hljs.version);
        
        // 初始化代码高亮
        hljs.highlightAll();
        
        // 为带预览的代码块添加高亮
        document.querySelectorAll('.code-toggle .code-content pre code').forEach((block) => {
            hljs.highlightElement(block);
        });
        
        // 为复杂结构的代码块添加高亮（处理 Parsedown 生成的结构）
        document.querySelectorAll('pre[data-language] code').forEach((block) => {
            if (!block.classList.contains('hljs')) {
                hljs.highlightElement(block);
            }
        });
        
        console.log('Code blocks highlighted:', document.querySelectorAll('code.hljs').length);
        
        // 代码块复制功能
        document.querySelectorAll('.copy-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const pre = this.closest('pre');
                const code = pre.querySelector('code');
                const text = code.textContent;
                
                navigator.clipboard.writeText(text).then(() => {
                    const icon = this.querySelector('i');
                    icon.className = 'fas fa-check';
                    this.classList.add('copied');
                    
                    setTimeout(() => {
                        icon.className = 'fas fa-copy';
                        this.classList.remove('copied');
                    }, 2000);
                }).catch(err => {
                    console.error('复制失败:', err);
                });
            });
        });
        
        // 代码块折叠/展开功能
        document.querySelectorAll('.toggle-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const pre = this.closest('pre');
                pre.classList.toggle('expanded');
                
                const icon = this.querySelector('i');
                if (pre.classList.contains('expanded')) {
                    icon.className = 'fas fa-chevron-up';
                    this.title = '折叠';
                } else {
                    icon.className = 'fas fa-chevron-down';
                    this.title = '展开';
                }
            });
        });
        
        // 为超过10行的代码块添加行号
        document.querySelectorAll('pre[data-lines]').forEach(pre => {
            const lineCount = parseInt(pre.dataset.lines);
            const lineNumbers = pre.querySelector('.line-numbers');
            if (lineNumbers && lineCount > 0) {
                let numbersHtml = '';
                for (let i = 1; i <= lineCount; i++) {
                    numbersHtml += `<div class="line-number">${i}</div>`;
                }
                lineNumbers.innerHTML = numbersHtml;
            }
        });
    });
</script>
</div>
<?php require_once 'includes/footer.php'; ?>

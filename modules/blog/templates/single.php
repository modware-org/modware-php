<?php
$post = $blogQuery->getPost($slug);
if (!$post) {
    header("HTTP/1.0 404 Not Found");
    echo '<div class="error-404">Запись не найдена.</div>';
    return;
}

$config = $blogQuery->getBlogData()['config'];
?>

<article class="single-post">
    <div class="post-container">
        <!-- Post Header -->
        <header class="post-header">
            <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>

            <?php if ($config['show_date'] === 'true' || $config['show_author'] === 'true'): ?>
                <div class="post-meta">
                    <?php if ($config['show_date'] === 'true'): ?>
                        <time datetime="<?php echo date('Y-m-d', strtotime($post['published_at'])); ?>">
                            <?php echo date('d.m.Y', strtotime($post['published_at'])); ?>
                        </time>
                    <?php endif; ?>

                    <?php if ($config['show_author'] === 'true'): ?>
                        <span class="post-author">
                            <?php echo htmlspecialchars($post['author_name']); ?>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($post['featured_image']): ?>
                <div class="post-featured-image">
                    <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                         alt="<?php echo htmlspecialchars($post['title']); ?>">
                </div>
            <?php endif; ?>
        </header>

        <!-- Post Content -->
        <div class="post-content">
            <?php echo $post['content']; ?>
        </div>

        <!-- Post Footer -->
        <footer class="post-footer">
            <?php if ($config['show_categories'] === 'true' && $post['category_name']): ?>
                <div class="post-category">
                    <span class="label">Категория:</span>
                    <a href="/blog/category/<?php echo htmlspecialchars($post['category_slug']); ?>">
                        <?php echo htmlspecialchars($post['category_name']); ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($config['show_tags'] === 'true' && !empty($post['tags'])): ?>
                <div class="post-tags">
                    <span class="label">Теги:</span>
                    <?php foreach ($post['tags'] as $tag): ?>
                        <a href="/blog/tag/<?php echo htmlspecialchars($tag['slug']); ?>" 
                           class="tag-link">
                            #<?php echo htmlspecialchars($tag['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Social Share Buttons -->
            <div class="share-buttons">
                <span class="label">Поделиться:</span>
                <a href="https://vk.com/share.php?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($post['title']); ?>" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="share-button vk"
                   aria-label="Поделиться ВКонтакте">
                    <svg viewBox="0 0 24 24" width="24" height="24">
                        <path d="M12.785 16.241s.288-.032.436-.194c.136-.148.132-.427.132-.427s-.02-1.304.576-1.496c.588-.19 1.341 1.26 2.14 1.818.605.422 1.064.33 1.064.33l2.137-.03s1.117-.071.587-.964c-.043-.073-.308-.661-1.588-1.87-1.34-1.264-1.16-1.059.453-3.246.983-1.332 1.376-2.145 1.253-2.493-.117-.332-.84-.244-.84-.244l-2.406.015s-.178-.025-.31.056c-.13.079-.212.262-.212.262s-.382 1.03-.89 1.907c-1.07 1.85-1.499 1.948-1.674 1.832-.407-.267-.305-1.075-.305-1.648 0-1.793.267-2.54-.521-2.733-.262-.065-.454-.107-1.123-.114-.858-.009-1.585.003-1.996.208-.274.136-.485.44-.356.457.159.022.519.099.71.363.246.341.237 1.107.237 1.107s.142 2.11-.33 2.371c-.325.18-.77-.187-1.725-1.865-.489-.859-.859-1.81-.859-1.81s-.07-.176-.198-.272c-.154-.115-.37-.151-.37-.151l-2.286.015s-.343.01-.469.161C3.94 7.721 4.043 8 4.043 8s1.79 4.258 3.817 6.403c1.858 1.967 3.968 1.838 3.968 1.838h.957z"/>
                    </svg>
                </a>
                <a href="https://t.me/share/url?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['title']); ?>" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="share-button telegram"
                   aria-label="Поделиться в Telegram">
                    <svg viewBox="0 0 24 24" width="24" height="24">
                        <path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/>
                    </svg>
                </a>
            </div>
        </footer>

        <?php if ($config['show_comments'] === 'true'): ?>
            <!-- Comments Section -->
            <section id="comments" class="comments-section">
                <h2 class="comments-title">Комментарии</h2>

                <?php if (!empty($post['comments'])): ?>
                    <div class="comments-list">
                        <?php foreach ($post['comments'] as $comment): ?>
                            <div class="comment" id="comment-<?php echo $comment['id']; ?>">
                                <div class="comment-meta">
                                    <?php if ($comment['author_url']): ?>
                                        <a href="<?php echo htmlspecialchars($comment['author_url']); ?>" 
                                           class="comment-author" 
                                           rel="nofollow">
                                            <?php echo htmlspecialchars($comment['author_name']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="comment-author">
                                            <?php echo htmlspecialchars($comment['author_name']); ?>
                                        </span>
                                    <?php endif; ?>

                                    <time datetime="<?php echo date('Y-m-d', strtotime($comment['created_at'])); ?>">
                                        <?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?>
                                    </time>
                                </div>

                                <div class="comment-content">
                                    <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                </div>

                                <?php if (!empty($comment['replies'])): ?>
                                    <div class="comment-replies">
                                        <?php foreach ($comment['replies'] as $reply): ?>
                                            <div class="comment reply" id="comment-<?php echo $reply['id']; ?>">
                                                <div class="comment-meta">
                                                    <?php if ($reply['author_url']): ?>
                                                        <a href="<?php echo htmlspecialchars($reply['author_url']); ?>" 
                                                           class="comment-author" 
                                                           rel="nofollow">
                                                            <?php echo htmlspecialchars($reply['author_name']); ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="comment-author">
                                                            <?php echo htmlspecialchars($reply['author_name']); ?>
                                                        </span>
                                                    <?php endif; ?>

                                                    <time datetime="<?php echo date('Y-m-d', strtotime($reply['created_at'])); ?>">
                                                        <?php echo date('d.m.Y H:i', strtotime($reply['created_at'])); ?>
                                                    </time>
                                                </div>

                                                <div class="comment-content">
                                                    <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-comments">Пока нет комментариев.</p>
                <?php endif; ?>

                <!-- Comment Form -->
                <div class="comment-form-wrapper">
                    <h3>Оставить комментарий</h3>
                    <form id="commentForm" class="comment-form" onsubmit="return submitComment(this);">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <input type="hidden" name="parent_id" value="">

                        <div class="form-group">
                            <label for="author">Имя *</label>
                            <input type="text" id="author" name="author_name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="author_email" required>
                        </div>

                        <div class="form-group">
                            <label for="url">Сайт</label>
                            <input type="url" id="url" name="author_url">
                        </div>

                        <div class="form-group">
                            <label for="comment">Комментарий *</label>
                            <textarea id="comment" name="content" rows="5" required></textarea>
                        </div>

                        <button type="submit" class="submit-button">Отправить</button>
                    </form>
                </div>
            </section>
        <?php endif; ?>
    </div>
</article>

<script>
async function submitComment(form) {
    try {
        const formData = new FormData(form);
        const response = await fetch('/api/comments', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Комментарий успешно добавлен' + 
                  (<?php echo $config['moderate_comments']; ?> ? ' и ожидает модерации.' : '.'));
            form.reset();
        } else {
            alert(result.message || 'Ошибка при добавлении комментария.');
        }
    } catch (error) {
        console.error('Error submitting comment:', error);
        alert('Произошла ошибка при отправке комментария.');
    }
    
    return false;
}
</script>

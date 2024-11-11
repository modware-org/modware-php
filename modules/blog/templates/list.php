<?php
$data = $blogQuery->getBlogData($page, $category, $tag, $search);
$config = $data['config'];
?>

<section class="blog-section">
    <div class="blog-container">
        <!-- Blog Header -->
        <header class="blog-header">
            <h1 class="blog-title">
                <?php
                if ($category) {
                    echo 'Категория: ' . htmlspecialchars($category);
                } elseif ($tag) {
                    echo 'Тег: ' . htmlspecialchars($tag);
                } elseif ($search) {
                    echo 'Поиск: ' . htmlspecialchars($search);
                } else {
                    echo 'Блог';
                }
                ?>
            </h1>
        </header>

        <div class="blog-layout">
            <!-- Main Content -->
            <main class="blog-main">
                <?php if (empty($data['posts'])): ?>
                    <div class="no-posts">
                        <p>Записей не найдено.</p>
                    </div>
                <?php else: ?>
                    <div class="posts-grid">
                        <?php foreach ($data['posts'] as $post): ?>
                            <article class="post-card">
                                <?php if ($post['featured_image']): ?>
                                    <div class="post-image">
                                        <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>">
                                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($post['title']); ?>"
                                                 loading="lazy">
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="post-content">
                                    <header class="post-header">
                                        <h2 class="post-title">
                                            <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </a>
                                        </h2>

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
                                    </header>

                                    <div class="post-excerpt">
                                        <?php echo htmlspecialchars($post['excerpt']); ?>
                                    </div>

                                    <footer class="post-footer">
                                        <?php if ($config['show_categories'] === 'true' && $post['category_name']): ?>
                                            <div class="post-category">
                                                <a href="/blog/category/<?php echo htmlspecialchars($post['category_slug']); ?>">
                                                    <?php echo htmlspecialchars($post['category_name']); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($config['show_tags'] === 'true' && !empty($post['tags'])): ?>
                                            <div class="post-tags">
                                                <?php foreach ($post['tags'] as $tag): ?>
                                                    <a href="/blog/tag/<?php echo htmlspecialchars($tag['slug']); ?>" 
                                                       class="tag-link">
                                                        #<?php echo htmlspecialchars($tag['name']); ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($config['show_comments'] === 'true'): ?>
                                            <div class="post-comments-count">
                                                <a href="/blog/<?php echo htmlspecialchars($post['slug']); ?>#comments">
                                                    <?php echo $post['comments_count']; ?> комментариев
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </footer>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($data['pagination']['total_pages'] > 1): ?>
                        <nav class="pagination" aria-label="Навигация по страницам">
                            <?php
                            $currentPage = $data['pagination']['current_page'];
                            $totalPages = $data['pagination']['total_pages'];
                            $urlParams = [];
                            if ($category) $urlParams['category'] = $category;
                            if ($tag) $urlParams['tag'] = $tag;
                            if ($search) $urlParams['s'] = $search;
                            ?>

                            <?php if ($currentPage > 1): ?>
                                <?php 
                                $urlParams['page'] = $currentPage - 1;
                                $prevUrl = '/blog?' . http_build_query($urlParams);
                                ?>
                                <a href="<?php echo $prevUrl; ?>" class="pagination-prev" aria-label="Предыдущая страница">
                                    &larr; Назад
                                </a>
                            <?php endif; ?>

                            <div class="pagination-numbers">
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <?php 
                                    $urlParams['page'] = $i;
                                    $pageUrl = '/blog?' . http_build_query($urlParams);
                                    ?>
                                    <a href="<?php echo $pageUrl; ?>" 
                                       class="pagination-number <?php echo $i === $currentPage ? 'active' : ''; ?>"
                                       aria-label="Страница <?php echo $i; ?>"
                                       <?php echo $i === $currentPage ? 'aria-current="page"' : ''; ?>>
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                            </div>

                            <?php if ($currentPage < $totalPages): ?>
                                <?php 
                                $urlParams['page'] = $currentPage + 1;
                                $nextUrl = '/blog?' . http_build_query($urlParams);
                                ?>
                                <a href="<?php echo $nextUrl; ?>" class="pagination-next" aria-label="Следующая страница">
                                    Вперед &rarr;
                                </a>
                            <?php endif; ?>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </main>

            <!-- Sidebar -->
            <?php if ($config['sidebar_position'] !== 'none'): ?>
                <aside class="blog-sidebar">
                    <!-- Search -->
                    <div class="sidebar-widget search-widget">
                        <form action="/blog" method="get" class="search-form">
                            <input type="search" 
                                   name="s" 
                                   placeholder="Поиск..." 
                                   value="<?php echo htmlspecialchars($search ?? ''); ?>"
                                   aria-label="Поиск по блогу">
                            <button type="submit" aria-label="Искать">
                                <svg viewBox="0 0 24 24" width="20" height="20">
                                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" 
                                          stroke="currentColor" 
                                          stroke-width="2" 
                                          stroke-linecap="round" 
                                          stroke-linejoin="round"
                                          fill="none"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Categories -->
                    <?php if ($config['show_categories'] === 'true' && !empty($data['categories'])): ?>
                        <div class="sidebar-widget categories-widget">
                            <h3 class="widget-title">Категории</h3>
                            <ul class="categories-list">
                                <?php foreach ($data['categories'] as $cat): ?>
                                    <li>
                                        <a href="/blog/category/<?php echo htmlspecialchars($cat['slug']); ?>"
                                           class="<?php echo $category === $cat['slug'] ? 'active' : ''; ?>">
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                            <span class="post-count"><?php echo $cat['post_count']; ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Tags -->
                    <?php if ($config['show_tags'] === 'true' && !empty($data['tags'])): ?>
                        <div class="sidebar-widget tags-widget">
                            <h3 class="widget-title">Теги</h3>
                            <div class="tags-cloud">
                                <?php foreach ($data['tags'] as $t): ?>
                                    <a href="/blog/tag/<?php echo htmlspecialchars($t['slug']); ?>"
                                       class="tag-link <?php echo $tag === $t['slug'] ? 'active' : ''; ?>">
                                        #<?php echo htmlspecialchars($t['name']); ?>
                                        <span class="post-count"><?php echo $t['post_count']; ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </aside>
            <?php endif; ?>
        </div>
    </div>
</section>

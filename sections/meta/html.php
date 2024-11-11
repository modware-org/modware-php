<?php
function renderMetaTags($db, $pageId) {
    $metaQuery = new MetaQuery($db);
    $meta = $metaQuery->getMetaByPageId($pageId);
    
    if (!$meta) {
        return;
    }

    // Basic meta tags
    echo $meta['title'] ? "<title>" . htmlspecialchars($meta['title']) . "</title>\n" : "";
    echo $meta['description'] ? "<meta name=\"description\" content=\"" . htmlspecialchars($meta['description']) . "\">\n" : "";
    echo $meta['keywords'] ? "<meta name=\"keywords\" content=\"" . htmlspecialchars($meta['keywords']) . "\">\n" : "";
    echo $meta['author'] ? "<meta name=\"author\" content=\"" . htmlspecialchars($meta['author']) . "\">\n" : "";
    echo $meta['robots'] ? "<meta name=\"robots\" content=\"" . htmlspecialchars($meta['robots']) . "\">\n" : "";
    
    // Open Graph meta tags
    echo $meta['og_title'] ? "<meta property=\"og:title\" content=\"" . htmlspecialchars($meta['og_title']) . "\">\n" : "";
    echo $meta['og_description'] ? "<meta property=\"og:description\" content=\"" . htmlspecialchars($meta['og_description']) . "\">\n" : "";
    echo $meta['og_image'] ? "<meta property=\"og:image\" content=\"" . htmlspecialchars($meta['og_image']) . "\">\n" : "";
    echo $meta['og_type'] ? "<meta property=\"og:type\" content=\"" . htmlspecialchars($meta['og_type']) . "\">\n" : "";
    
    // Twitter Card meta tags
    echo $meta['twitter_card'] ? "<meta name=\"twitter:card\" content=\"" . htmlspecialchars($meta['twitter_card']) . "\">\n" : "";
    echo $meta['twitter_site'] ? "<meta name=\"twitter:site\" content=\"" . htmlspecialchars($meta['twitter_site']) . "\">\n" : "";
    echo $meta['twitter_creator'] ? "<meta name=\"twitter:creator\" content=\"" . htmlspecialchars($meta['twitter_creator']) . "\">\n" : "";
    echo $meta['twitter_title'] ? "<meta name=\"twitter:title\" content=\"" . htmlspecialchars($meta['twitter_title']) . "\">\n" : "";
    echo $meta['twitter_description'] ? "<meta name=\"twitter:description\" content=\"" . htmlspecialchars($meta['twitter_description']) . "\">\n" : "";
    echo $meta['twitter_image'] ? "<meta name=\"twitter:image\" content=\"" . htmlspecialchars($meta['twitter_image']) . "\">\n" : "";
    
    // Canonical URL
    echo $meta['canonical_url'] ? "<link rel=\"canonical\" href=\"" . htmlspecialchars($meta['canonical_url']) . "\">\n" : "";
}

<?php
// Meta data should be passed from index.php in $metaData variable
$meta = $metaData[0] ?? null;

// Fallback configuration if no meta data found
$config = [
    'site_name' => 'DBT Unity - Диалектическая поведенческая терапия',
    'site_description' => 'Комплексная ДБТ терапия в России. Профессиональная помощь в управлении эмоциями, работа с ПРЛ, БАР и другими расстройствами.',
    'contact_email' => 'info@dbt-unity.com',
    'contact_phone' => '+1 (234) 567-890',
    'keywords' => 'ДБТ, диалектическая поведенческая терапия, ПРЛ, пограничное расстройство личности, БАР, управление эмоциями, психотерапия',
    'author' => 'DBT Unity Team'
];

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php if ($meta): ?>
        <!-- Dynamic Meta Tags -->
        <title><?php echo htmlspecialchars($meta['title'] ?? $config['site_name']); ?></title>
        <meta name="description" content="<?php echo htmlspecialchars($meta['description'] ?? $config['site_description']); ?>">
        <meta name="keywords" content="<?php echo htmlspecialchars($meta['keywords'] ?? $config['keywords']); ?>">
        <meta name="author" content="<?php echo htmlspecialchars($meta['author'] ?? $config['author']); ?>">
        <?php if (!empty($meta['robots'])): ?>
            <meta name="robots" content="<?php echo htmlspecialchars($meta['robots']); ?>">
        <?php endif; ?>

        <!-- Open Graph Meta Tags -->
        <?php if (!empty($meta['og_title'])): ?>
            <meta property="og:title" content="<?php echo htmlspecialchars($meta['og_title']); ?>">
        <?php endif; ?>
        <?php if (!empty($meta['og_description'])): ?>
            <meta property="og:description" content="<?php echo htmlspecialchars($meta['og_description']); ?>">
        <?php endif; ?>
        <?php if (!empty($meta['og_image'])): ?>
            <meta property="og:image" content="<?php echo htmlspecialchars($meta['og_image']); ?>">
        <?php endif; ?>
        <?php if (!empty($meta['og_type'])): ?>
            <meta property="og:type" content="<?php echo htmlspecialchars($meta['og_type']); ?>">
        <?php endif; ?>

        <!-- Twitter Card Meta Tags -->
        <?php if (!empty($meta['twitter_card'])): ?>
            <meta name="twitter:card" content="<?php echo htmlspecialchars($meta['twitter_card']); ?>">
        <?php endif; ?>
        <?php if (!empty($meta['twitter_site'])): ?>
            <meta name="twitter:site" content="<?php echo htmlspecialchars($meta['twitter_site']); ?>">
        <?php endif; ?>
        <?php if (!empty($meta['twitter_creator'])): ?>
            <meta name="twitter:creator" content="<?php echo htmlspecialchars($meta['twitter_creator']); ?>">
        <?php endif; ?>
        <?php if (!empty($meta['twitter_title'])): ?>
            <meta name="twitter:title" content="<?php echo htmlspecialchars($meta['twitter_title']); ?>">
        <?php endif; ?>
        <?php if (!empty($meta['twitter_description'])): ?>
            <meta name="twitter:description" content="<?php echo htmlspecialchars($meta['twitter_description']); ?>">
        <?php endif; ?>
        <?php if (!empty($meta['twitter_image'])): ?>
            <meta name="twitter:image" content="<?php echo htmlspecialchars($meta['twitter_image']); ?>">
        <?php endif; ?>

        <!-- Canonical URL -->
        <?php if (!empty($meta['canonical_url'])): ?>
            <link rel="canonical" href="<?php echo htmlspecialchars($meta['canonical_url']); ?>">
        <?php endif; ?>
    <?php else: ?>
        <!-- Fallback Meta Tags -->
        <title><?php echo htmlspecialchars($config['site_name']); ?></title>
        <meta name="description" content="<?php echo htmlspecialchars($config['site_description']); ?>">
        <meta name="keywords" content="<?php echo htmlspecialchars($config['keywords']); ?>">
        <meta name="author" content="<?php echo htmlspecialchars($config['author']); ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/img/favicon.png">
    
    <!-- Styles -->
    <link rel="stylesheet" href="/styles.css">
</head>
<body>

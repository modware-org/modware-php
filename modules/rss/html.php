<?php
require_once __DIR__ . '/query.php';

// Get feed type and category from URL parameters
$feedType = $_GET['type'] ?? 'all';
$category = $_GET['category'] ?? null;

// Set content type to XML
header('Content-Type: application/rss+xml; charset=UTF-8');

// Check if gzip is requested and available
$gzip = isset($_SERVER['HTTP_ACCEPT_ENCODING']) 
        && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false;

// Get RSS query instance
$rssQuery = new RssQuery();

// Generate or get cached feed
$feed = $rssQuery->generateFeed($feedType, $category);

// If gzip is requested and compression is enabled in settings
if ($gzip) {
    // Check if we have a pre-compressed version
    $cacheKey = $feedType . ($category ? '_' . $category : '');
    $gzFile = __DIR__ . '/../../cache/rss/feed_' . $cacheKey . '.xml.gz';
    
    if (file_exists($gzFile)) {
        header('Content-Encoding: gzip');
        readfile($gzFile);
    } else {
        // Compress on the fly if no pre-compressed version exists
        header('Content-Encoding: gzip');
        echo gzencode($feed, 9);
    }
} else {
    // Output uncompressed
    echo $feed;
}

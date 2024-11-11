<?php
require_once __DIR__ . '/query.php';

// Set content type to XML
header('Content-Type: application/xml; charset=UTF-8');

// Check if gzip is requested and available
$gzip = isset($_SERVER['HTTP_ACCEPT_ENCODING']) 
        && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false;

// Get sitemap query instance
$sitemapQuery = new SitemapQuery();

// Generate or get cached sitemap
$sitemap = $sitemapQuery->generateSitemap();

// If gzip is requested and compression is enabled in settings
if ($gzip) {
    // Check if we have a pre-compressed version
    $gzFile = __DIR__ . '/../../cache/sitemap/sitemap.xml.gz';
    if (file_exists($gzFile)) {
        header('Content-Encoding: gzip');
        readfile($gzFile);
    } else {
        // Compress on the fly if no pre-compressed version exists
        header('Content-Encoding: gzip');
        echo gzencode($sitemap, 9);
    }
} else {
    // Output uncompressed
    echo $sitemap;
}

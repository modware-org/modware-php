<?php
require_once __DIR__ . '/query.php';

$blogQuery = new BlogQuery();
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$category = $_GET['category'] ?? null;
$tag = $_GET['tag'] ?? null;
$search = $_GET['s'] ?? null;
$slug = $_GET['post'] ?? null;

if ($slug) {
    include __DIR__ . '/templates/single.php';
} else {
    include __DIR__ . '/templates/list.php';
}

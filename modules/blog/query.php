<?php
require_once __DIR__ . '/../../config/Database.php';

class BlogQuery {
    private $db;
    private $postsPerPage;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->loadConfig();
    }

    private function loadConfig() {
        $result = $this->db->getConnection()->query(
            "SELECT value FROM config WHERE name = 'blog_posts_per_page' LIMIT 1"
        );
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $this->postsPerPage = intval($row['value'] ?? 10);
    }

    public function getBlogData($page = 1, $category = null, $tag = null, $search = null) {
        $data = [
            'config' => $this->getConfig(),
            'posts' => $this->getPosts($page, $category, $tag, $search),
            'categories' => $this->getCategories(),
            'tags' => $this->getTags(),
            'pagination' => $this->getPagination($page, $category, $tag, $search)
        ];
        return $data;
    }

    private function getConfig() {
        $config = [];
        $result = $this->db->getConnection()->query(
            "SELECT name, value FROM config WHERE name LIKE 'blog_%'"
        );
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $key = str_replace('blog_', '', $row['name']);
            $config[$key] = $row['value'];
        }
        
        return $config;
    }

    private function getPosts($page = 1, $category = null, $tag = null, $search = null) {
        $offset = ($page - 1) * $this->postsPerPage;
        $params = [];
        
        $query = "SELECT p.*, 
                        u.username as author_name,
                        c.name as category_name,
                        c.slug as category_slug
                 FROM blog_posts p
                 LEFT JOIN users u ON p.author_id = u.id
                 LEFT JOIN menu_categories c ON p.category_id = c.id
                 WHERE p.status = 'published'";

        if ($category) {
            $query .= " AND c.slug = :category";
            $params[':category'] = $category;
        }

        if ($tag) {
            $query .= " AND p.id IN (
                SELECT post_id FROM blog_post_tags pt
                JOIN tags t ON pt.tag_id = t.id
                WHERE t.slug = :tag
            )";
            $params[':tag'] = $tag;
        }

        if ($search) {
            $query .= " AND (p.title LIKE :search OR p.content LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $query .= " ORDER BY p.published_at DESC LIMIT :limit OFFSET :offset";
        $params[':limit'] = $this->postsPerPage;
        $params[':offset'] = $offset;

        $stmt = $this->db->getConnection()->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $result = $stmt->execute();
        $posts = [];

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $post = $row;
            $post['tags'] = $this->getPostTags($row['id']);
            $post['comments_count'] = $this->getCommentsCount($row['id']);
            $posts[] = $post;
        }

        return $posts;
    }

    private function getPostTags($postId) {
        $tags = [];
        $query = "SELECT t.* FROM tags t
                 JOIN blog_post_tags pt ON t.id = pt.tag_id
                 WHERE pt.post_id = :post_id";
        
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':post_id', $postId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $tags[] = $row;
        }
        
        return $tags;
    }

    private function getCommentsCount($postId) {
        $query = "SELECT COUNT(*) as count FROM blog_comments 
                 WHERE post_id = :post_id AND status = 'approved'";
        
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':post_id', $postId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        
        return intval($row['count']);
    }

    private function getCategories() {
        $categories = [];
        $query = "SELECT c.*, COUNT(p.id) as post_count 
                 FROM menu_categories c
                 LEFT JOIN blog_posts p ON c.id = p.category_id AND p.status = 'published'
                 GROUP BY c.id
                 HAVING post_count > 0
                 ORDER BY c.name ASC";
        
        $result = $this->db->getConnection()->query($query);
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $categories[] = $row;
        }
        
        return $categories;
    }

    private function getTags() {
        $tags = [];
        $query = "SELECT t.*, COUNT(pt.post_id) as post_count 
                 FROM tags t
                 LEFT JOIN blog_post_tags pt ON t.id = pt.tag_id
                 LEFT JOIN blog_posts p ON pt.post_id = p.id AND p.status = 'published'
                 GROUP BY t.id
                 HAVING post_count > 0
                 ORDER BY post_count DESC";
        
        $result = $this->db->getConnection()->query($query);
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $tags[] = $row;
        }
        
        return $tags;
    }

    private function getPagination($page, $category = null, $tag = null, $search = null) {
        $params = [];
        $query = "SELECT COUNT(*) as total FROM blog_posts p";
        
        if ($category) {
            $query .= " JOIN menu_categories c ON p.category_id = c.id AND c.slug = :category";
            $params[':category'] = $category;
        }

        if ($tag) {
            $query .= " JOIN blog_post_tags pt ON p.id = pt.post_id
                       JOIN tags t ON pt.tag_id = t.id AND t.slug = :tag";
            $params[':tag'] = $tag;
        }

        if ($search) {
            $query .= " WHERE (p.title LIKE :search OR p.content LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $stmt = $this->db->getConnection()->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        $total = $row['total'];
        
        return [
            'current_page' => $page,
            'total_pages' => ceil($total / $this->postsPerPage),
            'total_posts' => $total,
            'posts_per_page' => $this->postsPerPage
        ];
    }

    public function getPost($slug) {
        $query = "SELECT p.*, 
                        u.username as author_name,
                        c.name as category_name,
                        c.slug as category_slug
                 FROM blog_posts p
                 LEFT JOIN users u ON p.author_id = u.id
                 LEFT JOIN menu_categories c ON p.category_id = c.id
                 WHERE p.slug = :slug AND p.status = 'published'
                 LIMIT 1";
        
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':slug', $slug, SQLITE3_TEXT);
        $result = $stmt->execute();
        
        if ($post = $result->fetchArray(SQLITE3_ASSOC)) {
            $post['tags'] = $this->getPostTags($post['id']);
            $post['comments'] = $this->getComments($post['id']);
            return $post;
        }
        
        return null;
    }

    private function getComments($postId, $parentId = null) {
        $comments = [];
        $query = "SELECT * FROM blog_comments 
                 WHERE post_id = :post_id 
                 AND parent_id " . ($parentId === null ? "IS NULL" : "= :parent_id") . "
                 AND status = 'approved'
                 ORDER BY created_at ASC";
        
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':post_id', $postId, SQLITE3_INTEGER);
        if ($parentId !== null) {
            $stmt->bindValue(':parent_id', $parentId, SQLITE3_INTEGER);
        }
        
        $result = $stmt->execute();
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $comment = $row;
            $comment['replies'] = $this->getComments($postId, $row['id']);
            $comments[] = $comment;
        }
        
        return $comments;
    }

    public function addComment($data) {
        $stmt = $this->db->getConnection()->prepare(
            "INSERT INTO blog_comments 
             (post_id, parent_id, author_name, author_email, author_url, content, status, ip_address, user_agent) 
             VALUES (:post_id, :parent_id, :author_name, :author_email, :author_url, :content, :status, :ip_address, :user_agent)"
        );
        
        $status = $this->getConfig()['moderate_comments'] === 'true' ? 'pending' : 'approved';
        
        $stmt->bindValue(':post_id', $data['post_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':parent_id', $data['parent_id'], $data['parent_id'] === null ? SQLITE3_NULL : SQLITE3_INTEGER);
        $stmt->bindValue(':author_name', $data['author_name'], SQLITE3_TEXT);
        $stmt->bindValue(':author_email', $data['author_email'], SQLITE3_TEXT);
        $stmt->bindValue(':author_url', $data['author_url'] ?? '', SQLITE3_TEXT);
        $stmt->bindValue(':content', $data['content'], SQLITE3_TEXT);
        $stmt->bindValue(':status', $status, SQLITE3_TEXT);
        $stmt->bindValue(':ip_address', $_SERVER['REMOTE_ADDR'], SQLITE3_TEXT);
        $stmt->bindValue(':user_agent', $_SERVER['HTTP_USER_AGENT'], SQLITE3_TEXT);
        
        return $stmt->execute();
    }
}

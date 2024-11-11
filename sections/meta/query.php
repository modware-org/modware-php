<?php
class MetaQuery {
    private $db;

    public function __construct($db) {
        // Store the database connection
        $this->db = $db;
    }

    public function getMetaByPageId($pageId = null) {
        // If no page_id provided, return null
        if ($pageId === null) {
            error_log("No page_id provided to getMetaByPageId");
            return null;
        }

        try {
            $rows = $this->db->query(
                "SELECT * FROM meta WHERE page_id = :page_id",
                [':page_id' => $pageId]
            );
            
            return $rows[0] ?? null;
            
        } catch (Exception $e) {
            error_log("Error in getMetaByPageId: " . $e->getMessage());
            return null;
        }
    }

    public function updateMeta($data) {
        try {
            $result = $this->db->execute("
                INSERT INTO meta (
                    page_id, title, description, keywords, author, robots,
                    og_title, og_description, og_image, og_type,
                    twitter_card, twitter_site, twitter_creator, twitter_title, twitter_description, twitter_image,
                    canonical_url
                ) VALUES (
                    :page_id, :title, :description, :keywords, :author, :robots,
                    :og_title, :og_description, :og_image, :og_type,
                    :twitter_card, :twitter_site, :twitter_creator, :twitter_title, :twitter_description, :twitter_image,
                    :canonical_url
                ) ON CONFLICT(page_id) DO UPDATE SET
                    title = :title,
                    description = :description,
                    keywords = :keywords,
                    author = :author,
                    robots = :robots,
                    og_title = :og_title,
                    og_description = :og_description,
                    og_image = :og_image,
                    og_type = :og_type,
                    twitter_card = :twitter_card,
                    twitter_site = :twitter_site,
                    twitter_creator = :twitter_creator,
                    twitter_title = :twitter_title,
                    twitter_description = :twitter_description,
                    twitter_image = :twitter_image,
                    canonical_url = :canonical_url,
                    updated_at = CURRENT_TIMESTAMP
            ", [
                ':page_id' => $data['page_id'],
                ':title' => $data['title'] ?? null,
                ':description' => $data['description'] ?? null,
                ':keywords' => $data['keywords'] ?? null,
                ':author' => $data['author'] ?? null,
                ':robots' => $data['robots'] ?? null,
                ':og_title' => $data['og_title'] ?? null,
                ':og_description' => $data['og_description'] ?? null,
                ':og_image' => $data['og_image'] ?? null,
                ':og_type' => $data['og_type'] ?? null,
                ':twitter_card' => $data['twitter_card'] ?? null,
                ':twitter_site' => $data['twitter_site'] ?? null,
                ':twitter_creator' => $data['twitter_creator'] ?? null,
                ':twitter_title' => $data['twitter_title'] ?? null,
                ':twitter_description' => $data['twitter_description'] ?? null,
                ':twitter_image' => $data['twitter_image'] ?? null,
                ':canonical_url' => $data['canonical_url'] ?? null
            ]);

            return $result !== false;

        } catch (Exception $e) {
            error_log("Error in updateMeta: " . $e->getMessage());
            return false;
        }
    }

    public function deleteMeta($pageId) {
        try {
            $result = $this->db->execute(
                "DELETE FROM meta WHERE page_id = :page_id",
                [':page_id' => $pageId]
            );

            return $result !== false;

        } catch (Exception $e) {
            error_log("Error in deleteMeta: " . $e->getMessage());
            return false;
        }
    }
}

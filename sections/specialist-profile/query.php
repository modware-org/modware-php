<?php

function getSpecialistData() {
    global $db;
    
    try {
        // Get specialist ID from URL parameter
        $specialistId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        if (!$specialistId) {
            return null;
        }

        $query = "SELECT 
            s.id,
            s.name,
            s.title,
            s.photo_url as photo,
            s.bio,
            GROUP_CONCAT(sp.specialization) as specializations
        FROM specialists s
        LEFT JOIN specialist_specializations sp ON s.id = sp.specialist_id
        WHERE s.id = ?
        GROUP BY s.id";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$specialistId]);
        
        $specialist = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($specialist) {
            // Convert specializations string to array
            $specialist['specializations'] = $specialist['specializations'] 
                ? explode(',', $specialist['specializations']) 
                : [];
        }
        
        return $specialist;
        
    } catch (PDOException $e) {
        error_log("Error fetching specialist data: " . $e->getMessage());
        return null;
    }
}

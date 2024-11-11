<?php

function getConsultationData() {
    global $db;
    
    try {
        // Get general consultation info
        $query = "SELECT 
            c.id,
            c.description,
            c.booking_info
        FROM consultation_settings c
        WHERE c.active = 1
        LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $consultationData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$consultationData) {
            return null;
        }
        
        // Get consultation types
        $typesQuery = "SELECT 
            t.id,
            t.name,
            t.duration,
            t.price,
            t.description
        FROM consultation_types t
        WHERE t.active = 1
        ORDER BY t.display_order";
        
        $stmt = $db->prepare($typesQuery);
        $stmt->execute();
        
        $consultationData['types'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get contact methods
        $methodsQuery = "SELECT 
            m.name,
            m.link
        FROM contact_methods m
        WHERE m.active = 1 AND m.show_in_consultation = 1
        ORDER BY m.display_order";
        
        $stmt = $db->prepare($methodsQuery);
        $stmt->execute();
        
        $consultationData['contact_methods'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $consultationData;
        
    } catch (PDOException $e) {
        error_log("Error fetching consultation data: " . $e->getMessage());
        return null;
    }
}

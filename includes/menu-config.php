<?php
class MenuConfig {
    public static function getFilterCounts() {
        global $pdo;
        
        // Get menu items from database
        $stmt = $pdo->prepare("SELECT category, COUNT(*) as count FROM menu GROUP BY category");
        $stmt->execute();
        $counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Calculate total count
        $total_count = array_sum($counts);
        
        return [
            'all' => $total_count,
            'veg' => $counts['veg'] ?? 0,
            'nonveg' => $counts['nonveg'] ?? 0,
            'diet' => $counts['diet'] ?? 0,
            'subscription' => $counts['subscription'] ?? 0
        ];
    }
    
    public static function getCategoryCount($category) {
        global $pdo;
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM menu WHERE category = ?");
        $stmt->execute([$category]);
        return $stmt->fetchColumn();
    }
}

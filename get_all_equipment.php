<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
$host = 'localhost';
$dbname = 'eventease';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}

// Fetch all equipment items for comparison
try {
    $stmt = $pdo->prepare("SELECT id, name, category, price_per_day, image_url, description, quantity, 
                           COALESCE(rating, 4.5) as rating, availability 
                           FROM equipment ORDER BY category, name");
    $stmt->execute();
    $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the response
    $items = [];
    foreach ($equipment as $item) {
        $items[] = [
            'id' => (int)$item['id'],
            'name' => $item['name'],
            'category' => $item['category'],
            'price_per_day' => (float)$item['price_per_day'],
            'image_url' => $item['image_url'],
            'description' => $item['description'],
            'quantity' => (int)$item['quantity'],
            'rating' => (float)$item['rating'],
            'availability' => $item['availability'] ?? 'Available'
        ];
    }
    
    echo json_encode([
        'success' => true,
        'equipment' => $items,
        'count' => count($items)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch equipment: ' . $e->getMessage()
    ]);
}
?>

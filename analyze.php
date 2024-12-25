<?php
declare(strict_types=1);

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Set JSON response headers
header('Content-Type: application/json');

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Validate file upload
if (!isset($_FILES['foodImage']) || $_FILES['foodImage']['error'] !== UPLOAD_ERR_OK) {
    error_log('File upload error: ' . ($_FILES['foodImage']['error'] ?? 'No file uploaded'));
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No image uploaded']);
    exit;
}

try {
    // Validate upload directory
    $uploadDir = __DIR__ . '/uploads/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0775, true)) {
            throw new Exception('Failed to create uploads directory');
        }
    }

    if (!is_writable($uploadDir)) {
        throw new Exception('Uploads directory is not writable');
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = $_FILES['foodImage']['type'];
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.');
    }

    // Generate safe filename
    $extension = pathinfo($_FILES['foodImage']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('meal_') . '.' . $extension;
    $filepath = $uploadDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($_FILES['foodImage']['tmp_name'], $filepath)) {
        throw new Exception('Failed to save uploaded file');
    }

    // Mock analysis result
    $mockResult = [
        'success' => true,
        'data' => [
            'confidence' => '95%',
            'imageUrl' => '/uploads/' . $filename,
            'total' => [
                'calories' => rand(300, 800),
                'protein' => rand(15, 30) . 'g',
                'carbs' => rand(30, 60) . 'g',
                'fat' => rand(10, 25) . 'g'
            ],
            'items' => [
                [
                    'name' => 'Grilled Chicken',
                    'portion' => '150g',
                    'calories' => rand(200, 300)
                ],
                [
                    'name' => 'Brown Rice',
                    'portion' => '100g',
                    'calories' => rand(100, 150)
                ],
                [
                    'name' => 'Mixed Vegetables',
                    'portion' => '80g',
                    'calories' => rand(50, 100)
                ]
            ]
        ]
    ];

    echo json_encode($mockResult);
} catch (Exception $e) {
    error_log('Analysis Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to analyze image: ' . $e->getMessage()
    ]);
} 
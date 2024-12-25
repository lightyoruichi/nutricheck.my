<?php
// Error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Response header
header('Content-Type: application/json');

// Function to validate image
function validateImage($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 10 * 1024 * 1024; // 10MB

    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Invalid parameters.');
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    if ($file['size'] > $maxSize) {
        throw new RuntimeException('Exceeded filesize limit.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        throw new RuntimeException('Invalid file format.');
    }

    return true;
}

try {
    // Check if file was uploaded
    if (!isset($_FILES['foodImage'])) {
        throw new RuntimeException('No file uploaded.');
    }

    // Validate the image
    validateImage($_FILES['foodImage']);

    // Create unique filename
    $filename = sprintf('%s-%s.%s',
        uniqid('food-', true),
        date('Y-m-d'),
        pathinfo($_FILES['foodImage']['name'], PATHINFO_EXTENSION)
    );

    // Move file to uploads directory
    $uploadPath = __DIR__ . '/uploads/' . $filename;
    if (!move_uploaded_file($_FILES['foodImage']['tmp_name'], $uploadPath)) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    // TODO: Implement actual food analysis here
    // For now, return dummy data
    $response = [
        'success' => true,
        'data' => [
            'items' => ['Apple', 'Banana', 'Orange'],
            'nutritionalInfo' => [
                'calories' => '120 kcal',
                'protein' => '2g',
                'carbs' => '25g',
                'fat' => '0.5g'
            ],
            'imageUrl' => 'uploads/' . $filename
        ]
    ];

    echo json_encode($response);

} catch (RuntimeException $e) {
    // Handle errors
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 
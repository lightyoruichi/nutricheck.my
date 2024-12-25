<?php
declare(strict_types=1);

// Configuration constants
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 10485760); // 10MB
define('ALLOWED_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp'
]);

// Initialize state
$state = [
    'message' => '',
    'messageType' => '',
    'analysisResult' => null,
    'imageUrl' => null,
    'confidence' => 0
];

// Error reporting based on environment
if (getenv('APP_DEBUG') === 'true') {
error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Ensure request method is set
if (!isset($_SERVER['REQUEST_METHOD'])) {
    $_SERVER['REQUEST_METHOD'] = 'GET';
}

// Start session and set security headers
session_start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verify CSRF token on POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foodImage'])) {
    try {
        // Call analyze endpoint
        $ch = curl_init('http://localhost/analyze');
        if ($ch === false) {
            throw new Exception('Failed to initialize cURL');
        }
        
        // Create form data
        $postData = [
            'foodImage' => new CURLFile(
                $_FILES['foodImage']['tmp_name'],
                $_FILES['foodImage']['type'],
                $_FILES['foodImage']['name']
            )
        ];

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false, // For local development only
            CURLOPT_SSL_VERIFYHOST => false, // For local development only
            CURLOPT_HTTPHEADER => [
                'X-Requested-With: XMLHttpRequest'
            ]
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('Network error: ' . curl_error($ch));
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('Failed to analyze image (HTTP ' . $httpCode . '). Please try again.');
        }

        $result = json_decode($response, true);
        if (!$result || !isset($result['success']) || !$result['success']) {
            throw new Exception($result['error'] ?? 'Invalid response from server. Please try again.');
        }

        $_SESSION['analysis_result'] = $result['data'];
        $_SESSION['success_message'] = 'Analysis complete!';
        
        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF'], true, 303);
        exit;
    } catch (Exception $e) {
        error_log('Analysis Error: ' . $e->getMessage());
        $_SESSION['error_message'] = "Sorry, we couldn't analyze your meal. Please try again with a clear photo of your food. 📸";
        header('Location: ' . $_SERVER['PHP_SELF'], true, 303);
        exit;
    }
}

// Get messages from session
if (isset($_SESSION['success_message'])) {
    $state['message'] = $_SESSION['success_message'];
    $state['messageType'] = 'success';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $state['message'] = $_SESSION['error_message'];
    $state['messageType'] = 'error';
    unset($_SESSION['error_message']);
}

// Get analysis result from session
if (isset($_SESSION['analysis_result'])) {
    $state['analysisResult'] = $_SESSION['analysis_result'];
    $state['imageUrl'] = $state['analysisResult']['imageUrl'] ?? null;
    $state['confidence'] = intval(str_replace('%', '', $state['analysisResult']['confidence'] ?? '0'));
    unset($_SESSION['analysis_result']);
}

// Clean up old files
cleanupOldFiles(UPLOAD_DIR, 24); // Remove files older than 24 hours

/**
 * Cleans up old files from the uploads directory
 */
function cleanupOldFiles(string $dir, int $hours): void {
    if (!is_dir($dir)) return;
    
    $files = glob($dir . '*');
    $now = time();
    
    foreach ($files as $file) {
        if (is_file($file) && $now - filemtime($file) >= $hours * 3600) {
            @unlink($file);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#4299e1">
    <meta name="description" content="NutriCheck - Instant food analysis and nutritional information. Take a photo of your meal and get detailed nutritional facts.">
    
    <title>🍽️ NutriCheck - Food Analysis Made Simple</title>
    
    <!-- PWA Support -->
    <link rel="manifest" href="/manifest.json">
    <link rel="author" type="text/plain" href="/humans.txt">
    <meta name="msapplication-config" content="/browserconfig.xml">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="NutriCheck">
    <link rel="icon" type="image/png" href="/icons/icon-192x192.png">
    
    <!-- Preconnect to external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    
    <!-- Critical CSS -->
    <style>
        :root {
            --primary: #4299e1;
            --primary-dark: #3182ce;
            --success: #48bb78;
            --danger: #f56565;
            --gray-100: #f7fafc;
            --gray-200: #edf2f7;
            --gray-300: #e2e8f0;
            --gray-400: #cbd5e0;
            --gray-500: #a0aec0;
            --gray-600: #718096;
            --gray-700: #4a5568;
            --gray-800: #2d3748;
            --gray-900: #1a202c;
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            line-height: 1.5;
            color: var(--gray-800);
            background: var(--gray-100);
        }

        /* Layout */
        .app-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }

        /* Header */
        .app-header {
            text-align: center;
            padding: 2rem 1rem;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #fff 0%, var(--gray-100) 100%);
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .app-logo {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--gray-800) 0%, var(--gray-600) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: none;
        }

        /* Feature Pills */
        .feature-pills {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 1rem;
        }

        .feature-pill {
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 2rem;
            font-size: 1.25rem;
            cursor: help;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            position: relative;
        }

        .feature-pill[data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 0.5rem;
            background: var(--gray-800);
            color: white;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            white-space: nowrap;
            z-index: 10;
            margin-bottom: 0.5rem;
        }

        /* Upload Section */
        .upload-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .upload-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 300px;
            background: white;
            border: 2px dashed var(--gray-300);
            border-radius: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 2rem;
            text-align: center;
        }

        .upload-button:hover {
            border-color: var(--primary);
            background: var(--gray-50);
        }

        .upload-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .supported-formats,
        .file-size-info {
            color: var(--gray-600);
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        /* Preview Modal */
        .preview-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.75);
            z-index: 1000;
            padding: 1rem;
        }

        .preview-container.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-content {
            background: white;
            border-radius: 1rem;
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }

        .preview-image-container {
            position: relative;
            background: var(--gray-100);
        }

        .preview-image {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: contain;
        }

        .preview-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease;
        }

        .preview-close:hover {
            background: rgba(0,0,0,0.7);
        }

        .preview-controls {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: white;
        }

        .preview-button {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .preview-submit {
            background: var(--primary);
            color: white;
        }

        .preview-submit:hover {
            background: var(--primary-dark);
        }

        .preview-cancel {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .preview-cancel:hover {
            background: var(--gray-300);
        }

        /* Loading State */
        .analyzing-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            z-index: 1000;
        }

        .analyzing-container.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .analyzing-content {
            text-align: center;
            padding: 2rem;
        }

        .analyzing-animation {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 2rem;
        }

        .food-icon {
            animation: bounce 1s infinite;
        }

        .food-icon:nth-child(2) { animation-delay: 0.1s; }
        .food-icon:nth-child(3) { animation-delay: 0.2s; }
        .food-icon:nth-child(4) { animation-delay: 0.3s; }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .analyzing-text {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .analyzing-subtext {
            color: var(--gray-600);
            margin-bottom: 1rem;
        }

        .analyzing-progress {
            width: 200px;
            height: 4px;
            background: var(--gray-200);
            border-radius: 2px;
            overflow: hidden;
            margin: 0 auto;
        }

        .analyzing-progress-bar {
            width: 30%;
            height: 100%;
            background: var(--primary);
            animation: progress 2s infinite;
        }

        @keyframes progress {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(400%); }
        }

        /* Results Card */
        .result-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .result-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--gray-100) 0%, white 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .brand h1 {
            font-size: 1.25rem;
            margin: 0;
        }

        .confidence {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .confidence-bar {
            width: 60px;
            height: 4px;
            background: var(--gray-200);
            border-radius: 2px;
            overflow: hidden;
        }

        .confidence-fill {
            height: 100%;
            background: var(--success);
            transition: width 0.3s ease;
        }

        .meal-preview {
            position: relative;
            height: 300px;
            background: var(--gray-100);
        }

        .meal-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .meal-overlay {
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            background: rgba(255,255,255,0.9);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 500;
        }

        .nutrition-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            padding: 1.5rem;
            background: var(--gray-50);
        }

        .nutrition-stat {
            background: white;
            padding: 1rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .stat-icon {
            font-size: 1.5rem;
        }

        .stat-details {
            flex: 1;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .ingredients-list {
            padding: 1.5rem;
        }

        .ingredients-list h2 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .ingredients-list ul {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.75rem;
        }

        .ingredients-list li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background: var(--gray-50);
            border-radius: 0.5rem;
        }

        .ingredient-amount {
            margin-left: auto;
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .result-footer {
            padding: 1.5rem;
            background: var(--gray-50);
            display: flex;
            justify-content: center;
        }

        .share-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .share-button:hover {
            background: var(--primary-dark);
        }

        .share-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Offline Badge */
        .offline-badge {
            display: none;
            position: fixed;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            padding: 0.75rem 1.5rem;
            background: var(--gray-800);
            color: white;
            border-radius: 2rem;
            font-size: 0.875rem;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .offline-badge.active {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Utilities */
        .hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        .spinner {
            width: 1em;
            height: 1em;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .app-logo {
                font-size: 2rem;
            }

            .nutrition-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .upload-button {
                height: 250px;
            }

            .preview-content {
                margin: 0;
            }

            .preview-image {
                max-height: 300px;
            }
        }

        @media (max-width: 480px) {
            .app-container {
                padding: 0.5rem;
            }

            .app-header {
                padding: 1.5rem 1rem;
            margin-bottom: 1rem;
            }

            .feature-pills {
                flex-wrap: wrap;
                justify-content: center;
            }

            .upload-button {
                height: 200px;
            }

            .preview-controls {
                flex-direction: column;
            }

            .analyzing-animation {
                font-size: 1.5rem;
            }

            .analyzing-text {
                font-size: 1.25rem;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
            }

            .app-container {
                padding: 0;
            }

            .result-card {
                box-shadow: none;
                margin: 0;
            }

            .share-button,
            .offline-badge {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .preview-container {
                padding: 0.5rem;
            }

            .preview-content {
                margin: 0;
            }

            .preview-image {
                max-height: 300px;
            }

            .preview-controls {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
    
    <!-- Deferred CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        <header class="app-header">
            <h1 class="app-logo">🍽️ nutricheck</h1>
            <p>Level up your meals for better health</p>
            <div class="feature-pills">
                <span class="feature-pill" data-tooltip="Instant camera access">📸</span>
                <span class="feature-pill" data-tooltip="Analysis in seconds">⚡</span>
                <span class="feature-pill" data-tooltip="High accuracy results">🎯</span>
                <span class="feature-pill" data-tooltip="Halal certified">🟢</span>
            </div>
        </header>

        <main class="upload-container">
            <?php if ($state['message']): ?>
                <div class="alert alert-<?php echo $state['messageType'] === 'success' ? 'success' : 'danger'; ?> mb-3">
                    <?php echo htmlspecialchars($state['message']); ?>
                </div>
            <?php endif; ?>

            <?php if ($state['analysisResult']): ?>
                <div class="result-card">
                    <header class="result-header">
                        <div class="brand">
                            <span class="logo">🎯</span>
                            <h1>nutricheck.my</h1>
                    </div>
                        <div class="share-info">
                            <div class="confidence">
                                <span class="confidence-label">Analysis Confidence</span>
                                <div class="confidence-bar">
                                    <div class="confidence-fill" style="width: <?php echo $state['confidence']; ?>%"></div>
                </div>
                                <span class="confidence-value"><?php echo $state['confidence']; ?>%</span>
                </div>
                            <time class="timestamp">Analyzed on <?php echo date('M j, Y'); ?></time>
                </div>
                    </header>

                    <?php if ($state['imageUrl'] && file_exists($state['imageUrl'])): ?>
                    <div class="meal-preview">
                        <img src="<?php echo htmlspecialchars($state['imageUrl']); ?>" alt="Your meal" class="meal-image">
                        <div class="meal-overlay">
                            <span class="meal-badge">✨ Epic Meal Analysis</span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($state['analysisResult']['total'])): ?>
                    <div class="nutrition-grid">
                        <div class="nutrition-stat">
                            <span class="stat-icon">🔥</span>
                            <div class="stat-details">
                                <span class="stat-value"><?php echo number_format($state['analysisResult']['total']['calories']); ?></span>
                                <span class="stat-label">calories</span>
                            </div>
                        </div>
                        <div class="nutrition-stat">
                            <span class="stat-icon">💪</span>
                            <div class="stat-details">
                                <span class="stat-value"><?php echo $state['analysisResult']['total']['protein']; ?></span>
                                <span class="stat-label">protein</span>
                    </div>
                    </div>
                        <div class="nutrition-stat">
                            <span class="stat-icon">🌾</span>
                            <div class="stat-details">
                                <span class="stat-value"><?php echo $state['analysisResult']['total']['carbs']; ?></span>
                                <span class="stat-label">carbs</span>
                            </div>
                            </div>
                        <div class="nutrition-stat">
                            <span class="stat-icon">🥑</span>
                            <div class="stat-details">
                                <span class="stat-value"><?php echo $state['analysisResult']['total']['fat']; ?></span>
                                <span class="stat-label">fats</span>
                        </div>
                            </div>
                            </div>
                    <?php endif; ?>

                    <?php if (isset($state['analysisResult']['items']) && !empty($state['analysisResult']['items'])): ?>
                                <div class="ingredients-list">
                        <h2>🍽️ Detected Items</h2>
                        <ul>
                            <?php foreach ($state['analysisResult']['items'] as $item): ?>
                            <li>
                                <span class="ingredient-icon">🍽️</span>
                                <span class="ingredient-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                <span class="ingredient-amount"><?php echo htmlspecialchars($item['portion']); ?></span>
                            </li>
                                    <?php endforeach; ?>
                        </ul>
                                </div>
                    <?php endif; ?>

                    <footer class="result-footer">
                        <button type="button" id="shareButton" class="share-button">
                            <span class="share-icon">📤</span>
                            Share Results
                        </button>
                    </footer>
                            </div>
            <?php else: ?>
                <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
                    <div id="uploadButton" class="upload-button">
                        <span class="upload-icon">📸</span>
                        <p id="uploadSubtext">Tap to take a photo</p>
                        <div class="supported-formats">Supported: JPG, PNG, WebP</div>
                        <div class="file-size-info">Max size: 10MB</div>
                        </div>
                    <input type="file" 
                        id="foodImage" 
                        name="foodImage" 
                        accept="image/*" 
                        capture="environment"
                        class="hidden" 
                        required>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                </form>
            <?php endif; ?>
        </main>
                    </div>

    <!-- Preview Modal -->
    <div id="previewContainer" class="preview-container" role="dialog" aria-modal="true" aria-labelledby="previewTitle">
        <div class="preview-content">
            <div class="preview-image-container">
                <img id="previewImage" class="preview-image" src="" alt="Preview of your meal" loading="lazy">
                <button type="button" class="preview-close" onclick="cancelPreview()" aria-label="Close preview">×</button>
                <div class="preview-header">
                    <h2 id="previewTitle" class="preview-title">Preview Your Meal</h2>
                    </div>
            </div>
            <div class="preview-controls">
                <button type="button" class="preview-button preview-cancel" onclick="retakePhoto()">
                    <span aria-hidden="true">📸</span> Retake Photo
                        </button>
                <button type="button" class="preview-button preview-submit" onclick="submitImage()">
                    <span aria-hidden="true">✨</span> Analyze Meal
                        </button>
            </div>
        </div>
                    </div>

    <!-- Loading State -->
    <div id="analyzingContainer" class="analyzing-container" role="alert" aria-busy="true">
        <div class="analyzing-content">
            <div class="analyzing-animation" aria-hidden="true">
                <span class="food-icon">🥗</span>
                <span class="food-icon">🍎</span>
                <span class="food-icon">🥑</span>
                <span class="food-icon">🥩</span>
                    </div>
            <div class="analyzing-text">Analyzing your meal...</div>
            <div class="analyzing-subtext">Our AI is checking ingredients and calculating nutrition facts</div>
            <div class="analyzing-progress">
                <div class="analyzing-progress-bar"></div>
                </div>
        </div>
    </div>

    <!-- Offline Status -->
    <div id="offlineBadge" class="offline-badge" role="alert">
        <span aria-hidden="true">📡</span> You're offline - Some features may be limited
    </div>

    <!-- Deferred Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('uploadForm');
            const fileInput = document.getElementById('foodImage');
            const uploadButton = document.getElementById('uploadButton');
            const previewContainer = document.getElementById('previewContainer');
            const previewImage = document.getElementById('previewImage');
            const analyzingContainer = document.getElementById('analyzingContainer');
            
            if (!form || !fileInput || !uploadButton) return;

            // Handle file selection
            fileInput.addEventListener('change', async function(e) {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    
                    // Validate file size
                    if (file.size > <?php echo MAX_FILE_SIZE; ?>) {
                        alert('File is too large. Maximum size is 10MB.');
                        this.value = '';
                        return;
                    }

                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPG, PNG, or WebP).');
                        this.value = '';
                        return;
                    }

                    // Compress image
                    const compressedBlob = await compressImage(file);
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewContainer.classList.add('active');
                    };
                    
                    reader.readAsDataURL(compressedBlob);
                }
            });

            // Handle button click
            uploadButton.addEventListener('click', () => {
                fileInput.click();
            });

            // Image compression
            async function compressImage(file) {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = new Image();
                        img.onload = function() {
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            
                            // Calculate new dimensions
                            let width = img.width;
                            let height = img.height;
                            const maxDimension = 1200;

                            if (width > height && width > maxDimension) {
                                height = (height * maxDimension) / width;
                                width = maxDimension;
                            } else if (height > maxDimension) {
                                width = (width * maxDimension) / height;
                                height = maxDimension;
                            }

                            canvas.width = width;
                            canvas.height = height;

                            // Draw and compress
                            ctx.drawImage(img, 0, 0, width, height);
                            canvas.toBlob(resolve, 'image/jpeg', 0.8);
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Form reset function
            window.resetForm = function() {
                fileInput.value = '';
                
                // Clear any stored FileList object
                const newFileInput = fileInput.cloneNode(true);
                fileInput.parentNode.replaceChild(newFileInput, fileInput);
                
                // Reset form and UI
                form.reset();
                uploadButton.style.display = 'flex';
                previewContainer.classList.remove('active');
                
                // Clear URL parameters
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            // Retake photo function
            window.retakePhoto = function() {
                resetForm();
                fileInput.click();
            }

            // Handle form submission
            window.submitImage = function() {
                previewContainer.classList.remove('active');
                analyzingContainer.classList.add('active');
                
                // Submit form
                            form.submit();
            }

            // Handle preview cancellation
            window.cancelPreview = function() {
                previewContainer.classList.remove('active');
                fileInput.value = '';
            }

            // Prevent form resubmission on page refresh
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

            // Share functionality
            const shareButton = document.getElementById('shareButton');
            if (shareButton) {
                shareButton.addEventListener('click', shareResults);
            }
        });

        // Offline detection
        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);

        function updateOnlineStatus() {
            const offlineBadge = document.getElementById('offlineBadge');
            if (!navigator.onLine) {
                offlineBadge.classList.add('active');
            } else {
                offlineBadge.classList.remove('active');
            }
        }

        // Screenshot and sharing functionality
        async function takeScreenshot(element, options = {}) {
            const {
                scale = 2,
                backgroundColor = '#ffffff',
                showLoadingUI = true
            } = options;

            if (showLoadingUI) {
                document.body.classList.add('screenshot-loading');
            }

            try {
                const { offsetWidth, offsetHeight } = element;
                const images = element.querySelectorAll('img');
                await Promise.all([...images].map(img => {
                    if (!img.complete) {
                        return new Promise((resolve) => {
                            img.onload = resolve;
                            img.onerror = resolve;
                            if (!img.crossOrigin && img.src.startsWith('http')) {
                                img.crossOrigin = 'anonymous';
                            }
                        });
                    }
                }));

                const canvas = await html2canvas(element, {
                    scale,
                    backgroundColor,
                    logging: false,
                    width: offsetWidth,
                    height: offsetHeight,
                    useCORS: true,
                    onclone: (clonedDoc) => {
                        const clonedElement = clonedDoc.body.firstChild;
                        clonedElement.setAttribute('aria-hidden', 'true');
                        clonedElement.setAttribute('role', 'presentation');
                    }
                });

                return canvas;
            } catch (error) {
                console.error('Screenshot generation failed:', error);
                throw new Error('Failed to generate screenshot');
            } finally {
                if (showLoadingUI) {
                    document.body.classList.remove('screenshot-loading');
                }
            }
        }

        async function shareResults() {
            const shareButton = document.getElementById('shareButton');
            if (!shareButton) return;

            try {
                shareButton.disabled = true;
                shareButton.innerHTML = '<span class="spinner"></span> Generating...';
                
                const resultsCard = document.querySelector('.result-card');
                if (!resultsCard) throw new Error('No results to share');

                const clone = resultsCard.cloneNode(true);
                const container = document.createElement('div');
                container.style.position = 'absolute';
                container.style.left = '-9999px';
                container.style.width = '480px';
                container.appendChild(clone);
                document.body.appendChild(container);

                const canvas = await takeScreenshot(clone, {
                    scale: 2,
                    backgroundColor: '#ffffff',
                    showLoadingUI: false
                });

                document.body.removeChild(container);

                const blob = await new Promise((resolve, reject) => {
                    canvas.toBlob((blob) => {
                        if (blob) resolve(blob);
                        else reject(new Error('Failed to convert canvas to blob'));
                    }, 'image/png');
                });

                const file = new File([blob], 'nutricheck-results.png', { type: 'image/png' });
                const calories = document.querySelector('.stat-value')?.textContent || 'N/A';
                const shareData = {
                    title: 'My NutriCheck Results 🍽️',
                    text: `🎯 Just analyzed my meal with NutriCheck!\n` +
                          `🔥 Calories: ${calories}\n` +
                          `💪 Check out my nutritional breakdown!`,
                    files: [file]
                };

                if (navigator.canShare && navigator.canShare(shareData)) {
                    await navigator.share(shareData);
                } else {
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'nutricheck-results.png';
                    link.setAttribute('aria-label', 'Download results image');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(link.href);
                    
                    await navigator.clipboard.writeText(shareData.text);
                    alert('Image downloaded and text copied to clipboard!');
                }
            } catch (error) {
                console.error('Error sharing:', error);
                alert('Unable to share results. Please try again.');
            } finally {
                shareButton.disabled = false;
                shareButton.innerHTML = '<span class="share-icon">📤</span> Share Results';
            }
        }

        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('Service Worker registered:', registration.scope);
                    })
                    .catch(error => {
                        console.error('Service Worker registration failed:', error);
                    });
            });
        }
    </script>
</body>
</html>

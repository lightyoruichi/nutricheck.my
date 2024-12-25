<?php

// Bootstrap file - only declarations
declare(strict_types=1);

// Configuration constants
const UPLOAD_DIR = __DIR__ . '/uploads/';
const MAX_FILE_SIZE = 10485760; // 10MB
const ALLOWED_TYPES = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp'
];

// Initialize variables
$message = '';
$messageType = '';
$analysisResult = null;

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add this near the top of the file after error reporting
const ANALYSIS_STATES = [
    'READY' => 'ready',
    'UPLOADING' => 'uploading',
    'ANALYZING' => 'analyzing',
    'COMPLETE' => 'complete',
    'ERROR' => 'error'
];

// Add this near the top after session_start()
if (!isset($_SERVER['REQUEST_METHOD'])) {
    $_SERVER['REQUEST_METHOD'] = 'GET';
}

session_start();

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verify CSRF token on POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header('HTTP/1.1 403 Forbidden');
        exit('Invalid CSRF token');
    }
}

// Handle file upload
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foodImage'])) {
    $file = $_FILES['foodImage'];

    try {
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Upload failed. Please try again.');
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('File is too large. Maximum size is 10MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, ALLOWED_TYPES, true)) {
            throw new Exception('Invalid file type. Please upload a JPG, PNG, GIF, or WebP image.');
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = UPLOAD_DIR . $filename;

        // Move file to uploads directory
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to save file. Please try again.');
        }

        // Simulate analysis (replace with actual API call)
        $analysisResult = [
            'items' => [
                [
                    'name' => 'Grilled Chicken Breast',
                    'portion' => '1 piece (150g)',
                    'calories' => 165,
                    'protein' => '31g',
                    'carbs' => '0g',
                    'fat' => '3.6g',
                    'ingredients' => [
                        'Chicken breast',
                        'Olive oil',
                        'Herbs and seasonings'
                    ]
                ],
                [
                    'name' => 'Brown Rice',
                    'portion' => '1 cup (195g)',
                    'calories' => 216,
                    'protein' => '5g',
                    'carbs' => '45g',
                    'fat' => '1.8g',
                    'ingredients' => [
                        'Whole grain brown rice'
                    ]
                ],
                [
                    'name' => 'Steamed Broccoli',
                    'portion' => '1 cup (156g)',
                    'calories' => 55,
                    'protein' => '3.7g',
                    'carbs' => '11.2g',
                    'fat' => '0.6g',
                    'ingredients' => [
                        'Fresh broccoli',
                        'Sea salt'
                    ]
                ]
            ],
            'total' => [
                'calories' => 436,
                'protein' => '39.7g',
                'carbs' => '56.2g',
                'fat' => '6g'
            ],
            'confidence' => '92%'
        ];

        // After successful analysis
        $_SESSION['analysis_result'] = $analysisResult;
        $_SESSION['success_message'] = 'Analysis complete!';
        
        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Get messages from session
if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    $messageType = 'success';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $message = $_SESSION['error_message'];
    $messageType = 'error';
    unset($_SESSION['error_message']);
}

// Get analysis result from session
if (isset($_SESSION['analysis_result'])) {
    $analysisResult = $_SESSION['analysis_result'];
    unset($_SESSION['analysis_result']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍽️ NutriCheck - Food Analysis Made Simple</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #009688, #00695C);
            --accent-color: #4CAF50;
            --accent-hover: #45a049;
            --background-color: #F9F9F9;
            --text-primary: #333333;
            --text-secondary: #777777;
            --border-radius: 16px;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        body {
            background: var(--background-color);
            color: var(--text-primary);
            font-family: var(--font-family);
            -webkit-font-smoothing: antialiased;
            height: 100vh;
            margin: 0;
            line-height: 1.6;
        }

        .app-container {
            max-width: 480px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
            position: relative;
            box-shadow: var(--shadow-md);
            display: flex;
            flex-direction: column;
        }

        .app-header {
            padding: 2.5rem 1.5rem;
            background: var(--primary-gradient);
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .app-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40%;
            background: linear-gradient(to top, rgba(0,0,0,0.1), transparent);
            pointer-events: none;
        }

        .adidas-style {
            font-family: var(--font-family);
            font-weight: 800;
            text-transform: lowercase;
            letter-spacing: -0.02em;
            color: white;
            margin: 0;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 2.75rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .app-header p {
            margin: 0.75rem 0 0;
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.35rem;
            font-weight: 500;
            letter-spacing: -0.01em;
        }

        .main-content {
            padding: 1rem;
            flex: 1;
        }

        .upload-zone {
            min-height: 220px;
            max-width: 340px;
            margin: 2rem auto;
            padding: 2.5rem;
            border: 2.5px dashed #e2e8f0;
            border-radius: var(--border-radius);
            background: white;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            box-shadow: var(--shadow-sm);
        }

        .upload-zone:hover {
            border-color: var(--accent-color);
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .camera-icon {
            font-size: 4rem;
            margin-bottom: 1.25rem;
            color: var(--accent-color);
            animation: pulse 2s infinite;
            display: block;
            line-height: 1;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.85; }
            100% { transform: scale(1); opacity: 1; }
        }

        .upload-text {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            letter-spacing: -0.01em;
        }

        .upload-subtext {
            font-size: 1.1rem;
            color: var(--text-secondary);
            max-width: 260px;
            line-height: 1.5;
        }

        .supported-formats {
            display: flex;
            gap: 1.25rem;
            margin: 2rem auto;
            justify-content: center;
            flex-wrap: wrap;
            max-width: 340px;
        }

        .format-tag {
            font-size: 1rem;
            color: var(--text-secondary);
            padding: 0.75rem 1rem;
            border-radius: 12px;
            background: white;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }

        .format-tag:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            color: var(--accent-color);
        }

        .format-note {
            width: 100%;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-top: 0.75rem;
            font-style: italic;
            opacity: 0.8;
            transition: opacity 0.2s ease;
        }

        .format-note:hover {
            opacity: 1;
        }

        .quick-info {
            text-align: center;
            margin: 2.5rem auto;
            max-width: 340px;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            padding: 1.5rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
        }

        .quick-info span {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            font-size: 1.1rem;
            color: var(--text-secondary);
            transition: all 0.2s ease;
            padding: 0.5rem;
            border-radius: 8px;
            position: relative;
        }

        .quick-info span:hover {
            color: var(--accent-color);
            transform: translateX(4px);
            background: rgba(76, 175, 80, 0.05);
        }

        .halal-info {
            position: relative;
            cursor: pointer;
        }

        .halal-tooltip {
            position: absolute;
            bottom: calc(100% + 0.5rem);
            left: 50%;
            transform: translateX(-50%) translateY(10px);
            background: white;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            color: var(--text-secondary);
            box-shadow: var(--shadow-md);
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            width: 250px;
            text-align: center;
            z-index: 10;
        }

        .halal-tooltip::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%) rotate(45deg);
            width: 12px;
            height: 12px;
            background: white;
            border-radius: 2px;
        }

        .halal-info:hover .halal-tooltip {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        @media (max-width: 480px) {
            .halal-tooltip {
                width: 200px;
                font-size: 0.8125rem;
                padding: 0.5rem 0.75rem;
            }
        }

        .footer-info {
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
            margin-top: 1rem;
        }

        .footer-info p {
            margin: 0.25rem 0;
        }

        #preview-container {
            display: none;
            position: relative;
            max-width: 280px;
            margin: 1rem auto;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        #preview {
            width: 100%;
            height: auto;
            display: block;
        }

        .preview-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 1rem;
        }

        .progress {
            width: 80%;
            max-width: 200px;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            margin-top: 1rem;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: #4ECDC4;
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .results-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-top: 1.5rem;
            box-shadow: var(--shadow-md);
            animation: slideUp 0.3s ease-out;
            position: relative;
            overflow: hidden;
        }

        .result-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, #4CAF50, #45a049);
        }

        .result-card.screenshot-mode {
            padding-bottom: 3rem;
        }

        .result-card.screenshot-mode::after {
            content: 'Analyzed with nutricheck.my';
            position: absolute;
            bottom: 1rem;
            right: 1rem;
            font-size: 0.75rem;
            color: var(--text-secondary);
            opacity: 0.7;
        }

        .result-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            gap: 1rem;
        }

        .result-header h3 {
            font-size: 1.75rem;
            font-weight: 800;
            margin: 0;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: bounce 0.6s ease-out;
        }

        .completion-badge {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: linear-gradient(135deg, #4CAF50, #45a049);
            padding: 0.75rem 1rem;
            border-radius: 12px;
            color: white;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.2);
        }

        .completion-icon {
            font-size: 1.5rem;
            animation: spin 4s linear infinite;
        }

        .completion-text {
            display: flex;
            flex-direction: column;
        }

        .confidence-label {
            font-size: 0.75rem;
            opacity: 0.9;
        }

        .confidence-value {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .success-message {
            text-align: center;
            color: var(--text-secondary);
            font-size: 1.125rem;
            margin-bottom: 2rem;
            animation: fadeIn 0.6s ease-out;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .metric {
            background: #f8fafc;
            padding: 1.25rem;
            border-radius: 16px;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .metric:hover {
            transform: translateY(-2px);
            background: #f1f5f9;
        }

        .metric-label {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            margin-bottom: 0.75rem;
        }

        .metric-icon {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .metric-subtitle {
            font-size: 0.75rem;
            color: var(--text-secondary);
            font-style: italic;
        }

        .metric-value {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .metric-value .unit {
            font-size: 0.875rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .metric-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            width: var(--progress);
            height: 4px;
            background: linear-gradient(to right, #4CAF50, #45a049);
            border-radius: 0 2px 2px 0;
            transition: width 1s ease-out;
        }

        .food-items {
            margin-top: 2.5rem;
        }

        .food-items h4 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
            text-align: center;
        }

        .food-item {
            background: #f8fafc;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .food-item:hover {
            transform: translateY(-2px);
            background: #f1f5f9;
            box-shadow: var(--shadow-sm);
        }

        .metric-pill {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-size: 0.875rem;
            color: var(--text-secondary);
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }

        .metric-pill:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
            color: var(--accent-color);
        }

        .share-section {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: center;
            padding: 0 1rem;
        }

        .share-button,
        .download-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #f8fafc;
            color: var(--text-primary);
            min-width: 140px;
            justify-content: center;
        }

        .share-button:hover,
        .download-button:hover {
            transform: translateY(-2px);
            background: #f1f5f9;
            box-shadow: var(--shadow-md);
        }

        .share-button:active,
        .download-button:active {
            transform: translateY(0);
        }

        .button-icon {
            font-size: 1.25rem;
            transition: transform 0.2s ease;
        }

        .share-button:hover .button-icon,
        .download-button:hover .button-icon {
            transform: translateY(-2px);
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @media (max-width: 480px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }

            .food-item-metrics {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .metric-pill {
                flex: 1;
                min-width: calc(50% - 0.5rem);
            }

            .share-section {
                flex-direction: column;
            }

            .share-button,
            .download-button {
                width: 100%;
                justify-content: center;
            }

            .halal-tooltip {
                width: 200px;
                font-size: 0.8125rem;
                padding: 0.5rem 0.75rem;
            }
        }

        .alert {
            position: relative;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
            animation: slideIn 0.3s ease-out;
            opacity: 1;
            transition: opacity 0.3s ease-out, transform 0.3s ease-out;
        }

        .alert-content {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-icon {
            font-size: 1.25rem;
        }

        .alert-message {
            flex: 1;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .alert-close {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 1.25rem;
            color: currentColor;
            opacity: 0.5;
            cursor: pointer;
            padding: 0.25rem;
            transition: opacity 0.2s ease;
        }

        .alert-close:hover {
            opacity: 1;
        }

        .alert-fade-out {
            opacity: 0;
            transform: translateY(-10px);
            pointer-events: none;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FCA5A5;
        }

        .alert-success {
            background: #DCFCE7;
            color: #166534;
            border: 1px solid #86EFAC;
        }

        .halal-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--accent-color);
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease;
        }

        .halal-badge:hover {
            transform: translateY(-2px);
        }

        .halal-badge .icon {
            color: #4CAF50;
            font-size: 1.25rem;
        }

        .halal-tooltip {
            position: absolute;
            bottom: -2.5rem;
            right: 0;
            background: white;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            color: var(--text-secondary);
            box-shadow: var(--shadow-md);
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            pointer-events: none;
            width: 200px;
            text-align: center;
        }

        .halal-badge:hover .halal-tooltip {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="app-container">
        <header class="app-header">
            <h1 class="adidas-style">🍽️ nutricheck</h1>
            <p>Level up your meals for better health</p>
        </header>

        <main class="main-content">
            <?php if ($message) : ?>
                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                    <?php echo $messageType === 'success' ? '✅ ' : '❌ '; ?><?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="upload-zone" id="uploadButton">
                    <span class="camera-icon">📸</span>
                    <div class="upload-text">Upload or Capture Your Meal</div>
                    <div class="upload-subtext" id="uploadSubtext">
                        Drop your food pic here or click to summon your camera spell
                    </div>
                    <input type="file" class="d-none" id="foodImage" name="foodImage" accept="image/*" required>
                </div>

                <div class="supported-formats">
                    <span class="format-tag">🖼️ JPG</span>
                    <span class="format-tag">📂 PNG</span>
                    <span class="format-tag">🎥 GIF</span>
                    <span class="format-tag">🌐 WebP</span>
                    <div class="format-note">Max size: 10MB—because some bosses need limits</div>
                </div>

                <div class="quick-info">
                    <span>📱 Mobile? Use your camera!</span>
                    <span>⚡ Analysis in under 10 seconds—no loading screens</span>
                    <span>🎯 High accuracy for insights you can trust</span>
                    <span class="halal-info">
                        <span>🟢 Certified Halal for peace of mind</span>
                        <div class="halal-tooltip">Our analysis ensures your meals align with Halal dietary requirements</div>
                    </span>
                </div>

                <div id="preview-container">
                    <img id="preview" src="" alt="Preview">
                    <div class="preview-overlay">
                        <p>Analyzing your meal...</p>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <?php if ($analysisResult) : ?>
                <div class="result-card" id="resultCard">
                    <div class="result-header">
                        <h3>🍽 Analysis Complete!</h3>
                        <div class="completion-badge">
                            <div class="completion-icon">✨</div>
                            <div class="completion-text">
                                <span class="confidence-label">Analysis Confidence</span>
                                <span class="confidence-value"><?php echo $analysisResult['confidence']; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="success-message">
                        Great job! Here's your meal's nutritional breakdown
                    </div>
                    
                    <div class="metrics-grid">
                        <div class="metric">
                            <div class="metric-label">
                                <span class="metric-icon">🔥</span>
                                <span>Calories</span>
                                <span class="metric-subtitle">Fuel for your journey</span>
                            </div>
                            <div class="metric-value">
                                <span class="value"><?php echo $analysisResult['total']['calories']; ?></span>
                                <span class="unit">kcal</span>
                            </div>
                            <div class="metric-progress" style="--progress: <?php echo min(100, ($analysisResult['total']['calories'] / 2000) * 100); ?>%"></div>
                        </div>
                        <div class="metric">
                            <div class="metric-label">
                                <span class="metric-icon">🥩</span>
                                <span>Protein</span>
                                <span class="metric-subtitle">Muscle power, unlocked!</span>
                            </div>
                            <div class="metric-value">
                                <span class="value"><?php echo $analysisResult['total']['protein']; ?></span>
                            </div>
                            <div class="metric-progress" style="--progress: <?php echo min(100, (intval($analysisResult['total']['protein']) / 50) * 100); ?>%"></div>
                        </div>
                        <div class="metric">
                            <div class="metric-label">
                                <span class="metric-icon">🍚</span>
                                <span>Carbs</span>
                                <span class="metric-subtitle">Energy on demand</span>
                            </div>
                            <div class="metric-value">
                                <span class="value"><?php echo $analysisResult['total']['carbs']; ?></span>
                            </div>
                            <div class="metric-progress" style="--progress: <?php echo min(100, (intval($analysisResult['total']['carbs']) / 300) * 100); ?>%"></div>
                        </div>
                        <div class="metric">
                            <div class="metric-label">
                                <span class="metric-icon">🥑</span>
                                <span>Fat</span>
                                <span class="metric-subtitle">Good vibes (and oils)</span>
                            </div>
                            <div class="metric-value">
                                <span class="value"><?php echo $analysisResult['total']['fat']; ?></span>
                            </div>
                            <div class="metric-progress" style="--progress: <?php echo min(100, (intval($analysisResult['total']['fat']) / 65) * 100); ?>%"></div>
                        </div>
                    </div>

                    <div class="food-items">
                        <h4>🍱 Here's what we found on your plate</h4>
                        <?php foreach ($analysisResult['items'] as $item): ?>
                        <div class="food-item">
                            <div class="food-item-header">
                                <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                                <span class="portion"><?php echo htmlspecialchars($item['portion']); ?></span>
                            </div>
                            <div class="food-item-metrics">
                                <span class="metric-pill">
                                    <span class="metric-icon">🔥</span>
                                    <span class="metric-value"><?php echo $item['calories']; ?> cal</span>
                                </span>
                                <span class="metric-pill">
                                    <span class="metric-icon">🥩</span>
                                    <span class="metric-value"><?php echo $item['protein']; ?></span>
                                </span>
                                <span class="metric-pill">
                                    <span class="metric-icon">🍚</span>
                                    <span class="metric-value"><?php echo $item['carbs']; ?></span>
                                </span>
                                <span class="metric-pill">
                                    <span class="metric-icon">🥑</span>
                                    <span class="metric-value"><?php echo $item['fat']; ?></span>
                                </span>
                            </div>
                            <div class="ingredients">
                                <h6>🥗 Made with:</h6>
                                <div class="ingredients-list">
                                    <?php foreach ($item['ingredients'] as $ingredient): ?>
                                    <span class="ingredient-tag"><?php echo htmlspecialchars($ingredient); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="action-buttons">
                        <button type="button" class="action-button secondary-button" onclick="resetForm()">
                            <span class="button-icon">←</span>
                            <span>Edit Meal</span>
                        </button>
                        <button type="button" class="action-button primary-button" onclick="retakePhoto()">
                            <span class="button-icon">📸</span>
                            <span>Analyze Another Meal</span>
                        </button>
                    </div>

                    <div class="share-section">
                        <button type="button" class="share-button" onclick="shareResults()">
                            <span class="button-icon">📤</span>
                            <span>Share Results</span>
                        </button>
                        <button type="button" class="download-button" onclick="downloadPDF()">
                            <span class="button-icon">📥</span>
                            <span>Download Report</span>
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </form>
        </main>
    </div>

    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    ></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('uploadForm');
            const fileInput = document.getElementById('foodImage');
            const uploadButton = document.getElementById('uploadButton');
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('preview-container');
            const resultCard = document.getElementById('resultCard');
            const uploadSubtext = document.getElementById('uploadSubtext');
            const progressBar = document.querySelector('.progress-bar');

            // Show result card if it exists
            if (resultCard) {
                uploadButton.style.display = 'none';
                resultCard.style.display = 'block';
                // Hide the supported formats and quick info when showing results
                document.querySelector('.supported-formats').style.display = 'none';
                document.querySelector('.quick-info').style.display = 'none';
            }

            // Enhanced device and capability detection
            const deviceInfo = {
                isMobile: /iPhone|iPad|iPod|Android/i.test(navigator.userAgent),
                hasCamera: !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia),
                hasTouchScreen: ('ontouchstart' in window) || (navigator.maxTouchPoints > 0),
                prefersDarkMode: window.matchMedia('(prefers-color-scheme: dark)').matches
            };

            // Update UI based on device capabilities
            function updateDeviceSpecificUI() {
                if (deviceInfo.isMobile && deviceInfo.hasCamera) {
                    uploadSubtext.textContent = '📸 Tap to take a photo';
                    fileInput.setAttribute('capture', 'environment');
                    uploadButton.classList.add('mobile-optimized');
                } else {
                    uploadSubtext.textContent = '🖼️ Drop your food image here or click to choose';
                }

                // Add device-specific classes
                document.body.classList.toggle('is-mobile', deviceInfo.isMobile);
                document.body.classList.toggle('has-touch', deviceInfo.hasTouchScreen);
                document.body.classList.toggle('dark-mode', deviceInfo.prefersDarkMode);
            }

            // Initialize device-specific UI
            updateDeviceSpecificUI();

            // Listen for dark mode changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateDeviceSpecificUI);

            // Click handling
            uploadButton.addEventListener('click', () => fileInput.click());

            // Drag and drop handling
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadButton.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadButton.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadButton.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                const primaryColor = getComputedStyle(
                    document.documentElement
                ).getPropertyValue('--primary-color')
                    .trim();
                uploadButton.style.borderColor = primaryColor;
                uploadButton.style.background = '#f8fafc';
            }

            function unhighlight(e) {
                uploadButton.style.borderColor = '#cbd5e1';
                const docStyle = getComputedStyle(document.documentElement);
                const secondaryColor = docStyle.getPropertyValue('--secondary-color').trim();
                uploadButton.style.background = secondaryColor;
            }

            uploadButton.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput.files = files;
                handleFiles(files);
            }

            function handleFiles(files) {
                if (files.length > 0) {
                    const file = files[0];
                    
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        showError('Please select an image file');
                        return;
                    }
                    
                    // Validate file size
                    if (file.size > <?php echo MAX_FILE_SIZE; ?>) {
                        showError('File is too large. Maximum size is 10MB');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        updateUIState('<?php echo ANALYSIS_STATES['UPLOADING']; ?>');
                        
                        setTimeout(() => {
                            updateUIState('<?php echo ANALYSIS_STATES['ANALYZING']; ?>');
                            form.submit();
                        }, 1000);
                    }
                    reader.readAsDataURL(file);
                }
            }

            function updateUIState(state, message = '') {
                const uploadButton = document.getElementById('uploadButton');
                const previewContainer = document.getElementById('preview-container');
                const resultCard = document.getElementById('resultCard');
                const progressBar = document.querySelector('.progress-bar');
                const previewOverlay = document.querySelector('.preview-overlay p');

                switch (state) {
                    case '<?php echo ANALYSIS_STATES['READY']; ?>':
                        uploadButton.style.display = 'flex';
                        previewContainer.style.display = 'none';
                        if (resultCard) resultCard.style.display = 'none';
                        break;
                    case '<?php echo ANALYSIS_STATES['UPLOADING']; ?>':
                        uploadButton.style.display = 'none';
                        previewContainer.style.display = 'block';
                        if (resultCard) resultCard.style.display = 'none';
                        previewOverlay.textContent = 'Uploading your image...';
                        progressBar.style.width = '30%';
                        break;
                    case '<?php echo ANALYSIS_STATES['ANALYZING']; ?>':
                        previewOverlay.textContent = 'Analyzing your meal...';
                        progressBar.style.width = '60%';
                        break;
                    case '<?php echo ANALYSIS_STATES['COMPLETE']; ?>':
                        progressBar.style.width = '100%';
                        break;
                    case '<?php echo ANALYSIS_STATES['ERROR']; ?>':
                        uploadButton.style.display = 'flex';
                        previewContainer.style.display = 'none';
                        if (resultCard) resultCard.style.display = 'none';
                        showError(message);
                        break;
                }
            }

            function showError(message) {
                const alertHtml = `
                    <div class="alert alert-danger" role="alert">
                        <div class="alert-content">
                            <span class="alert-icon">❌</span>
                            <span class="alert-message">${message}</span>
                        </div>
                        <button type="button" class="alert-close" aria-label="Close alert">×</button>
                    </div>
                `;
                const mainContent = document.querySelector('.main-content');
                const existingAlert = mainContent.querySelector('.alert');
                
                // Remove existing alert if present
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                // Insert new alert
                mainContent.insertAdjacentHTML('afterbegin', alertHtml);
                
                // Add click handler for close button
                const newAlert = mainContent.querySelector('.alert');
                const closeButton = newAlert.querySelector('.alert-close');
                
                closeButton.addEventListener('click', () => {
                    newAlert.classList.add('alert-fade-out');
                    setTimeout(() => newAlert.remove(), 300);
                });

                // Auto-remove after delay with fade animation
                setTimeout(() => {
                    if (newAlert && newAlert.isConnected) {
                        newAlert.classList.add('alert-fade-out');
                        setTimeout(() => newAlert.remove(), 300);
                    }
                }, 2000); // Reduced from 3000 to 2000ms
            }

            // Remove success message after 2 seconds with animation
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                successAlert.remove(); // Remove immediately since it's not needed
            }

            // File input change
            fileInput.addEventListener('change', function() {
                handleFiles(this.files);
            });
        });

        function resetForm() {
            const form = document.getElementById('uploadForm');
            const uploadButton = document.getElementById('uploadButton');
            const previewContainer = document.getElementById('preview-container');
            const resultCard = document.getElementById('resultCard');
            const fileInput = document.getElementById('foodImage');

            // Clear the file input
            fileInput.value = '';
            
            // Clear any stored FileList object
            const newFileInput = fileInput.cloneNode(true);
            fileInput.parentNode.replaceChild(newFileInput, fileInput);
            
            // Reset form and UI
            form.reset();
            uploadButton.style.display = 'flex';
            previewContainer.style.display = 'none';
            if (resultCard) {
                resultCard.style.display = 'none';
                // Remove from DOM to prevent ghost data
                resultCard.remove();
            }
            
            // Clear URL parameters
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        function retakePhoto() {
            resetForm();
            document.getElementById('foodImage').click();
        }

        window.onload = function() {
            // Prevent form resubmission on page refresh
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        }

        async function shareResults() {
            try {
                showMessage('Preparing your results...', 'success');
                
                // Get the result card element
                const resultCard = document.getElementById('resultCard');
                if (!resultCard) {
                    throw new Error('No results to share');
                }

                // Create a temporary container for the screenshot
                const container = document.createElement('div');
                container.style.position = 'absolute';
                container.style.left = '-9999px';
                container.style.top = '-9999px';
                container.style.width = '480px'; // Fixed width for consistent results
                
                // Clone the result card
                const clone = resultCard.cloneNode(true);
                container.appendChild(clone);
                document.body.appendChild(container);

                // Take screenshot
                const canvas = await html2canvas(clone, {
                    scale: 2, // Higher resolution
                    backgroundColor: '#ffffff',
                    logging: false,
                    width: 480,
                    windowWidth: 480,
                    useCORS: true
                });

                // Remove temporary container
                document.body.removeChild(container);

                // Convert canvas to blob
                const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
                const file = new File([blob], 'nutricheck-results.png', { type: 'image/png' });

                // Prepare share data
                const shareData = {
                    title: 'My NutriCheck Results 🍽️',
                    text: '🎯 Just analyzed my meal with NutriCheck!\n' +
                          '🔥 Calories: ' + document.querySelector('.metric-value .value').textContent + ' kcal\n' +
                          '💪 Check out my nutritional breakdown and level up your meals too!',
                    files: [file]
                };

                // Try native sharing first
                if (navigator.canShare && navigator.canShare(shareData)) {
                    await navigator.share(shareData);
                } else {
                    // Fallback: Download the image
                    const link = document.createElement('a');
                    link.href = canvas.toDataURL('image/png');
                    link.download = 'nutricheck-results.png';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Copy share text to clipboard
                    await navigator.clipboard.writeText(shareData.text);
                    showMessage('Image downloaded and text copied to clipboard!', 'success');
                }
            } catch (error) {
                console.error('Error sharing:', error);
                showMessage('Unable to share results. Please try again.', 'error');
            }
        }

        function downloadPDF() {
            // This is a placeholder - you'll need to implement actual PDF generation
            showMessage('Downloading your meal analysis...', 'success');
            
            // Simulate PDF download
            setTimeout(() => {
                const link = document.createElement('a');
                link.href = '#'; // Replace with actual PDF URL
                link.download = 'nutricheck-analysis.pdf';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 1000);
        }

        function showMessage(message, type = 'success') {
            const alertHtml = `
                <div class="alert alert-${type}" role="alert">
                    <div class="alert-content">
                        <span class="alert-icon">${type === 'success' ? '✅' : '❌'}</span>
                        <span class="alert-message">${message}</span>
                    </div>
                    <button type="button" class="alert-close" aria-label="Close alert">×</button>
                </div>
            `;
            const mainContent = document.querySelector('.main-content');
            const existingAlert = mainContent.querySelector('.alert');
            
            if (existingAlert) {
                existingAlert.remove();
            }
            
            mainContent.insertAdjacentHTML('afterbegin', alertHtml);
            
            const newAlert = mainContent.querySelector('.alert');
            const closeButton = newAlert.querySelector('.alert-close');
            
            closeButton.addEventListener('click', () => {
                newAlert.classList.add('alert-fade-out');
                setTimeout(() => newAlert.remove(), 300);
            });

            setTimeout(() => {
                if (newAlert && newAlert.isConnected) {
                    newAlert.classList.add('alert-fade-out');
                    setTimeout(() => newAlert.remove(), 300);
                }
            }, 2000);
        }
    </script>
</body>
</html>

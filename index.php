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
    <title>🍽️ NutriCheck</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-gradient: linear-gradient(135deg, #2563eb, #0ea5e9);
            --secondary-color: #f8fafc;
            --text-color: #0f172a;
            --border-radius: 16px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        
        body {
            background: var(--secondary-color);
            color: var(--text-color);
            font-family: 
                -apple-system, 
                BlinkMacSystemFont, 
                'Segoe UI', 
                Roboto, 
                Oxygen, 
                Ubuntu, 
                Cantarell, 
                sans-serif;
            -webkit-font-smoothing: antialiased;
            height: 100vh;
            margin: 0;
            line-height: 1.5;
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
            padding: 1.25rem 1rem;
            background: linear-gradient(to right, #ff6b6b, #4ecdc4);
        }

        .app-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-color);
        }

        .app-header p {
            margin: 0.75rem 0 0;
            color: #64748b;
            font-size: 1.125rem;
            font-weight: 500;
            background: linear-gradient(135deg, #64748b, #94a3b8);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .main-content {
            padding: 1rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .upload-button {
            width: 100%;
            max-width: 280px;
            aspect-ratio: 3/2;
            margin: 0.5rem 0;
        }

        .upload-button:hover, .upload-button:focus {
            border-color: var(--primary-color);
            background: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .upload-button .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .upload-button .text {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .upload-button .subtext {
            font-size: 1rem;
            color: #64748b;
            text-align: center;
        }

        .footer-info {
            margin-top: 1rem;
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
        }

        .footer-info p {
            margin: 0.25rem 0;
        }

        #preview-container {
            position: absolute;
            inset: 1.25rem;
            border-radius: var(--border-radius);
            overflow: hidden;
            background: var(--secondary-color);
            display: none;
        }

        #preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 1.25rem;
            backdrop-filter: blur(4px);
        }

        .preview-overlay .progress {
            width: 80%;
            max-width: 240px;
            height: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            overflow: hidden;
            margin-top: 1rem;
        }

        .preview-overlay .progress-bar {
            height: 100%;
            background: var(--primary-color);
            border-radius: 1rem;
            transition: width 0.2s ease;
        }

        .result-card {
            position: absolute;
            inset: 1.25rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            padding: 1.25rem;
            display: none;
            overflow: auto;
            gap: 1rem;
        }

        .result-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .result-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .confidence {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #10b981;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .metric {
            background: var(--secondary-color);
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
        }

        .metric-label {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .metric-value {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .action-button {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 12px;
            background: var(--primary-color);
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 1rem;
        }

        .action-button:hover {
            background: #1d4ed8;
        }

        .action-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @media (max-width: 480px) {
            .app-container {
                box-shadow: none;
            }
            
            .upload-button {
                aspect-ratio: 5/4;
            }
            
            .main-content {
                padding: 1rem;
            }
        }

        @supports (padding: max(0px)) {
            .app-container {
                padding-bottom: max(1rem, env(safe-area-inset-bottom));
            }
        }

        .adidas-style {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-weight: 700;
            text-transform: lowercase;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #FF6B6B, #4ECDC4);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            display: inline-flex;
            align-items: center;
            font-size: clamp(1.5rem, 3vw, 2.5rem);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            position: relative;
        }

        .adidas-style::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(135deg, #FF6B6B, #4ECDC4);
            border-radius: 2px;
            opacity: 0.5;
        }

        .total-metrics {
            background: var(--secondary-color);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
        }

        .total-metrics h4 {
            margin: 0 0 1rem;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .food-items {
            margin-top: 1rem;
        }

        .food-items h4 {
            margin: 0 0 1rem;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .food-item {
            background: var(--secondary-color);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 0.75rem;
        }

        .food-item:last-child {
            margin-bottom: 0;
        }

        .food-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .food-item-header h5 {
            margin: 0;
            font-size: 1rem;
            color: var(--text-color);
        }

        .portion {
            font-size: 0.875rem;
            color: #64748b;
        }

        .food-item-metrics {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            color: #64748b;
        }

        .ingredients h6 {
            margin: 0 0 0.5rem;
            font-size: 0.875rem;
            color: var(--text-color);
        }

        .ingredients-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .ingredient-tag {
            background: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            color: var(--text-color);
            box-shadow: var(--shadow-sm);
        }

        .alert {
            width: 100%;
            max-width: 320px;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            text-align: center;
            animation: fadeOut 5s forwards;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; visibility: hidden; }
        }

        .button-group {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .action-button {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .primary-button {
            background: var(--primary-color);
            color: white;
        }

        .primary-button:hover {
            background: #1d4ed8;
        }

        .secondary-button {
            background: var(--secondary-color);
            color: var(--text-color);
            border: 1px solid #e2e8f0;
        }

        .secondary-button:hover {
            background: #f1f5f9;
        }

        .upload-button {
            position: relative;
            overflow: hidden;
        }

        .upload-button::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(14, 165, 233, 0.1));
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .upload-button:hover::after {
            opacity: 1;
        }

        .upload-hint {
            position: absolute;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.875rem;
            color: #64748b;
            white-space: nowrap;
            pointer-events: none;
        }

        .upload-zone {
            min-height: 200px;
            max-width: 280px;
            aspect-ratio: 4/3;
            margin: 1rem auto;
            padding: 1.5rem;
            border: 2px dashed #ccc;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
        }

        .camera-icon {
            width: 48px;
            height: 48px;
            margin-bottom: 0.75rem;
        }

        .format-info {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #666;
        }

        .results-card {
            margin-top: 1rem;
            padding: 1.25rem;
            border-radius: 12px;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="app-container">
        <header class="app-header">
            <h1 class="adidas-style">🍽️ NutriCheck</h1>
            <p>Quickly analyze your meals for better health</p>
        </header>

        <main class="main-content">
            <?php if ($message) : ?>
                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                    <?php echo $messageType === 'success' ? '✅ ' : '❌ '; ?><?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="upload-button" id="uploadButton">
                    <span class="icon">📸</span>
                    <span class="text">Upload or Capture Your Meal</span>
                    <span class="subtext" id="uploadSubtext">
                        Drop your food image here or click to choose
                    </span>
                    <input 
                        type="file" 
                        class="d-none" 
                        id="foodImage" 
                        name="foodImage" 
                        accept="image/*" 
                        required
                    >
                </div>

                <div id="preview-container" class="preview-container">
                    <img id="preview" alt="Preview" class="preview-image">
                    <div class="preview-overlay">
                        <p>Analyzing your food...</p>
                        <div class="progress">
                            <div class="progress-bar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <div class="footer-info">
                    <p>Supported formats: JPG, PNG, GIF, WebP</p>
                    <p>Maximum file size: 10MB</p>
                </div>

                <?php if ($analysisResult) : ?>
                <div class="result-card" id="resultCard" style="display: block;">
                    <div class="result-header">
                        <h3>🍽️ Analysis Results</h3>
                        <span class="confidence">
                            <span>🎯</span>
                            <span><?php echo $analysisResult['confidence']; ?> confidence</span>
                        </span>
                    </div>
                    
                    <div class="total-metrics">
                        <h4>Total Nutritional Value</h4>
                        <div class="metrics-grid">
                            <div class="metric">
                                <div class="metric-label">🔥 Calories</div>
                                <div class="metric-value"><?php echo $analysisResult['total']['calories']; ?></div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">🥩 Protein</div>
                                <div class="metric-value"><?php echo $analysisResult['total']['protein']; ?></div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">🍚 Carbs</div>
                                <div class="metric-value"><?php echo $analysisResult['total']['carbs']; ?></div>
                            </div>
                            <div class="metric">
                                <div class="metric-label">🥑 Fat</div>
                                <div class="metric-value"><?php echo $analysisResult['total']['fat']; ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="food-items">
                        <h4>Detected Food Items</h4>
                        <?php foreach ($analysisResult['items'] as $item): ?>
                        <div class="food-item">
                            <div class="food-item-header">
                                <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                                <span class="portion"><?php echo htmlspecialchars($item['portion']); ?></span>
                            </div>
                            <div class="food-item-metrics">
                                <span>🔥 <?php echo $item['calories']; ?> cal</span>
                                <span>🥩 <?php echo $item['protein']; ?></span>
                                <span>🍚 <?php echo $item['carbs']; ?></span>
                                <span>🥑 <?php echo $item['fat']; ?></span>
                            </div>
                            <div class="ingredients">
                                <h6>Ingredients:</h6>
                                <div class="ingredients-list">
                                    <?php foreach ($item['ingredients'] as $ingredient): ?>
                                    <span class="ingredient-tag"><?php echo htmlspecialchars($ingredient); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="button-group">
                        <button type="button" class="action-button secondary-button" onclick="resetForm()">
                            ← Back to Camera
                        </button>
                        <button type="button" class="action-button primary-button" onclick="retakePhoto()">
                            📸 Take New Photo
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
            }

            // Detect device capabilities
            const isMobile = /iPhone|iPad|iPod|Android/i.test(
                navigator.userAgent
            );
            const hasCamera = navigator.mediaDevices && 
                navigator.mediaDevices.getUserMedia;

            // Update upload text based on device
            if (isMobile && hasCamera) {
                uploadSubtext.textContent = 'Tap to take a photo';
                fileInput.setAttribute('capture', 'environment');
            }

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
                    <div class="alert alert-danger">
                        ❌ ${message}
                    </div>
                `;
                const mainContent = document.querySelector('.main-content');
                mainContent.insertAdjacentHTML('afterbegin', alertHtml);
                setTimeout(() => {
                    const alert = document.querySelector('.alert');
                    if (alert) alert.remove();
                }, 5000);
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
    </script>
</body>
</html>

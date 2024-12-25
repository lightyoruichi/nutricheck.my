<?php
// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Handle file upload
$message = '';
$messageType = '';
$analysisResult = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foodImage'])) {
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
        
        if (!in_array($mimeType, ALLOWED_TYPES)) {
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
            'name' => 'Sample Food',
            'calories' => rand(100, 500),
            'protein' => rand(5, 30) . 'g',
            'carbs' => rand(10, 50) . 'g',
            'fat' => rand(5, 25) . 'g',
            'confidence' => rand(75, 98) . '%'
        ];
        
        $message = 'Analysis complete!';
        $messageType = 'success';
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍽️ NutriCheck</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #f1f5f9;
            --accent-color: #0ea5e9;
            --text-color: #0f172a;
            --border-radius: 16px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        
        body {
            background-color: var(--secondary-color);
            color: var(--text-color);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .app-container {
            max-width: 480px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
            position: relative;
            box-shadow: var(--shadow-md);
        }

        .app-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--secondary-color);
            background: white;
            position: sticky;
            top: 0;
            z-index: 100;
            text-align: center;
        }

        .app-header h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .upload-container {
            padding: 1.25rem;
        }

        .upload-zone {
            background: var(--secondary-color);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px dashed #cbd5e1;
            position: relative;
            overflow: hidden;
        }

        .upload-zone:hover, .upload-zone:focus {
            border-color: var(--primary-color);
            background: #f8fafc;
        }

        .upload-zone .upload-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        #preview-container {
            margin: 1rem 0;
            border-radius: var(--border-radius);
            overflow: hidden;
            position: relative;
            box-shadow: var(--shadow-md);
        }

        #preview {
            width: 100%;
            height: auto;
            display: block;
            object-fit: cover;
        }

        .action-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem 1.25rem calc(1rem + env(safe-area-inset-bottom));
            background: white;
            box-shadow: 0 -1px 2px rgba(0,0,0,0.05);
            display: flex;
            gap: 0.75rem;
            max-width: 480px;
            margin: 0 auto;
            z-index: 100;
        }

        .action-button {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 12px;
            background: var(--secondary-color);
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .action-button:hover, .action-button:focus {
            background: #e2e8f0;
        }

        .action-button.primary {
            background: var(--primary-color);
            color: white;
        }

        .action-button.primary:hover, .action-button.primary:focus {
            background: #1d4ed8;
        }

        .action-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .result-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.25rem;
            margin: 1rem 0;
            box-shadow: var(--shadow-sm);
        }

        .nutrition-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .nutrition-item {
            background: var(--secondary-color);
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            transition: all 0.2s ease;
        }

        .nutrition-item:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .nutrition-item h5 {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .nutrition-item p {
            font-size: 1.125rem;
            font-weight: 600;
            margin: 0;
            color: var(--text-color);
        }

        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: var(--shadow-sm);
        }

        .progress {
            height: 0.5rem;
            border-radius: 1rem;
            overflow: hidden;
            background: var(--secondary-color);
        }

        .progress-bar {
            background: var(--primary-color);
        }

        @media (max-width: 480px) {
            .app-container {
                min-height: 100vh;
                box-shadow: none;
            }
            
            .upload-container {
                padding-bottom: 5rem;
            }
        }

        @supports (padding: max(0px)) {
            .action-bar {
                padding-bottom: max(1rem, env(safe-area-inset-bottom));
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <header class="app-header">
            <h1>🍽️ NutriCheck</h1>
        </header>

        <main class="upload-container">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                    <?php echo $messageType === 'success' ? '✅ ' : '❌ '; ?><?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
                <div class="upload-zone" id="dropZone">
                    <div class="upload-icon">📸</div>
                    <input type="file" class="form-control d-none" id="foodImage" name="foodImage" accept="image/*" required>
                    <p class="mb-0" id="uploadText">Drop your food image here</p>
                    <small class="text-muted" id="uploadSubText">or click to choose</small>
                </div>
                
                <div id="preview-container" style="display: none;">
                    <img id="preview" alt="Preview">
                </div>

                <div class="progress mb-3" style="display: none;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div>
                </div>
            </form>

            <?php if ($analysisResult): ?>
            <div class="result-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h5 mb-0">🍽️ <?php echo htmlspecialchars($analysisResult['name']); ?></h3>
                    <span class="badge bg-success">🎯 <?php echo $analysisResult['confidence']; ?></span>
                </div>
                
                <div class="nutrition-grid">
                    <div class="nutrition-item">
                        <h5>🔥 Calories</h5>
                        <p><?php echo $analysisResult['calories']; ?></p>
                    </div>
                    <div class="nutrition-item">
                        <h5>🥩 Protein</h5>
                        <p><?php echo $analysisResult['protein']; ?></p>
                    </div>
                    <div class="nutrition-item">
                        <h5>🍚 Carbs</h5>
                        <p><?php echo $analysisResult['carbs']; ?></p>
                    </div>
                    <div class="nutrition-item">
                        <h5>🥑 Fat</h5>
                        <p><?php echo $analysisResult['fat']; ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>

        <div class="action-bar">
            <button type="button" class="action-button" onclick="document.getElementById('foodImage').click()">
                📸 Camera
            </button>
            <button type="button" class="action-button" onclick="document.getElementById('foodImage').click()">
                🖼️ Gallery
            </button>
            <button type="submit" class="action-button primary" form="uploadForm" id="analyzeBtn" disabled>
                🔍 Analyze
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('uploadForm');
            const fileInput = document.getElementById('foodImage');
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('preview-container');
            const progress = document.querySelector('.progress');
            const progressBar = document.querySelector('.progress-bar');
            const dropZone = document.getElementById('dropZone');
            const analyzeBtn = document.getElementById('analyzeBtn');

            // Click handling
            dropZone.addEventListener('click', () => fileInput.click());

            // Drag and drop handling
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                dropZone.classList.add('dragover');
            }

            function unhighlight(e) {
                dropZone.classList.remove('dragover');
            }

            dropZone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput.files = files;
                handleFiles(files);
            }

            function handleFiles(files) {
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            previewContainer.style.display = 'block';
                            dropZone.style.display = 'none';
                            analyzeBtn.disabled = false;
                        }
                        reader.readAsDataURL(file);
                    }
                }
            }

            // File input change
            fileInput.addEventListener('change', function() {
                handleFiles(this.files);
            });

            // Form submission
            form.addEventListener('submit', function() {
                if (fileInput.files.length > 0) {
                    progress.style.display = 'flex';
                    let width = 0;
                    const interval = setInterval(function() {
                        if (width >= 100) {
                            clearInterval(interval);
                        } else {
                            width++;
                            progressBar.style.width = width + '%';
                            progressBar.setAttribute('aria-valuenow', width);
                        }
                    }, 20);
                }
            });

            // Detect mobile device and camera
            const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
            const hasCamera = navigator.mediaDevices && navigator.mediaDevices.getUserMedia;
            
            // Update upload text and camera access
            const uploadText = document.getElementById('uploadText');
            const uploadSubText = document.getElementById('uploadSubText');
            const cameraBtn = document.querySelector('.action-button:first-child');
            const galleryBtn = document.querySelector('.action-button:nth-child(2)');
            
            if (isMobile && hasCamera) {
                uploadText.textContent = 'Take a photo of your food';
                uploadSubText.textContent = 'or choose from gallery';
                
                // Set up camera button
                cameraBtn.addEventListener('click', () => {
                    fileInput.setAttribute('capture', 'environment');
                    fileInput.click();
                });
                
                // Set up gallery button
                galleryBtn.addEventListener('click', () => {
                    fileInput.removeAttribute('capture');
                    fileInput.click();
                });
            } else {
                uploadText.textContent = 'Drop your food image here';
                uploadSubText.textContent = 'or click to choose';
                cameraBtn.style.display = 'none';
                galleryBtn.style.display = 'none';
                document.querySelector('.action-button.primary').style.flex = '1';
            }
        });
    </script>
</body>
</html>

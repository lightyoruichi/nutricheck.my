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
        if ($file['error'] !== UPLOAD_ERROR_OK) {
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
    <title>NutriCheck - Food Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #212529;
        }
        .upload-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        #preview {
            max-width: 100%;
            max-height: 300px;
            margin: 20px 0;
            border-radius: 8px;
            display: none;
        }
        .progress {
            display: none;
            margin: 20px 0;
        }
        .result-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-top: 20px;
            padding: 20px;
        }
        .confidence-badge {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9em;
        }
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="">NutriCheck</a>
        </div>
    </nav>

    <main class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="upload-container p-4">
                    <h1 class="text-center mb-4">Food Analysis</h1>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
                        <div class="mb-3">
                            <label for="foodImage" class="form-label">Upload Food Image</label>
                            <input type="file" class="form-control" id="foodImage" name="foodImage" accept="image/*" required>
                        </div>
                        
                        <img id="preview" class="mx-auto d-block" alt="Preview">
                        
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Analyze Food</button>
                        </div>
                    </form>
                </div>

                <?php if ($analysisResult): ?>
                <div class="result-card">
                    <h3 class="mb-3">Analysis Results</h3>
                    <span class="confidence-badge float-end"><?php echo $analysisResult['confidence']; ?> confidence</span>
                    <p class="lead mb-4"><?php echo htmlspecialchars($analysisResult['name']); ?></p>
                    
                    <div class="row">
                        <div class="col-6 col-md-3 mb-3">
                            <h5>Calories</h5>
                            <p class="lead"><?php echo $analysisResult['calories']; ?></p>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <h5>Protein</h5>
                            <p class="lead"><?php echo $analysisResult['protein']; ?></p>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <h5>Carbs</h5>
                            <p class="lead"><?php echo $analysisResult['carbs']; ?></p>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <h5>Fat</h5>
                            <p class="lead"><?php echo $analysisResult['fat']; ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('uploadForm');
            const fileInput = document.getElementById('foodImage');
            const preview = document.getElementById('preview');
            const progress = document.querySelector('.progress');
            const progressBar = document.querySelector('.progress-bar');

            // Image preview
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Form submission with progress bar
            form.addEventListener('submit', function() {
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
            });
        });
    </script>
</body>
</html>

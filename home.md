# Home Page Layout 🎮

## Header Section
```html
<header class="header" role="banner">
    <div class="container">
        <a href="https://nutricheck.my" class="logo-link" aria-label="Go to Nutricheck homepage">
            <span class="logo-icon">🍽️</span>
            <span class="logo-text">nutricheck.my</span>
            <span class="logo-sparkle" aria-hidden="true">✨</span>
        </a>
        <p class="tagline">Your personal food analyst. Ready to level up your health!</p>
    </div>
</header>

<style>
.header {
    background: linear-gradient(135deg, #f0f7ff 0%, #e6f3ff 100%);
    padding: 1.5rem 1rem;
}

.logo-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    text-decoration: none;
    transition: color 0.3s ease;
}

.logo-sparkle {
    display: inline-block;
    transition: transform 0.3s ease;
}

.logo-link:hover .logo-sparkle {
    transform: scale(1.1) rotate(3deg);
}

.tagline {
    margin-top: 0.5rem;
    font-size: 1.25rem;
    color: #4a5568;
}

@media (max-width: 768px) {
    .header {
        text-align: center;
    }
    
    .logo-link {
        font-size: 1.75rem;
    }
    
    .tagline {
        font-size: 1.125rem;
    }
}
</style>
```

## Upload Section
```html
<section class="upload-section" role="main" aria-labelledby="upload-title">
    <div class="container">
        <div class="upload-card">
            <span class="upload-icon" aria-hidden="true">📸</span>
            <h1 id="upload-title">Upload or Capture Your Meal</h1>
            <p class="upload-description">Drop your food pic here or tap to take a photo!</p>
            
            <form action="/upload.php" method="post" enctype="multipart/form-data" class="upload-form">
                <input type="hidden" name="MAX_FILE_SIZE" value="10485760"><!-- 10MB -->
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <label for="file-upload" class="upload-zone" tabindex="0">
                    <div class="upload-content">
                        <span class="upload-arrow" aria-hidden="true">⬆️</span>
                        <p class="upload-formats">Supported: JPG, PNG, GIF, WebP</p>
                        <p class="upload-limit">Max size: 10MB—because we all have limits!</p>
                    </div>
                    <input 
                        type="file" 
                        id="file-upload" 
                        name="food_image" 
                        accept="image/*" 
                        class="file-input"
                        aria-label="Choose a food image to upload"
                    >
                </label>
                
                <div id="preview-container" class="preview-container" hidden>
                    <img id="image-preview" src="" alt="Preview of your food image" class="preview-image">
                    <button type="button" class="remove-preview" aria-label="Remove uploaded image">✕</button>
                </div>
                
                <div id="upload-error" class="upload-error" role="alert" hidden></div>
            </form>
        </div>
    </div>
</section>

<style>
.upload-section {
    padding: 2rem 1rem;
}

.upload-card {
    max-width: 600px;
    margin: 0 auto;
    padding: 2rem;
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.upload-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.upload-zone {
    display: block;
    padding: 2rem;
    margin: 1.5rem 0;
    border: 2px dashed #cbd5e0;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-zone:hover,
.upload-zone:focus {
    border-color: #4299e1;
    background: #f7fafc;
    outline: none;
}

.upload-zone:focus-visible {
    box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
}

.file-input {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}

.preview-container {
    position: relative;
    margin-top: 1rem;
}

.preview-image {
    max-width: 100%;
    max-height: 300px;
    border-radius: 0.5rem;
}

.remove-preview {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    padding: 0.5rem;
    background: rgba(0, 0, 0, 0.5);
    color: #fff;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    transition: background 0.3s ease;
}

.remove-preview:hover {
    background: rgba(0, 0, 0, 0.7);
}

.upload-error {
    margin-top: 1rem;
    padding: 0.75rem;
    background: #fed7d7;
    color: #c53030;
    border-radius: 0.5rem;
}

@media (max-width: 480px) {
    .upload-card {
        padding: 1.5rem;
    }
    
    .upload-zone {
        padding: 1.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.querySelector('.upload-form');
    const fileInput = document.getElementById('file-upload');
    const previewContainer = document.getElementById('preview-container');
    const imagePreview = document.getElementById('image-preview');
    const errorDisplay = document.getElementById('upload-error');
    const uploadZone = document.querySelector('.upload-zone');
    
    // Handle drag and drop
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('upload-zone-active');
    });
    
    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('upload-zone-active');
    });
    
    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('upload-zone-active');
        handleFiles(e.dataTransfer.files);
    });
    
    // Handle file selection
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
    
    function handleFiles(files) {
        const file = files[0];
        
        if (!file) return;
        
        // Validate file type and size
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        const maxSize = 10 * 1024 * 1024; // 10MB
        
        if (!validTypes.includes(file.type)) {
            showError('Please upload a valid image file (JPG, PNG, GIF, or WebP).');
            return;
        }
        
        if (file.size > maxSize) {
            showError('File size exceeds 10MB limit. Please choose a smaller image.');
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.src = e.target.result;
            previewContainer.hidden = false;
            errorDisplay.hidden = true;
        };
        reader.readAsDataURL(file);
    }
    
    function showError(message) {
        errorDisplay.textContent = message;
        errorDisplay.hidden = false;
        previewContainer.hidden = true;
    }
    
    // Remove preview
    document.querySelector('.remove-preview').addEventListener('click', () => {
        fileInput.value = '';
        previewContainer.hidden = true;
        errorDisplay.hidden = true;
    });
    
    // Handle keyboard interaction
    uploadZone.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            fileInput.click();
        }
    });
});
</script>
```

## Features Grid
```html
<section class="features" aria-labelledby="features-title">
    <div class="container">
        <h2 id="features-title" class="features-title">Why Choose Nutricheck?</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <span class="feature-icon" aria-hidden="true">📱</span>
                <h3>Mobile Ready</h3>
                <p>Use your camera instantly</p>
            </div>
            
            <div class="feature-card">
                <span class="feature-icon" aria-hidden="true">⚡</span>
                <h3>Lightning Fast</h3>
                <p>Analysis in under 10 seconds</p>
            </div>
            
            <div class="feature-card">
                <span class="feature-icon" aria-hidden="true">🎯</span>
                <h3>High Accuracy</h3>
                <p>Insights you can trust</p>
            </div>
            
            <div class="feature-card">
                <span class="feature-icon" aria-hidden="true">🟢</span>
                <h3>Halal Certified</h3>
                <p>Peace of mind guaranteed</p>
            </div>
        </div>
    </div>
</section>

<style>
.features {
    padding: 3rem 1rem;
    background: #f7fafc;
}

.features-title {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 2rem;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
}

.feature-card {
    padding: 1.5rem;
    background: #fff;
    border-radius: 0.75rem;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.feature-card h3 {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.feature-card p {
    color: #4a5568;
}

@media (max-width: 768px) {
    .features-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}
</style>
```

## Loading State
```html
<div id="loading-overlay" class="loading-overlay" hidden>
    <div class="loading-content">
        <p class="loading-text">Analyzing your meal...</p>
        <div class="loading-bar">
            <div class="loading-progress"></div>
        </div>
    </div>
</div>

<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.loading-content {
    text-align: center;
}

.loading-text {
    font-size: 1.25rem;
    margin-bottom: 1rem;
}

.loading-bar {
    width: 200px;
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
}

.loading-progress {
    width: 50%;
    height: 100%;
    background: #4299e1;
    animation: progress 2s infinite;
}

@keyframes progress {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(200%); }
}
</style>
```

## PHP Functions
```php
<?php
// CSRF Protection
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// File Upload Handling
function handleFileUpload($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 10 * 1024 * 1024; // 10MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Please upload a JPG, PNG, GIF, or WebP image.');
    }
    
    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds 10MB limit.');
    }
    
    $uploadDir = 'uploads/';
    $fileName = uniqid() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to upload file. Please try again.');
    }
    
    return $targetPath;
}

// Error Handling
function displayError($message) {
    return json_encode(['error' => $message]);
}

// Success Response
function displaySuccess($data) {
    return json_encode(['success' => true, 'data' => $data]);
}
?>
```
# Home Page Layout 🎮

## Header Section
The header section contains the NutriCheck logo, tagline, and feature indicators. The feature indicators use emojis with tooltips to save space while providing information about the app's capabilities.

```html
<header class="app-header" role="banner">
    <div class="container">
        <a href="/" class="app-logo" aria-label="Go to NutriCheck homepage">
            <span class="logo-icon">🍽️</span>
            <span class="logo-text">nutricheck.my</span>
            <span class="logo-sparkle" aria-hidden="true">✨</span>
        </a>
        <p class="tagline">Your personal food analyst. Ready to level up your health!</p>
        <div class="feature-pills" role="list" aria-label="App features">
            <span class="feature-pill" role="listitem" data-tooltip="Instant camera access">📱</span>
            <span class="feature-pill" role="listitem" data-tooltip="Analysis in seconds">⚡</span>
            <span class="feature-pill" role="listitem" data-tooltip="High accuracy results">🎯</span>
            <span class="feature-pill" role="listitem" data-tooltip="Halal certified">🟢</span>
        </div>
    </div>
</header>
```

## Upload Section
The upload section provides a simple interface for users to upload or capture food images. It includes:
- Drag and drop support
- Image preview with compression
- File type and size validation
- Loading indicators
- Error handling

```html
<section class="upload-section" role="main" aria-labelledby="upload-title">
    <div class="container">
        <form id="uploadForm" action="/analyze" method="post" enctype="multipart/form-data">
            <input type="hidden" name="MAX_FILE_SIZE" value="10485760">
            
            <button type="button" id="uploadButton" class="upload-button" aria-label="Upload or take a photo of your meal">
                <span class="upload-icon" aria-hidden="true">📸</span>
                <span class="upload-text">Upload or Take Photo</span>
                <span class="upload-info">JPG, PNG, WebP • Max 10MB</span>
            </button>
            
            <input type="file" id="foodImage" name="foodImage" accept="image/*" capture="environment" class="visually-hidden">
            
            <div id="previewContainer" class="preview-container">
                <img id="previewImage" src="" alt="Preview of your meal" class="preview-image">
                <div class="preview-actions">
                    <button type="button" onclick="submitImage()" class="action-button submit-button">
                        <span class="action-icon">✅</span> Analyze
                    </button>
                    <button type="button" onclick="retakePhoto()" class="action-button retake-button">
                        <span class="action-icon">🔄</span> Retake
                    </button>
                    <button type="button" onclick="cancelPreview()" class="action-button cancel-button">
                        <span class="action-icon">❌</span> Cancel
                    </button>
                </div>
            </div>
            
            <div id="analyzingContainer" class="analyzing-container">
                <div class="spinner"></div>
                <p>Analyzing your meal... 🔍</p>
            </div>
        </form>
    </div>
</section>
```

## Offline Support
The app includes offline detection and a service worker for progressive enhancement:

```html
<div id="offlineBadge" class="offline-badge" role="alert">
    <span class="offline-icon">📡</span>
    <span class="offline-text">You're offline</span>
</div>
```

## Loading States
Loading states are shown during image upload and analysis:

```css
.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e2e8f0;
    border-top-color: #4299e1;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.analyzing-container {
    display: none;
    text-align: center;
    padding: 2rem;
}

.analyzing-container.active {
    display: block;
}
```

## Error Handling
User-friendly error messages are displayed for various scenarios:
- File size exceeded
- Invalid file type
- Upload errors
- Network issues
- Analysis failures

## Accessibility
The app follows accessibility best practices:
- Semantic HTML structure
- ARIA labels and roles
- Keyboard navigation
- Focus management
- Screen reader support
- High contrast support

## Performance
Performance optimizations include:
- Image compression before upload
- Lazy loading of images
- Efficient DOM updates
- Service worker for caching
- Gzip compression
- Browser caching
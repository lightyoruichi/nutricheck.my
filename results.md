# Results Page Layout 🎮

## Results Card
The results card is designed to be screenshot-friendly and shareable on social media. It includes:
- Analysis confidence score
- Meal preview image
- Nutritional information
- Detected food items
- Share button

```html
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
                    <div class="confidence-fill" style="width: <?php echo $confidence; ?>%"></div>
                </div>
                <span class="confidence-value"><?php echo $confidence; ?>%</span>
            </div>
            <time class="timestamp">Analyzed <?php echo date('M j, Y'); ?></time>
        </div>
    </header>

    <div class="meal-preview">
        <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Your meal" class="meal-image" loading="lazy">
        <div class="meal-overlay">
            <span class="meal-badge">✨ Epic Meal Analysis</span>
        </div>
    </div>

    <div class="nutrition-grid">
        <div class="nutrition-stat">
            <span class="stat-icon" aria-hidden="true">🔥</span>
            <div class="stat-details">
                <span class="stat-value"><?php echo number_format($calories); ?></span>
                <span class="stat-label">calories</span>
            </div>
        </div>
        <div class="nutrition-stat">
            <span class="stat-icon" aria-hidden="true">💪</span>
            <div class="stat-details">
                <span class="stat-value"><?php echo number_format($protein, 1); ?>g</span>
                <span class="stat-label">protein</span>
            </div>
        </div>
        <div class="nutrition-stat">
            <span class="stat-icon" aria-hidden="true">🌾</span>
            <div class="stat-details">
                <span class="stat-value"><?php echo number_format($carbs, 1); ?>g</span>
                <span class="stat-label">carbs</span>
            </div>
        </div>
        <div class="nutrition-stat">
            <span class="stat-icon" aria-hidden="true">🥑</span>
            <div class="stat-details">
                <span class="stat-value"><?php echo number_format($fats, 1); ?>g</span>
                <span class="stat-label">fats</span>
            </div>
        </div>
    </div>

    <div class="ingredients-list">
        <h2>🍽️ Detected Items</h2>
        <ul>
            <?php foreach ($items as $item): ?>
            <li>
                <span class="ingredient-name"><?php echo htmlspecialchars($item['name']); ?></span>
                <span class="ingredient-amount"><?php echo htmlspecialchars($item['portion']); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <footer class="result-footer">
        <button type="button" id="shareButton" class="share-button">
            <span class="share-icon">📤</span>
            Share Results
        </button>
    </footer>
</div>
```

## Share Functionality
The share button triggers a screenshot of the results card and offers multiple sharing options:
- Native share API (mobile)
- Download image
- Copy text summary

```javascript
async function shareResults() {
    const shareButton = document.getElementById('shareButton');
    if (!shareButton) return;

    try {
        shareButton.disabled = true;
        shareButton.innerHTML = '<span class="spinner"></span> Generating...';
        
        const resultsCard = document.querySelector('.result-card');
        if (!resultsCard) throw new Error('No results to share');

        const canvas = await takeScreenshot(resultsCard);
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
            link.click();
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
```

## Accessibility
The results card follows accessibility best practices:
- Semantic HTML structure
- ARIA labels for icons
- Keyboard navigation
- Screen reader support
- High contrast support

## Performance
Performance optimizations include:
- Lazy loading of meal image
- Efficient DOM updates
- Optimized screenshot generation
- Browser caching
- Gzip compression
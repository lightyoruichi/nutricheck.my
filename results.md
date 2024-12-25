# Results Page Layout 🎮

## Screenshot-Optimized View
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
                    <div class="confidence-fill" style="width: 92%"></div>
                </div>
                <span class="confidence-value">92%</span>
            </div>
            <time class="timestamp">Analyzed on <?php echo date('M j, Y'); ?></time>
        </div>
    </header>

    <div class="meal-preview">
        <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Your meal" class="meal-image">
        <div class="meal-overlay">
            <span class="meal-badge">✨ Epic Meal Analysis</span>
        </div>
    </div>

    <div class="nutrition-grid">
        <div class="nutrition-stat">
            <span class="stat-icon">🔥</span>
            <div class="stat-details">
                <span class="stat-value"><?php echo number_format($calories); ?></span>
                <span class="stat-label">calories</span>
            </div>
        </div>
        <div class="nutrition-stat">
            <span class="stat-icon">💪</span>
            <div class="stat-details">
                <span class="stat-value"><?php echo number_format($protein, 1); ?>g</span>
                <span class="stat-label">protein</span>
            </div>
        </div>
        <div class="nutrition-stat">
            <span class="stat-icon">🌾</span>
            <div class="stat-details">
                <span class="stat-value"><?php echo number_format($carbs, 1); ?>g</span>
                <span class="stat-label">carbs</span>
            </div>
        </div>
        <div class="nutrition-stat">
            <span class="stat-icon">🥑</span>
            <div class="stat-details">
                <span class="stat-value"><?php echo number_format($fats, 1); ?>g</span>
                <span class="stat-label">fats</span>
            </div>
        </div>
    </div>

    <div class="ingredients-list">
        <h2>🍽️ Detected Items</h2>
        <ul>
            <?php foreach ($ingredients as $item): ?>
            <li>
                <span class="ingredient-icon"><?php echo $item['icon']; ?></span>
                <span class="ingredient-name"><?php echo htmlspecialchars($item['name']); ?></span>
                <span class="ingredient-amount"><?php echo htmlspecialchars($item['amount']); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <footer class="result-footer">
        <div class="qr-code">
            <!-- QR code for the detailed results -->
        </div>
        <div class="footer-text">
            <p>Scan for full analysis at nutricheck.my</p>
            <p class="footer-tag">🎮 Level up your health with every meal!</p>
        </div>
    </footer>
</div>

<style>
.result-card {
    max-width: 1080px;
    max-height: 1350px;
    margin: 0 auto;
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.result-header {
    padding: 1.5rem;
    background: linear-gradient(135deg, #f0f7ff 0%, #e6f3ff 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.brand {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.5rem;
    font-weight: bold;
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
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
}

.confidence-fill {
    height: 100%;
    background: #4a90e2;
}

.meal-preview {
    position: relative;
    height: 300px;
    background: #f8fafc;
    overflow: hidden;
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
    background: rgba(255, 255, 255, 0.9);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
}

.nutrition-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    padding: 1.5rem;
    background: #f8fafc;
}

.nutrition-stat {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.stat-icon {
    font-size: 1.5rem;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: bold;
    color: #1a1a1a;
    display: block;
}

.stat-label {
    font-size: 0.875rem;
    color: #666;
}

.ingredients-list {
    padding: 1.5rem;
}

.ingredients-list h2 {
    font-size: 1.25rem;
    margin-bottom: 1rem;
}

.ingredients-list ul {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.ingredients-list li {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem;
    background: #f8fafc;
    border-radius: 8px;
}

.ingredient-amount {
    margin-left: auto;
    color: #666;
    font-size: 0.875rem;
}

.result-footer {
    padding: 1.5rem;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    text-align: center;
}

.footer-tag {
    color: #666;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

@media (max-width: 640px) {
    .nutrition-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .ingredients-list ul {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
// Example data structure for ingredients
$ingredients = [
    [
        'icon' => '🍗',
        'name' => 'Grilled Chicken',
        'amount' => '150g'
    ],
    [
        'icon' => '🌾',
        'name' => 'Brown Rice',
        'amount' => '200g'
    ],
    [
        'icon' => '🥦',
        'name' => 'Broccoli',
        'amount' => '100g'
    ],
    [
        'icon' => '🥕',
        'name' => 'Carrots',
        'amount' => '50g'
    ]
];
?>
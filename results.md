# Results Page Layout 🎮

## Header Section
```html
<header class="header" role="banner">
    <div class="container">
        <a href="https://nutricheck.my" class="logo-link" aria-label="Return to Nutricheck homepage">
            🎯 nutricheck.my
            <span class="sparkle" aria-hidden="true">✨</span>
        </a>
        <div class="tagline">Level up your health with every meal!</div>
    </div>
</header>

<style>
.header {
    padding: 1.5rem 1rem;
    text-align: center;
}

@media (min-width: 768px) {
    .header {
        text-align: left;
    }
}

.logo-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 2rem;
    font-weight: bold;
    color: #1a1a1a;
    text-decoration: none;
    transition: color 0.3s ease;
}

.logo-link:hover {
    color: #4a90e2;
}

.sparkle {
    display: inline-block;
    transition: transform 0.3s ease;
}

.logo-link:hover .sparkle {
    transform: scale(1.1) rotate(3deg);
}

.tagline {
    margin-top: 0.5rem;
    font-size: 1.125rem;
    color: #666;
}
</style>
```

## Achievement Banner
```html
<section class="achievement-banner" role="status" aria-label="Analysis Results">
    <div class="container">
        <h1 class="achievement-title">
            🎉 Food Analysis Achievement Unlocked!
        </h1>
        <div class="analysis-level">
            <span>✨ Analysis Power Level:</span>
            <div class="progress-bar">
                <div class="progress-fill" style="width: 92%"></div>
            </div>
            <span class="progress-text">92%</span>
        </div>
        <p class="achievement-message">
            Epic scan complete! Ready to see your meal stats?
        </p>
    </div>
</section>

<style>
.achievement-banner {
    padding: 1.5rem 1rem;
    background: linear-gradient(to right, #f0f9ff, #e6f7ff);
    text-align: center;
}

.achievement-title {
    font-size: 1.875rem;
    font-weight: bold;
    margin-bottom: 1rem;
}

.analysis-level {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin: 1rem 0;
}

.progress-bar {
    width: 100px;
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #4a90e2;
    transition: width 0.5s ease;
}

.progress-text {
    font-weight: bold;
}

.achievement-message {
    color: #4a5568;
    font-size: 1.125rem;
}
</style>
```

## Stats Overview
```html
<section class="stats-overview" aria-labelledby="stats-title">
    <h2 id="stats-title" class="sr-only">Nutrition Statistics Overview</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">🔥</div>
            <h3>Energy Points</h3>
            <div class="stat-value">436 kcal</div>
            <p class="stat-subtitle">Power up your day!</p>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">💪</div>
            <h3>Protein Power</h3>
            <div class="stat-value">39.7g</div>
            <p class="stat-subtitle">Build that character strength!</p>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">⚡</div>
            <h3>Energy Crystals</h3>
            <div class="stat-value">56.2g</div>
            <p class="stat-subtitle">Fuel your next adventure!</p>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🛡️</div>
            <h3>Essential Fats</h3>
            <div class="stat-value">6g</div>
            <p class="stat-subtitle">Boost your defense stats!</p>
        </div>
    </div>
</section>

<style>
.stats-overview {
    padding: 2rem 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
}

@media (min-width: 640px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .stats-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.stat-card {
    padding: 1.5rem;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #4a90e2;
    margin: 0.5rem 0;
}

.stat-subtitle {
    color: #666;
    font-size: 0.875rem;
}
</style>
```

## Inventory Items
```html
<section class="inventory" aria-labelledby="inventory-title">
    <div class="container">
        <h2 id="inventory-title" class="inventory-title">
            🎒 Your Meal Inventory
        </h2>
        <p class="inventory-subtitle">Here's what our Food Scanner detected!</p>
        
        <div class="inventory-grid">
            <?php foreach ($foodItems as $item): ?>
            <div class="inventory-card">
                <div class="item-header">
                    <div class="item-icon"><?php echo htmlspecialchars($item['icon']); ?></div>
                    <div class="item-info">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="item-quantity">Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                    </div>
                </div>
                
                <div class="item-stats">
                    <div class="stat-bar">
                        <span class="stat-icon">🔥</span>
                        <div class="stat-progress">
                            <div class="stat-fill" style="width: <?php echo ($item['stats']['energy'] / 500) * 100; ?>%"></div>
                        </div>
                        <span class="stat-value"><?php echo htmlspecialchars($item['stats']['energy']); ?> HP</span>
                    </div>
                    <!-- Similar bars for protein, carbs, and fats -->
                </div>
                
                <div class="item-ingredients">
                    <p>🌟 Crafted with: <?php echo htmlspecialchars(implode(', ', $item['ingredients'])); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
.inventory {
    padding: 2rem 1rem;
}

.inventory-title {
    font-size: 1.5rem;
    font-weight: bold;
    text-align: center;
    margin-bottom: 0.5rem;
}

.inventory-subtitle {
    text-align: center;
    color: #666;
    margin-bottom: 2rem;
}

.inventory-grid {
    display: grid;
    gap: 1.5rem;
    max-width: 800px;
    margin: 0 auto;
}

.inventory-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.item-header {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.item-icon {
    font-size: 2.5rem;
}

.item-info h3 {
    font-size: 1.25rem;
    font-weight: bold;
}

.item-quantity {
    color: #666;
}

.stat-bar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0.5rem 0;
}

.stat-progress {
    flex: 1;
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
}

.stat-fill {
    height: 100%;
    background: #4a90e2;
    transition: width 0.3s ease;
}

.stat-value {
    font-size: 0.875rem;
    min-width: 4rem;
}

.item-ingredients {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
    font-size: 0.875rem;
    color: #666;
}
</style>
```

## Quick Actions
```html
<section class="quick-actions" aria-labelledby="actions-title">
    <h2 id="actions-title" class="sr-only">Quick Actions</h2>
    
    <div class="actions-grid">
        <a href="/modify" class="action-button">
            <span class="action-icon">🔄</span>
            <span class="action-text">Modify Quest</span>
        </a>
        
        <a href="/new" class="action-button">
            <span class="action-icon">📸</span>
            <span class="action-text">New Adventure</span>
        </a>
        
        <a href="/share" class="action-button">
            <span class="action-icon">🏆</span>
            <span class="action-text">Share Victory</span>
        </a>
        
        <a href="/save" class="action-button">
            <span class="action-icon">📜</span>
            <span class="action-text">Save to Codex</span>
        </a>
    </div>
</section>

<style>
.quick-actions {
    padding: 2rem 1rem;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

@media (min-width: 640px) {
    .actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .actions-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.action-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    color: #1a1a1a;
    text-decoration: none;
    transition: all 0.3s ease;
}

.action-button:hover {
    background: #f7fafc;
    transform: translateY(-2px);
}

.action-icon {
    font-size: 1.25rem;
}

.action-text {
    font-weight: 500;
}
</style>
```

## Footer
```html
<footer class="footer" role="contentinfo">
    <div class="container">
        <p class="footer-text">✨ Achievement analyzed in 10s flat!</p>
        <p class="footer-text">🎮 Keep leveling up your nutrition game!</p>
    </div>
</footer>

<style>
.footer {
    padding: 1rem;
    background: #f7fafc;
    text-align: center;
}

.footer-text {
    color: #666;
    font-size: 0.875rem;
    margin: 0.25rem 0;
}
</style>
```

## PHP Functions
```php
<?php
// Data formatting functions
function formatNutritionValue($value, $unit) {
    return number_format($value, 1) . $unit;
}

function calculatePercentage($value, $max) {
    return min(($value / $max) * 100, 100);
}

// Example data structure
$foodItems = [
    [
        'icon' => '🍗',
        'name' => 'Epic Grilled Chicken',
        'quantity' => '1 piece (150g)',
        'stats' => [
            'energy' => 165,
            'protein' => 31,
            'carbs' => 0,
            'fats' => 3.6
        ],
        'ingredients' => ['Premium chicken breast', 'Sacred olive oil', 'Rare herbs & seasonings']
    ],
    // Add more items as needed
];

// Helper function for accessibility
function generateAriaLabel($stats) {
    return sprintf(
        'Contains %s calories, %s protein, %s carbohydrates, and %s fat',
        formatNutritionValue($stats['energy'], 'kcal'),
        formatNutritionValue($stats['protein'], 'g'),
        formatNutritionValue($stats['carbs'], 'g'),
        formatNutritionValue($stats['fats'], 'g')
    );
}
?>
# Results Page Components 🎮

## Header Component
```tsx
const Header = () => (
  <header className="px-4 py-6 md:px-6 lg:px-8 text-center md:text-left">
    <Link 
      href="https://nutricheck.my"
      className="group inline-flex items-center gap-2 text-2xl font-bold hover:text-primary-600 transition-all duration-300"
    >
      🎯 nutricheck.my
      <span className="inline-block group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
        ✨
      </span>
    </Link>
    
    <div className="mt-2 text-lg text-gray-600">
      Level up your health with every meal!
    </div>
  </header>
);
```

## Achievement Banner
```tsx
const AchievementBanner = () => (
  <section className="px-4 py-6 md:px-6 lg:px-8 bg-gradient-to-r from-green-50 to-blue-50">
    <div className="container mx-auto text-center">
      <h1 className="text-3xl font-bold mb-2">
        🎉 Food Analysis Achievement Unlocked!
      </h1>
      <div className="flex items-center justify-center gap-2 text-lg">
        <span>✨ Analysis Power Level:</span>
        <div className="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
          <div 
            className="h-full bg-primary-500" 
            style={{ width: "92%" }}
          />
        </div>
        <span>92%</span>
      </div>
      <p className="mt-2 text-gray-600">
        Epic scan complete! Ready to see your meal stats?
      </p>
    </div>
  </section>
);
```

## Stats Grid
```tsx
interface StatCardProps {
  icon: string;
  title: string;
  value: string;
  subtitle: string;
}

const StatCard = ({ icon, title, value, subtitle }: StatCardProps) => (
  <div className="p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow bg-white">
    <div className="text-2xl mb-2">{icon}</div>
    <h3 className="font-semibold text-gray-700">{title}</h3>
    <div className="text-2xl font-bold text-primary-600 my-2">{value}</div>
    <p className="text-sm text-gray-500">{subtitle}</p>
  </div>
);

const StatsOverview = () => (
  <section className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-4 md:p-6 lg:p-8">
    <StatCard
      icon="🔥"
      title="Energy Points"
      value="436 kcal"
      subtitle="Power up your day!"
    />
    <StatCard
      icon="💪"
      title="Protein Power"
      value="39.7g"
      subtitle="Build that character strength!"
    />
    <StatCard
      icon="⚡"
      title="Energy Crystals"
      value="56.2g"
      subtitle="Fuel your next adventure!"
    />
    <StatCard
      icon="🛡️"
      title="Essential Fats"
      value="6g"
      subtitle="Boost your defense stats!"
    />
  </section>
);
```

## Inventory Items
```tsx
interface InventoryItemProps {
  icon: string;
  name: string;
  quantity: string;
  stats: {
    energy: string;
    protein: string;
    carbs: string;
    fats: string;
  };
  ingredients: string[];
}

const InventoryItem = ({ icon, name, quantity, stats, ingredients }: InventoryItemProps) => (
  <div className="p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow bg-white">
    <div className="flex items-start gap-4">
      <div className="text-4xl">{icon}</div>
      <div className="flex-1">
        <h3 className="text-xl font-semibold">{name}</h3>
        <p className="text-gray-600">Quantity: {quantity}</p>
        
        <div className="mt-3 space-y-1">
          <div className="flex items-center gap-2">
            <span>🔥</span>
            <div className="w-full h-2 bg-gray-100 rounded-full">
              <div 
                className="h-full bg-red-400 rounded-full"
                style={{ width: `${(parseInt(stats.energy) / 500) * 100}%` }}
              />
            </div>
            <span className="text-sm">{stats.energy} HP</span>
          </div>
          {/* Similar progress bars for protein, carbs, and fats */}
        </div>
        
        <div className="mt-4">
          <p className="text-sm text-gray-500">
            🌟 Crafted with: {ingredients.join(", ")}
          </p>
        </div>
      </div>
    </div>
  </div>
);
```

## Quick Actions
```tsx
const QuickActions = () => (
  <section className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4 md:p-6 lg:p-8">
    <Button variant="outline" className="flex items-center justify-center gap-2 px-6 py-3">
      <span>🔄</span> Modify Quest
    </Button>
    <Button variant="outline" className="flex items-center justify-center gap-2 px-6 py-3">
      <span>📸</span> New Adventure
    </Button>
    <Button variant="outline" className="flex items-center justify-center gap-2 px-6 py-3">
      <span>🏆</span> Share Victory
    </Button>
    <Button variant="outline" className="flex items-center justify-center gap-2 px-6 py-3">
      <span>📜</span> Save to Codex
    </Button>
  </section>
);
```

## Footer
```tsx
const Footer = () => (
  <footer className="text-center py-4 bg-gray-50">
    <p className="text-sm text-gray-600">
      ✨ Achievement analyzed in 10s flat!
    </p>
    <p className="text-sm text-gray-600 mt-1">
      🎮 Keep leveling up your nutrition game!
    </p>
  </footer>
);
```

## Styling Notes
```css
@layer components {
  .stat-progress {
    @apply w-full h-2 bg-gray-100 rounded-full overflow-hidden;
  }
  
  .stat-progress-bar {
    @apply h-full rounded-full transition-all duration-500;
  }
  
  .achievement-glow {
    @apply animate-pulse;
  }
  
  .hover-scale {
    @apply hover:scale-105 transition-transform duration-300;
  }
}
```

## Accessibility Features
```tsx
// Screen reader announcements
const announceResult = (result: AnalysisResult) => {
  const announcement = `Analysis complete! Your meal contains ${result.calories} calories, 
    ${result.protein} grams of protein, ${result.carbs} grams of carbohydrates, 
    and ${result.fats} grams of fat.`;
  
  return (
    <div className="sr-only" role="status" aria-live="polite">
      {announcement}
    </div>
  );
};

// Keyboard navigation
const KeyboardNav = () => {
  const handleKeyPress = (e: KeyboardEvent) => {
    if (e.key === "ArrowRight") navigateToNextItem();
    if (e.key === "ArrowLeft") navigateToPrevItem();
  };
  
  return <div tabIndex={0} onKeyDown={handleKeyPress} />;
};
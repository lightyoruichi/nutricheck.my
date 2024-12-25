# Home Page Layout and Design 🎮

## Hero Section
```tsx
<section className="bg-gradient-to-r from-primary-50 to-secondary-50 px-4 py-12 md:py-20">
  <div className="container mx-auto text-center">
    <h1 className="text-4xl md:text-6xl font-bold mb-4 animate-fade-in">
      🍽️ nutricheck.my
    </h1>
    <p className="text-xl md:text-2xl text-gray-700 mb-8">
      Your personal food analyst. Ready to level up your health!
    </p>
  </div>
</section>
```

## Upload Section
```tsx
<section className="container mx-auto px-4 py-12">
  <div className="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8 hover:shadow-xl transition-shadow">
    <div className="text-center space-y-4">
      <div className="text-3xl mb-2">📸</div>
      <h2 className="text-2xl font-semibold">Upload or Capture Your Meal</h2>
      <p className="text-gray-600">
        Drop your food pic here or click to summon your camera spell
      </p>
      
      <div className="border-2 border-dashed border-gray-300 rounded-lg p-8 mt-4 hover:border-primary-500 transition-colors">
        <input type="file" className="hidden" id="file-upload" accept="image/*" />
        <label htmlFor="file-upload" className="cursor-pointer">
          <div className="space-y-4">
            <div className="flex justify-center">
              <span className="text-4xl animate-bounce">⬆️</span>
            </div>
            <p className="text-sm text-gray-500">
              Supported: 🖼️ JPG | 📂 PNG | 🎥 GIF | 🌐 WebP
            </p>
            <p className="text-xs text-gray-400">
              Max size: 10MB—because some bosses need limits
            </p>
          </div>
        </label>
      </div>
    </div>
  </div>
</section>
```

## Features Grid
```tsx
<section className="bg-gray-50 px-4 py-12">
  <div className="container mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <FeatureCard
      icon="📱"
      title="Mobile Ready"
      description="Use your camera instantly"
    />
    <FeatureCard
      icon="⚡"
      title="Lightning Fast"
      description="Analysis in under 10 seconds"
    />
    <FeatureCard
      icon="🎯"
      title="High Accuracy"
      description="Insights you can trust"
    />
    <FeatureCard
      icon="🟢"
      title="Halal Certified"
      description="Peace of mind guaranteed"
    />
  </div>
</section>
```

## Preview Section
```tsx
<section className="container mx-auto px-4 py-12">
  <div className="max-w-2xl mx-auto">
    <div className="aspect-video bg-gray-100 rounded-lg overflow-hidden relative">
      <div className="absolute inset-0 flex items-center justify-center">
        <div className="text-center space-y-4">
          <p className="text-lg text-gray-600">Analyzing your meal...</p>
          <div className="w-48 h-2 bg-gray-200 rounded-full overflow-hidden">
            <div className="w-1/2 h-full bg-primary-500 animate-progress"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
```

## Technical Implementation Notes

### Performance Optimizations
- Use Next.js Image component for optimized images
- Implement lazy loading for below-the-fold content
- Use React Suspense boundaries for code splitting
- Implement progressive image loading

### Accessibility Features
```tsx
// Focus management
const uploadButton = {
  role: "button",
  tabIndex: 0,
  "aria-label": "Upload or capture food image",
  onKeyPress: (e) => e.key === "Enter" && handleUpload(),
};

// Screen reader optimizations
const progressStatus = {
  role: "progressbar",
  "aria-valuenow": progress,
  "aria-valuemin": 0,
  "aria-valuemax": 100,
};
```

### Error States
```tsx
const ErrorDisplay = ({ error }) => (
  <div role="alert" className="text-red-500 p-4 rounded bg-red-50">
    <p className="font-semibold">❌ {error.title}</p>
    <p className="text-sm">{error.message}</p>
  </div>
);
```

### Mobile Optimizations
```tsx
const deviceDetection = {
  isMobile: /iPhone|iPad|iPod|Android/i.test(navigator.userAgent),
  hasCamera: !!(navigator.mediaDevices?.getUserMedia),
  hasTouchScreen: "ontouchstart" in window || navigator.maxTouchPoints > 0,
};
```

### Security Measures
- Implement file type validation
- Add CSRF protection
- Set up rate limiting
- Secure file upload handling

### Animation Keyframes
```css
@keyframes progress {
  0% { width: 0% }
  100% { width: 100% }
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(20px) }
  to { opacity: 1; transform: translateY(0) }
}
```
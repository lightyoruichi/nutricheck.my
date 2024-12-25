# 🎯 Nutricheck.my

A modern, gamified food analysis web application that makes nutrition tracking fun and engaging.

## 🚀 Features

- 📸 Instant food image analysis
- 🎮 Gamified nutrition tracking
- ⚡ Real-time results in under 10 seconds
- 🎯 High accuracy analysis
- 🟢 Halal certified
- 📱 Mobile-first responsive design
- 📤 Easy sharing of results
- 🌐 Progressive Web App support

## 🛠️ Tech Stack

- PHP 8.2
- HTML5, CSS3, JavaScript
- Service Workers for offline support
- Image compression and optimization
- Browser caching and Gzip compression

## 🏃‍♂️ Getting Started

1. Clone the repository:
```bash
git clone https://github.com/yourusername/nutricheck.git
cd nutricheck
```

2. Set up environment variables:
```bash
cp .env.example .env
```

3. Configure your web server:
- Enable mod_rewrite and mod_headers
- Point document root to the project directory
- Ensure write permissions for uploads directory

4. Update configuration:
- Set APP_URL in .env
- Configure upload limits in .htaccess
- Set API credentials if using external services

## 📱 Mobile Support

The application is fully optimized for mobile devices with:
- Native camera integration
- Touch-optimized interface
- Responsive design
- Share API integration
- Offline support

## 🔒 Security Features

- CSRF protection
- Secure file handling
- Input validation
- Rate limiting (10 requests per hour)
- XSS prevention
- SQL injection protection
- Content Security Policy
- Strict Transport Security
- File type validation
- Size limits (10MB max)

## 🎨 UI/UX Features

- Image preview before upload
- Loading animations
- Progress indicators
- Error handling
- Tooltips for features
- High contrast support
- Screen reader compatibility

## 🚀 Performance

- Image compression
- Lazy loading
- Browser caching
- Gzip compression
- Service worker caching
- Optimized API calls
- Efficient DOM updates

## 📦 File Structure

```
nutricheck/
├── .env.example       # Environment variables template
├── .gitignore        # Git ignore rules
├── .htaccess         # Apache configuration
├── index.php         # Main application file
├── analyze.php       # Analysis endpoint
├── sw.js            # Service worker
└── uploads/         # Image upload directory
```

## 🌟 Contributing

Contributions are welcome! Please read our contributing guidelines before submitting pull requests.

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Support

For support, email support@nutricheck.my or join our Discord community.

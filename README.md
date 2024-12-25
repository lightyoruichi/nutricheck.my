# 🍽️ NutriCheck

A modern, single-file PHP application for food analysis through image processing. Built with simplicity and user experience in mind.

## ✨ Features

### 🎯 Core Functionality
- 📸 Drag & drop image upload
- 🖼️ Real-time image preview
- 🔍 Instant food analysis
- 📊 Detailed nutritional information
- 📱 Mobile-first responsive design

### 🛠️ Technical Features
- 🔒 Secure file handling
- 🚀 Real-time validation
- 💾 Automatic file type detection
- 📏 Size limit enforcement (10MB)
- 🎨 Supported formats: JPG, PNG, GIF, WebP

### 🎨 User Interface
- 🎯 Modern, clean design
- 💫 Smooth animations
- 📱 Responsive layout
- 🎨 Intuitive drag & drop
- ❌ One-click image removal
- 📊 Progress indicators

### 🔄 Data Flow
1. 📤 User uploads/drops image
2. ✅ Automatic validation
3. 🖼️ Preview generation
4. 📊 Progress tracking
5. 🔍 Analysis processing
6. 📋 Results display

## 🚀 Installation

```bash
# Clone the repository
git clone https://github.com/lightyoruichi/nutricheck.my.git

# Navigate to project directory
cd nutricheck.my

# Install dependencies
composer install

# Set up environment file
cp .env.example .env

# Configure permissions
chmod -R 755 uploads/
chmod 644 .env
```

## ⚙️ Configuration

1. Server Requirements:
   - PHP 8.3+
   - Apache/Nginx
   - GD/Imagick extension
   - FileInfo extension

2. Directory Permissions:
   ```bash
   sudo chown -R www-data:www-data uploads/
   sudo chmod -R 775 uploads/
   ```

3. Web Server Configuration (Nginx):
   ```nginx
   location ~ \.php$ {
       include snippets/fastcgi-php.conf;
       fastcgi_pass unix:/run/php/php8.3-fpm.sock;
   }
   ```

## 🧪 Testing

Run the test suite:
```bash
composer test
```

Tests cover:
- 📤 File upload validation
- 🔍 Image processing
- ✅ Error handling
- 📊 Analysis results
- 🔒 Security checks

## 🚀 Deployment

Use the deployment script:
```bash
./deploy.sh
```

The script:
- 📦 Creates backups
- ✅ Runs tests
- 🔒 Fixes permissions
- 🔄 Clears cache
- 📊 Verifies deployment

## 🔒 Security

- 🛡️ File type validation
- 🔐 Secure file handling
- 🔒 Environment protection
- 🛡️ XSS prevention
- 🔐 CSRF protection

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch
3. Run tests
4. Create a pull request

## 📄 License

MIT License - see [LICENSE](LICENSE)

## 👥 Contact

- Author: [@lightyoruichi](https://github.com/lightyoruichi)
- Project: [nutricheck.my](https://github.com/lightyoruichi/nutricheck.my)

## 🙏 Acknowledgments

- 🎨 Bootstrap for styling
- 📱 Mobile-first approach
- ✨ Modern web practices
- 🚀 PHP 8.3 features 
# 🍽️ NutriCheck

A lightweight, single-file PHP application for instant food analysis through image processing. Built with simplicity and ease of use in mind.

## ✨ Features

### 📸 Image Upload
- Drag & drop support
- Camera capture on mobile
- Gallery selection
- Real-time preview
- Progress indication
- Supports JPG, PNG, GIF, WebP
- Up to 10MB file size

### 🔍 Analysis Features
- Instant food recognition
- Nutritional information:
  - 🔥 Calories
  - 🥩 Protein
  - 🍚 Carbs
  - 🥑 Fat
- Confidence scoring
- Real-time feedback

### 💫 User Experience
- Mobile-first design
- Adaptive interface
- Smart device detection
- Intuitive controls
- Visual feedback
- Error handling

## 🚀 Quick Start

1. Clone the repository:
```bash
git clone https://github.com/lightyoruichi/nutricheck.my.git
```

2. Set up permissions:
```bash
mkdir uploads
chmod 775 uploads
```

3. Configure your web server (Apache/Nginx) to point to the directory.

## ⚙️ Requirements

- PHP 8.3+
- Web server (Apache/Nginx)
- FileInfo extension
- GD/Imagick extension
- Write permissions for uploads directory

## 🔧 Configuration

The application uses these default settings (adjustable in `index.php`):

```php
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
```

## 🌐 Web Server Configuration

### Nginx
```nginx
location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/run/php/php8.3-fpm.sock;
}

client_max_body_size 10M;
```

### Apache
```apache
<Directory /path/to/nutricheck>
    AllowOverride All
    Require all granted
</Directory>

php_value upload_max_filesize 10M
php_value post_max_size 10M
```

## 🔒 Security

- File type validation
- Size restrictions
- Error handling
- XSS prevention
- Upload directory protection

## 📱 Mobile Support

The application automatically detects mobile devices and provides:
- Camera access for food photos
- Gallery selection option
- Touch-friendly interface
- Responsive design
- Safe area handling

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch
3. Make your changes
4. Create a pull request

## 📄 License

MIT License - see [LICENSE](LICENSE)

## 👥 Contact

- Author: [@lightyoruichi](https://github.com/lightyoruichi)
- Project: [nutricheck.my](https://github.com/lightyoruichi/nutricheck.my) 
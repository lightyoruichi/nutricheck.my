# 🍽️ NutriCheck

A streamlined, single-page PHP application for quick and easy meal nutrition analysis.

## Features

- 📸 Instant image upload/capture with mobile camera support
- 🔄 Real-time image preview and analysis
- 📊 Detailed nutritional breakdown
- 🧩 Ingredient identification
- 📱 Responsive, mobile-first design
- 🎨 Modern UI with gradient accents and glass morphism
- 🔒 Secure file handling and CSRF protection

## Technical Stack

- PHP 8.3
- Apache 2.4
- Bootstrap 5.3
- Vanilla JavaScript
- CSS3 with Custom Properties
- Let's Encrypt SSL

## Recent Improvements

- Optimized layout with reduced whitespace and better proportions
- Enhanced visual hierarchy with gradient header and glass morphism effects
- Improved mobile experience with context-aware upload prompts
- Streamlined single-file architecture
- Added CSRF protection and secure file handling
- Implemented real-time analysis feedback
- Enhanced error handling and user feedback

## UI/UX Features

- Gradient accents for visual hierarchy
- Glass morphism effects for modern aesthetics
- Optimized spacing and proportions
- Mobile-first responsive design
- Context-aware interface elements
- Smooth animations and transitions
- Clear visual feedback

## Installation

1. Clone the repository:
```bash
git clone git@github.com:lightyoruichi/nutricheck.my.git
```

2. Set up Apache virtual host:
```apache
<VirtualHost *:80>
    ServerName nutricheck.my
    DocumentRoot /var/www/html
    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Configure SSL with Let's Encrypt:
```bash
sudo certbot --apache -d nutricheck.my
```

4. Set proper permissions:
```bash
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html
sudo chmod -R 775 /var/www/html/uploads
```

## Development

1. Install PHP dependencies:
```bash
composer install
```

2. Run tests:
```bash
./vendor/bin/phpunit --testdox
```

3. Deploy changes:
```bash
sudo ./deploy.sh
```

## Security

- CSRF protection on all forms
- Secure file upload handling
- SSL/TLS encryption
- Input sanitization
- XSS prevention
- Rate limiting
- File type validation

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

MIT License - see LICENSE file for details.

## Contact

For questions or feedback, please open an issue on GitHub. 
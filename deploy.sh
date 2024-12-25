#!/bin/bash

# Exit on error
set -e

echo "�� Starting deployment..."

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "❌ Please run as root"
    exit 1
fi

# Install required packages if not present
echo "📦 Checking required packages..."
apt-get update
apt-get install -y php-gd fonts-noto-color-emoji apache2 libapache2-mod-php php-fpm

# Enable required Apache modules
echo "🔧 Enabling Apache modules..."
a2enmod rewrite
a2enmod ssl
a2enmod headers

# Generate favicon
echo "🎨 Generating favicon..."
php favicon.php > /var/www/html/favicon.png

# Set correct permissions
echo "📝 Setting permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 775 /var/www/html/uploads

# Run tests
echo "🧪 Running tests..."
cd /var/www/html
./vendor/bin/phpunit --testdox
if [ $? -ne 0 ]; then
    echo "❌ Tests failed"
    exit 1
fi

# Restart Apache
echo "🔄 Restarting Apache..."
systemctl restart apache2

# Verify Apache is running
if ! systemctl is-active --quiet apache2; then
    echo "❌ Apache failed to start"
    exit 1
fi

echo "✅ Deployment completed successfully!" 

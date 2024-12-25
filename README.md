# NutriCheck 🥗

A modern food analysis web application that helps users understand their food through image analysis.

## Features 🌟

### Core Functionality
- 📸 Food image upload/capture
- 🔍 Intelligent food analysis processing
- 🔔 Real-time feedback with toast notifications
- 📱 Responsive UI with mobile-first design

### User Flow
```
User → Upload/Capture Image → Analysis → Display Results
```

### Key Components

#### Image Handling
- 📱 Camera capture (mobile-friendly)
- 📤 File upload support
- 🖼️ Image preview functionality
- 📊 Upload progress visualization
- ❌ Remove image option

#### File Validation
- 📏 Size limit: 10MB
- 🎨 Supported formats:
  - JPG/JPEG
  - PNG
  - GIF
  - WebP

### Data Flow Process
1. User captures/uploads image
2. Automatic image validation
3. Preview display generation
4. Progress bar animation
5. API call to '/api/analyze'
6. Results/error display

### UI Components
- ⏳ Progress bar for upload status
- 🔄 Loading spinner for analysis
- 🖼️ Responsive image preview
- ⚠️ Clean error display

### User Experience Features
- �� Real-time feedback
- 📈 Visual progress indicators
- 🛡️ Graceful error handling
- 📱 Mobile-optimized interface
- ♿ Accessible design elements

## Installation 🚀

```bash
# Clone the repository
git clone https://github.com/lightyoruichi/nutricheck.my.git

# Navigate to project directory
cd nutricheck.my

# Install dependencies
composer install

# Set up environment file
cp .env.example .env

# Configure your environment variables
nano .env

# Set up file permissions
chmod -R 755 uploads/
chmod 644 .env
```

## Configuration ⚙️

1. Configure your web server (Apache/Nginx) to point to the project directory
2. Ensure PHP 8.3+ is installed with required extensions
3. Set up proper file permissions for the uploads directory
4. Configure your environment variables in `.env`

## Usage 📝

1. Access the application through your web browser
2. Upload or capture a food image
3. Wait for the analysis to complete
4. View detailed nutritional information

## Security 🔒

- Input validation for all file uploads
- Secure file handling
- Environment variable protection
- XSS protection
- CSRF protection

## Contributing 🤝

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License 📄

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments 👏

- Built with PHP 8.3
- Uses Bootstrap for styling
- Implements modern web practices
- Mobile-first approach

## Contact 📧

Your Name - [@lightyoruichi](https://github.com/lightyoruichi)

Project Link: [https://github.com/lightyoruichi/nutricheck.my](https://github.com/lightyoruichi/nutricheck.my) 
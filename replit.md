# Golden ICT Solutions Website

## Overview
This is the corporate website for Golden ICT Solutions, a Zambian ICT consulting and services company. The website showcases their services including IT consulting, networking, cybersecurity, and cloud services.

**Status:** Fully functional and running on Replit
**Last Updated:** October 20, 2025

## Project Architecture

### Technology Stack
- **Frontend:** Static HTML5, CSS3, JavaScript
- **Backend:** PHP 8.2 (for contact and newsletter forms)
- **Frameworks/Libraries:**
  - Bootstrap 5 (UI framework)
  - AOS (Animate On Scroll)
  - GLightbox (Lightbox gallery)
  - Swiper (Carousels)
  - PHPMailer (Email sending via SMTP)

### Project Structure
```
.
├── index.html              # Homepage
├── about.html              # About page
├── contact.html            # Contact page
├── services.html           # Services page
├── features.html           # Features page
├── forms/
│   ├── contact.php         # Contact form handler
│   └── newsletter.php      # Newsletter subscription handler
├── assets/
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   ├── img/                # Images and logos
│   └── vendor/             # Third-party libraries
│       └── php-email-form/ # Email form library with PHPMailer
└── CNAME                   # Custom domain configuration
```

## Development Setup

### Running Locally
The website runs on PHP's built-in development server:
```bash
php -S 0.0.0.0:5000
```

The server is configured to run automatically via the workflow on port 5000.

### Email Configuration
The contact and newsletter forms use PHPMailer for SMTP email delivery:
- Contact form: Sends to `info@goldenictsolutions.com`
- Newsletter: Sends to `Mwewap68@outlook.com`
- SMTP configured for Zoho Mail (smtp.zoho.com:465 with SSL)

**Note:** The Zoho SMTP password needs to be configured in `forms/contact.php` (currently placeholder).

## Key Features
1. **Responsive Design:** Mobile-friendly layout using Bootstrap
2. **Animated Sections:** Smooth scroll animations with AOS
3. **Contact Forms:** PHP-powered contact and newsletter forms
4. **Service Showcase:** Detailed information about IT services
5. **Social Media Integration:** Links to Facebook and LinkedIn

## Custom Domain
The website is configured for the custom domain: `www.goldenictsolutions.com` (via CNAME file)

## Recent Changes
- **Oct 20, 2025:** Imported from GitHub and set up on Replit
  - Installed PHP 8.3 module
  - Created PHP_Email_Form class
  - Installed PHPMailer via Composer
  - Configured workflow to serve on port 5000
  - Added .gitignore for PHP and IDE files

## Notes
- The PHP email forms require SMTP credentials to be functional
- Images and assets are all included in the repository
- The website uses external Google Fonts
- Map integration uses Google Maps embed

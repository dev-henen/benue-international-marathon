# Benue International Marathon

A web application for managing the Benue International Marathon event, built with PHP, FastRoute, Twig, and Tailwind CSS.

## Setup Requirements

- PHP 8.0 or higher
- Node.js and npm
- Composer

## Installation

1. **Install PHP Dependencies**
```bash
composer install
```

2. **Install Frontend Dependencies**
```bash
npm install
```

3. **Environment Setup**
- Copy `.env.example` to `.env`
- Configure the following variables:
  ```env
  # Application
  ENCRYPTION_KEY="EeDk40KKS4#lld,d/dsd|sd/d.#@S3))@"
  APP_ENV=development  # Use 'production' for live environment

  # Database
  DB_DRIVER=mysql
  DB_HOST=127.0.0.1
  DB_DATABASE=bim
  DB_USERNAME=root
  DB_PASSWORD=
  DB_CHARSET=utf8
  DB_COLLATION=utf8_unicode_ci
  DB_PREFIX=

  # Mail
  MAIL_DRIVER=smtp
  MAIL_HOST=sandbox.smtp.mailtrap.io
  MAIL_SMTP_AUTH=true
  MAIL_USERNAME=your-username
  MAIL_PASSWORD=your-password
  MAIL_ENCRYPTION=starttls
  MAIL_PORT=2525
  MAIL_FROM_ADDRESS=your-email
  MAIL_FROM_NAME="Benue International Marathon"

  # Template Engine
  TWIG_DEBUG=true
  TWIG_AUTO_RELOAD=true

  # Payment Gateway
  PAYSTACK_PAYMENT_SECRET_KEY=your-secret-key
  PAYSTACK_PAYMENT_PUBLIC_KEY=your-public-key
  ```

## Running the Application

1. **Compile CSS**
```bash
# One-time build
npm run build:css

# Watch for changes during development
npm run watch:css
```

2. **Start PHP Server**
```bash
php -S localhost:8000 -t public_html
```

3. Access the application at `http://localhost:8000`

## Project Structure

```
├── app/                    # Application source code
├── public_html/           # Web root directory
│   └── assets/
│       └── css/
├── .env                   # Environment configuration
├── composer.json          # PHP dependencies
└── package.json          # Node.js dependencies
```

## Dependencies

### PHP Packages
- FastRoute: URL routing
- Twig: Template engine
- Illuminate/Database: Database ORM
- PHPMailer: Email handling
- PayStack PHP: Payment processing
- FPDF: PDF generation
- PHP QR Code: QR code generation
- PHP dotenv: Environment configuration

### Frontend
- Tailwind CSS: Utility-first CSS framework
- PostCSS: CSS processing
- Autoprefixer: CSS vendor prefixing
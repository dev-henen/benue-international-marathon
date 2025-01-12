# Benue International Marathon

This project is a web application for the **Benue International Marathon** event, utilizing various tools such as **FastRoute** for routing, **Twig** for templating, and **Tailwind CSS** for styling.

## Prerequisites

Before running the project, ensure you have the following installed:

- [PHP](https://www.php.net/) (for FastRoute)
- [Node.js](https://nodejs.org/) (for Tailwind CSS and npm dependencies)
- [Composer](https://getcomposer.org/) (for PHP dependencies)
- [npm](https://www.npmjs.com/) (for JavaScript dependencies)

## Installation

### 1. Install PHP Dependencies

To install the required PHP dependencies, run the following command:

```bash
composer install
```

This will install all the php dependencies.

### 2. Install Node.js Dependencies

Navigate to the project directory and run the following command to install the JavaScript dependencies:

```bash
npm install
```

This will install **Tailwind CSS**, **PostCSS**, and **Autoprefixer**.

## Development Setup

### 1. Start the Tailwind CSS Build

Run the following command to compile your CSS files:

```bash
npm run build:css
```

This will compile the Tailwind CSS into the `public_html/assets/css/style.css` file.

### 2. Watch for CSS Changes

To automatically recompile CSS when changes are made, run:

```bash
npm run watch:css
```

This will watch the `app/styles.css` file for any changes and automatically recompile it.

## Running the Application

1. **Start the PHP server** (if not set up already) to serve your application. You can use PHP’s built-in server or configure a web server (like Apache or Nginx) based on your setup.

2. **Access the application** via the browser at `http://localhost` (or your configured domain).

## Project Structure

- `app/styles.css`: The main Tailwind CSS input file.
- `public_html/assets/css/style.css`: The output CSS file.
- `composer.json`: Contains PHP dependencies like **FastRoute** and **Twig**.
- `package.json`: Contains JavaScript dependencies and scripts for Tailwind CSS.

## Development

To contribute or make changes to the project:

1. Clone the repository or fork it.
2. Create a new branch for your feature or bug fix.
3. Install dependencies using `composer install` (for PHP) and `npm install` (for JS).
4. Make your changes and test locally.
5. Commit and push your changes.
6. Create a pull request with a detailed description of the changes made.

---

## Environment Variables

This project uses environment variables to configure its settings. Below is a breakdown of the key variables used in the `.env` file:

### General Configuration
- **ENCRYPTION_KEY**: A secure key used for encryption within the application.  
  Example: `EeDk40KKS4#lld,d/dsd|sd/d.#@S3))@`

- **APP_ENV**: Defines the application environment.  
  Possible values:  
  - `development` (for local or testing environments)  
  - `production` (for live environments)

### Database Configuration
- **DB_DRIVER**: The database driver (e.g., `mysql`, `pgsql`, etc.).  
- **DB_HOST**: The database host (e.g., `127.0.0.1` or a server address).  
- **DB_DATABASE**: The name of the database.  
- **DB_USERNAME**: The database username.  
- **DB_PASSWORD**: The database password.  
- **DB_CHARSET**: Character set used by the database (default: `utf8`).  
- **DB_COLLATION**: Collation used by the database (default: `utf8_unicode_ci`).  
- **DB_PREFIX**: Prefix for database tables (if any).

### Mail Configuration
- **MAIL_DRIVER**: The mail transport driver (e.g., `smtp`, `sendmail`).  
- **MAIL_HOST**: The SMTP host.  
- **MAIL_SMTP_AUTH**: Whether SMTP authentication is required (`true` or `false`).  
- **MAIL_USERNAME**: The username for SMTP authentication.  
- **MAIL_PASSWORD**: The password for SMTP authentication.  
- **MAIL_ENCRYPTION**: Encryption method for SMTP (e.g., `starttls`, `ssl`).  
- **MAIL_PORT**: Port for SMTP (e.g., `2525`, `587`).  
- **MAIL_FROM_ADDRESS**: The default "from" email address for outgoing emails.  
- **MAIL_FROM_NAME**: The default "from" name for outgoing emails.

### Twig Configuration
- **TWIG_DEBUG**: Enables or disables Twig debugging (`true` or `false`).  
- **TWIG_AUTO_RELOAD**: Automatically reloads Twig templates when they are updated (`true` or `false`).

---

### Notes
1. Ensure sensitive values like `ENCRYPTION_KEY` and `MAIL_PASSWORD` are not exposed publicly.
2. Use strong, unique keys and passwords for production environments.
3. Switch `APP_ENV` to `production` and configure appropriate production settings before deploying live.

### Example `.env` File

```env
# General Configuration
ENCRYPTION_KEY="EeDk40KKS4#lld,d/dsd|sd/d.#@S3))@"
APP_ENV=development  # Change to 'production' for live environments

# Database Configuration
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_DATABASE=bim
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8
DB_COLLATION=utf8_unicode_ci
DB_PREFIX=

# Mail Configuration
MAIL_DRIVER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_SMTP_AUTH=true
MAIL_USERNAME=e950bed8fasd43
MAIL_PASSWORD=643fd8997sdb70e
MAIL_ENCRYPTION=starttls
MAIL_PORT=2525
MAIL_FROM_ADDRESS=0sfdj499s-0cc719+1@inbox.mailtrap.io
MAIL_FROM_NAME="Benue International Marathon"

# Twig Configuration
TWIG_DEBUG=true
TWIG_AUTO_RELOAD=true
```

---

### Usage Instructions
1. Copy the example `.env` content into a new file named `.env` in the project root.
2. Update the values as per the server specific requirements, especially sensitive credentials like `DB_PASSWORD` and `MAIL_PASSWORD`.
3. Ensure the `.env` file is added to `.gitignore` to prevent it from being committed to version control.

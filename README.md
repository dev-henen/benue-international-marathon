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

This will install the `nikic/fast-route` and `twig/twig` libraries as defined in the `composer.json` file.

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

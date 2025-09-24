

PHP Environment Switcher

A lightweight PHP project that simplifies environment management for development and production servers with automatic configuration loading using .env files.




🚀 Features

✅ Automatic Environment Detection via APP_ENV variable

✅ Dynamic .env Loading: Automatically loads .env.development or .env.production

✅ Singleton Database Connection: Secure PDO connection with error handling

✅ Error Logging: Errors are logged into error.log instead of showing sensitive details in production

✅ Environment Detection: Visual indicators for current environment status

✅ Database Integration: Checks database connectivity and verifies table existence

✅ Zero Configuration: Works immediately after setting environment files




📦 Quick Start

Clone the repository

Install dependencies:

composer require vlucas/phpdotenv




Configure your environment files:

    .env.development → Local development settings

    .env.production → Production server settings

Access index.php in your browser to see environment status




📂 File Structure


├── config.php              # Environment auto-loader (detects development/production)
├── Database.php            # Singleton PDO database connection
├── index.php               # Status dashboard
├── .env.development        # Local environment variables
├── .env.production         # Production environment variables
├── create-users-table.php  # Database setup helper
└── error.log               # Error logs (auto-created if missing)




⚙️ Requirements

PHP 7.4+
Composer
MySQL/MariaDB database
vlucas/phpdotenv package






🖥️ Usage
🔹 Local Development
Inside .env.development set:

APP_ENV=development
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=dev_db
DB_USER=root
DB_PASS=yourpassword
DB_CHARSET=utf8mb4


The project will automatically detect and load this file.





🔹 Production Server

Inside .env.production set:

APP_ENV=production
DB_HOST=your-production-host
DB_PORT=3306
DB_NAME=production_db
DB_USER=prod_user
DB_PASS=securepassword
DB_CHARSET=utf8mb4

The project will automatically detect and load this file.






🔹 Status Check (index.php)

Visit index.php to view:

🌍 Current environment (development or production)
🕒 Server time
✅ Database connection status
🗂️ Table existence check
👥 In development → fetch users from DB
🔒 In production → only show database name (no sensitive data)





🔐 Security Notes

❌ Never commit .env files to version control
🔑 Use strong database passwords in production
🛡️ Keep production credentials safe
📝 Errors in production go to error.log, not browser





📜 License

MIT License – free to use in your projects



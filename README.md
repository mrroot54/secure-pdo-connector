# secure-pdo-connector
This project is a secure PHP database helper. It uses PDO for database connection, Dotenv for loading settings from a .env file, and a safe logging system with error handling and log rotation. It makes it easy to connect, query, and manage MySQL in any PHP project with clean and reusable code.



## Features
- ✅ Secure PDO connection (Singleton pattern)  
- ✅ Load configuration from `.env` file (Dotenv)  
- ✅ Centralized error handling (development vs production)  
- ✅ Safe logging system (`env_logs/error.log`) with log rotation  
- ✅ Helper methods for queries (`fetch`, `fetchAll`, `execute`, etc.)  
- ✅ Works in any Core PHP project

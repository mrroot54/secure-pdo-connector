# secure-pdo-connector  

A secure and reusable PHP database helper.  
It is designed to make database work in **Core PHP projects** easier, safer, and cleaner.  
This project uses:  

- **PDO** → to connect with MySQL safely  
- **Dotenv** → to load settings from a `.env` file (no hardcoded credentials)  
- **Custom Logging** → errors are stored in `env_logs/error.log` with log rotation  
- **Centralized Error Handling** → different behavior in development vs production  

The goal is to help developers **quickly integrate a secure database connection** into any PHP project without repeating the same boilerplate code.  

---

## 🚀 Features  

- ✅ Secure PDO connection (Singleton pattern)  
- ✅ Load configuration from `.env` file (Dotenv)  
- ✅ Centralized error handling (development vs production)  
- ✅ Safe logging system (`env_logs/error.log`) with log rotation  
- ✅ Helper methods for queries (`fetch`, `fetchAll`, `fetchColumn`, `execute`)  
- ✅ Supports dynamic `IN()` clauses with placeholder binding  
- ✅ Works in any Core PHP project  

---

## 📋 Requirements  

- PHP **8.0+**  
- MySQL database  
- Composer (for dependencies)  

---

## ⚙️ Installation  

1. Clone the repository:  
   ```bash
   git clone https://github.com/your-username/secure-pdo-connector.git
   cd secure-pdo-connector

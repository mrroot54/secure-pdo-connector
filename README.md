# php-secure-db-helper  

A **secure and reusable database helper for PHP projects**.  
It uses **PDO**, **Dotenv**, and a custom **logging + error handling** system to make database connections safe, simple, and consistent.  

---

## ✨ Features  

- ✅ PDO Singleton connection (no repeated connections)  
- ✅ `.env` configuration support (no hardcoded credentials)  
- ✅ Error handling (different for development and production)  
- ✅ Error logging with rotation (`env_logs/error.log`)  
- ✅ Helper functions for queries:  
  - `DB::fetchAll()` → Get multiple rows  
  - `DB::fetch()` → Get one row  
  - `DB::fetchColumn()` → Get one column  
  - `DB::execute()` → Run insert/update/delete  
- ✅ Safe prepared statements with type binding  
- ✅ Support for dynamic `IN()` placeholders  

---

## 📋 Requirements  

- PHP **8.0+**  
- MySQL database  
- Composer (for dependencies)  

---

## ⚙️ Installation  

1. Clone the repository:  
   ```bash
   git clone https://github.com/your-username/php-secure-db-helper.git
   cd php-secure-db-helper

2. Install dependencies:
 ```bash
  composer install
 ```

3. Create a .env file in the project root:
```bash
APP_ENV=development
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=my_database
DB_USER=root
DB_PASS=secret
DB_CHARSET=utf8mb4
```

4. Create the logs folder (if not exists):
```
mkdir env_logs
touch env_logs/error.log
```


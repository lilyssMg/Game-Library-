# Installation

## Requirements

- PHP 8.0+
- MariaDB 10.5+ (or MySQL 8+)
- Apache 2 with `mod_rewrite` enabled
- PHP extensions: `pdo`, `pdo_mysql`, `fileinfo`

## Local setup

### 1. Clone the repo

```bash
git clone <repo-url>
cd Game-Library-
```

### 2. Create the database and user

```sql
CREATE DATABASE game_library CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'game_user'@'localhost' IDENTIFIED BY 'game_password';
GRANT ALL PRIVILEGES ON game_library.* TO 'game_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Run the schema

```bash
mysql -u game_user -p game_library < database/schema.sql
```

### 4. Configure credentials

Edit `database/config.php` if your credentials differ:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'game_library');
define('DB_USER', 'game_user');
define('DB_PASS', 'game_password');
```

### 5. Set upload directory permissions

```bash
mkdir -p uploads
chmod 755 uploads
```

### 6. Serve

Point Apache (or `php -S localhost:8000`) at the project root.

## Raspberry Pi / DietPi deployment

See `deploy/deploy.sh` for an automated setup script targeting DietPi on Pi Zero W 2.

Run as root:

```bash
bash deploy/deploy.sh
```

The script installs Apache, PHP, MariaDB, runs the schema, and configures the virtual host.

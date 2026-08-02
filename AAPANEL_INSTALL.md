# Complete aaPanel Installation & Deployment Guide

Follow these step-by-step instructions to deploy the **Dynamic Personal Biography Website** on **aaPanel** (Nginx or Apache server).

---

## Prerequisites on aaPanel

Before starting, ensure you have the following installed via aaPanel App Store:
- **Web Server**: Nginx or Apache 2.4+
- **PHP**: PHP 8.0, 8.1, 8.2, or 8.3 (with extensions `pdo_mysql`, `gd`, `mbstring`, `fileinfo`, `json`, `iconv`)
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **phpMyAdmin**: For database management

---

## Step 1: Create Database in aaPanel

1. Log in to your **aaPanel Dashboard**.
2. Click **Database** on the left menu.
3. Click **Add Database**:
   - **DB Name**: `biography_db` (or custom name)
   - **Access Rights**: Local server / Everyone
   - **Username**: `biography_user` (or default `root`)
   - **Password**: Generate a strong password.
4. Click **Submit**.
5. Click **Import** next to the newly created database, or open **phpMyAdmin**:
   - Select `biography_db`.
   - Click **Import** tab.
   - Choose the file `database/schema.sql` from your local machine / server.
   - Click **Go** / **Import**.

---

## Step 2: Add Website in aaPanel

1. Click **Website** on the left menu in aaPanel.
2. Click **Add site**:
   - **Domain**: Enter your domain (e.g., `yourdomain.com` or `bio.yourdomain.com`).
   - **Root directory**: `/www/wwwroot/yourdomain.com`
   - **FTP**: Optional
   - **Database**: Select the database created in Step 1 (or create it here).
   - **PHP Version**: Select **PHP-8.0** (or higher).
3. Click **Submit**.

---

## Step 3: Deploy Codebase (via Git or aaPanel File Manager)

### Option A: Via aaPanel Terminal / SSH (Recommended)
Run the following commands in SSH terminal:
```bash
cd /www/wwwroot/yourdomain.com

# Clear default aaPanel index files
rm -rf index.html 404.html .htaccess

# Clone repository
git clone https://github.com/edge-tec/bio_details.git .

# Set proper permissions
chown -R www:www /www/wwwroot/yourdomain.com
chmod -R 755 /www/wwwroot/yourdomain.com
chmod -R 777 /www/wwwroot/yourdomain.com/assets/uploads
chmod -R 777 /www/wwwroot/yourdomain.com/logs
```

---

## Step 4: Configure `config/config.php`

Open `/www/wwwroot/yourdomain.com/config/config.php` in aaPanel File Editor and update:

```php
/** Database Credentials */
define('DB_HOST', 'localhost');
define('DB_NAME', 'biography_db');     // Your aaPanel DB Name
define('DB_USER', 'biography_user');   // Your aaPanel DB User
define('DB_PASS', 'YOUR_DB_PASSWORD'); // Your aaPanel DB Password
```

---

## Step 5: Configure URL Rewrite Rules (Nginx or Apache)

### If using Nginx on aaPanel:
1. In aaPanel, go to **Website** > Click your domain > **URL rewrite**.
2. Paste the following Nginx rewrite rule:

```nginx
if (!-e $request_filename) {
    rewrite ^/admin/?$ /admin/index.php last;
    rewrite ^/admin/([a-zA-Z0-9_-]+)/?$ /admin/index.php?page=$1 last;
    rewrite ^/blog/category/([a-zA-Z0-9_-]+)/?$ /index.php?page=blog&category=$1 last;
    rewrite ^/blog/tag/([a-zA-Z0-9_-]+)/?$ /index.php?page=blog&tag=$1 last;
    rewrite ^/blog/([a-zA-Z0-9_-]+)/?$ /index.php?page=blog-single&slug=$1 last;
    rewrite ^/portfolio/([a-zA-Z0-9_-]+)/?$ /index.php?page=portfolio&slug=$1 last;
    rewrite ^/sitemap\.xml$ /sitemap.php last;
    rewrite ^/([a-zA-Z0-9_-]+)/?$ /index.php?page=$1 last;
}
```
3. Click **Save**.

### If using Apache on aaPanel:
The included `.htaccess` file will handle URL rewrites automatically. Make sure `mod_rewrite` is enabled in Apache settings.

---

## Step 6: Enable Free SSL Certificate (HTTPS)

1. In aaPanel, go to **Website** > Click your domain > **SSL**.
2. Click **Let's Encrypt** tab.
3. Select your domain name.
4. Click **Apply**.
5. Check **Force HTTPS** toggle switch.

---

## Step 7: Verify Installation & Login to Admin

1. Open your browser and visit `https://yourdomain.com`.
2. Access the Admin Panel at `https://yourdomain.com/admin/login`.
3. Use default credentials:
   - **Email**: `admin@example.com`
   - **Password**: `Admin@123`
4. **IMPORTANT**: Immediately go to **Profile** & change password to secure your admin panel!

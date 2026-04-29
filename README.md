# Laravel REST API

A production-ready Laravel 11 REST API with Sanctum token authentication, role-based access control, full CRUD, and deployment configs for Docker, VPS, and shared hosting.

---

## ✅ Features

- **Laravel 11** + **PHP 8.2**
- **Laravel Sanctum** – token-based API authentication
- **Role-based access** – `user` and `admin` roles with Gates & Policies
- **Versioned routes** – all endpoints under `/api/v1`
- **Full CRUD** – Posts resource with ownership checks
- **Form Requests** – validated inputs with clean error responses
- **JSON error handler** – all errors return consistent JSON
- **Feature tests** – PHPUnit tests for Auth and Posts
- **Docker** – single-image setup with Nginx + PHP-FPM + Supervisor
- **CI/CD** – GitHub Actions pipeline (test → build → deploy)

---

## 📁 Project Structure

```
laravel-api/
├── app/
│   ├── Exceptions/Handler.php          # Global JSON error handler
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── AuthController.php      # register / login / logout / me
│   │   │   ├── PostController.php      # CRUD for posts
│   │   │   └── UserController.php      # Admin user management
│   │   └── Requests/                   # Validated form requests
│   ├── Models/
│   │   ├── User.php                    # With role + HasApiTokens
│   │   └── Post.php
│   ├── Policies/PostPolicy.php         # Owner / admin authorization
│   └── Providers/AppServiceProvider.php
├── config/                             # app, auth, database, sanctum, cache
├── database/
│   ├── factories/                      # UserFactory, PostFactory
│   ├── migrations/                     # users, personal_access_tokens, posts
│   └── seeders/DatabaseSeeder.php      # Admin + demo user
├── docker/                             # nginx.conf, supervisord.conf, opcache.ini
├── routes/api.php                      # All versioned API routes
├── tests/Feature/                      # AuthTest, PostTest
├── .env.example
├── docker-compose.yml
├── Dockerfile
└── .github/workflows/ci.yml
```

---

## 🚀 Quick Start (Local)

### Option A — Docker (recommended)

```bash
git clone <your-repo> laravel-api && cd laravel-api

# 1. Copy env
cp .env.example .env

# 2. Start containers
docker-compose up -d

# 3. Shell into app container
docker-compose exec app sh

# 4. Inside container:
composer install
php artisan key:generate
php artisan migrate --seed
```

API available at: **http://localhost:8000/api/v1**
phpMyAdmin at: **http://localhost:8080**

---

### Option B — Local PHP (without Docker)

```bash
# Requirements: PHP 8.2+, Composer, MySQL

composer install
cp .env.example .env
php artisan key:generate

# Edit .env with your DB credentials, then:
php artisan migrate --seed
php artisan serve
```

API available at: **http://localhost:8000/api/v1**

---

## 📡 API Endpoints

### Public
| Method | Endpoint            | Description         |
|--------|---------------------|---------------------|
| GET    | `/api/v1/health`    | Health check        |
| POST   | `/api/v1/register`  | Register new user   |
| POST   | `/api/v1/login`     | Login, get token    |

### Authenticated (Bearer token required)
| Method | Endpoint             | Description               |
|--------|----------------------|---------------------------|
| GET    | `/api/v1/me`         | Current user profile      |
| POST   | `/api/v1/logout`     | Revoke token              |
| GET    | `/api/v1/posts`      | List posts (own / all)    |
| POST   | `/api/v1/posts`      | Create post               |
| GET    | `/api/v1/posts/{id}` | Get post                  |
| PUT    | `/api/v1/posts/{id}` | Update post               |
| DELETE | `/api/v1/posts/{id}` | Delete post               |

### Admin only
| Method | Endpoint              | Description    |
|--------|-----------------------|----------------|
| GET    | `/api/v1/users`       | List all users |
| GET    | `/api/v1/users/{id}`  | Get user       |
| PUT    | `/api/v1/users/{id}`  | Update user    |
| DELETE | `/api/v1/users/{id}`  | Delete user    |

### Authentication
All protected endpoints require:
```
Authorization: Bearer <token>
```

---

## 🌍 Deployment

### 1. VPS / Linux Server

```bash
# On your server (Ubuntu 22.04+)
sudo apt install php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml nginx mysql-server

# Clone project
git clone <repo> /var/www/laravel-api
cd /var/www/laravel-api

# Install deps
composer install --no-dev --optimize-autoloader

# Configure env
cp .env.example .env
nano .env            # set APP_KEY, DB_*, APP_URL, APP_ENV=production, APP_DEBUG=false

php artisan key:generate
php artisan migrate --force --seed

# Permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Nginx config** (`/etc/nginx/sites-available/laravel-api`):
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/laravel-api/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/laravel-api /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

---

### 2. Docker (production)

```bash
# Build image
docker build -t laravel-api .

# Run
docker run -d \
  -p 80:80 \
  -e APP_KEY=base64:... \
  -e DB_HOST=your-db-host \
  -e DB_DATABASE=laravel_api \
  -e DB_USERNAME=laravel \
  -e DB_PASSWORD=secret \
  laravel-api
```

---

### 3. Shared Hosting (cPanel)

1. Upload all files to `public_html/laravel-api/`
2. Point your domain's document root to `public_html/laravel-api/public`
3. Create a MySQL database in cPanel and update `.env`
4. Run via SSH: `php artisan migrate --seed`
5. Set `APP_ENV=production`, `APP_DEBUG=false` in `.env`

---

## 🧪 Tests

```bash
php artisan test
# or
./vendor/bin/phpunit
```

---

## 🔐 Default Seed Users

| Email              | Password   | Role  |
|--------------------|------------|-------|
| admin@example.com  | `password` | admin |
| user@example.com   | `password` | user  |

> **Change these immediately in production.**

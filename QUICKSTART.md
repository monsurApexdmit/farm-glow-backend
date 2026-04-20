# Farm Glow Backend - Quick Start

## 🚀 Running Right Now

Your application is already running!

```
🌐 Open: http://localhost:8007
```

## ⚡ Quick Commands

### Check Status
```bash
docker compose ps
```

### View Logs
```bash
docker compose logs -f app
```

### Stop/Start
```bash
docker compose down   # Stop
docker compose up -d  # Start
```

## 🎮 Laravel Commands

```bash
# Database migrations
docker compose exec app php artisan migrate

# Create model with migration
docker compose exec app php artisan make:model Post -m

# Create controller
docker compose exec app php artisan make:controller PostController

# Run tests
docker compose exec app php artisan test

# Interactive shell
docker compose exec app php artisan tinker
```

## 📦 NPM Commands

```bash
# Install packages
docker compose exec app npm install

# Build assets
docker compose exec app npm run build

# Watch for changes
docker compose exec app npm run dev
```

## 🗄️ Database

```bash
# Connect from host
mysql -h 127.0.0.1 -u admin -p farm_glow

# From Docker
docker compose exec app mysql -h central_mysql -u admin -p farm_glow
```

## 📂 Key Files

| File | Purpose |
|------|---------|
| `.env` | Configuration (already configured) |
| `app/` | Your application code |
| `routes/` | API & web routes |
| `database/migrations/` | Database migrations |
| `resources/views/` | Blade templates |
| `docker-compose.yml` | Docker services |

## 🔗 Services

- **App**: http://localhost:8007 ✅
- **Database**: central_mysql:3306
- **Cache**: central_redis:6379
- **API**: http://localhost:8007/api

## 📋 Environment

```env
APP_NAME=Farm Glow
DB_CONNECTION=mysql
DB_HOST=central_mysql
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

## ❓ Troubleshooting

**App won't start?**
```bash
docker compose logs app
```

**Database error?**
```bash
docker compose exec app php artisan tinker
> DB::connection()->getPdo()
```

**Need to rebuild?**
```bash
docker compose build --no-cache
docker compose up -d
```

## 📚 Documentation

- [Full Setup Guide](SETUP.md)
- [Deployment Guide](DEPLOYMENT.md)
- [Laravel Docs](https://laravel.com/docs/13)

---

**Status**: ✅ Ready to go!  
**Port**: 8007  
**Database**: MySQL  
**Cache**: Redis  

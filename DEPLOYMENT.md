# Farm Glow Backend - Deployment & Running

## ✅ Project Status

Your Laravel 13 project is fully configured and running!

### Current Setup
- **Laravel Version**: 13.5.0
- **PHP Version**: 8.3-FPM
- **Database**: MySQL (central_mysql)
- **Cache/Queue**: Redis (central_redis)
- **Web Server**: Nginx
- **App Port**: 8007
- **Status**: Running ✅

## 🌐 Access Your Application

```bash
# Open in browser
http://localhost:8007
```

## 📁 Project Structure

```
farm-glow-backend/
├── app/                    # Application logic
├── bootstrap/              # Framework initialization
├── config/                 # Configuration files
├── database/               # Migrations & seeders
├── public/                 # Public assets
├── resources/              # Views, CSS, JS
├── routes/                 # API & Web routes
├── storage/                # Logs, cache, uploads
├── tests/                  # Test files
├── .env                    # Environment variables (configured)
├── docker-compose.yml      # Docker services
├── Dockerfile              # PHP-FPM image
├── nginx.conf              # Nginx config
└── setup.sh               # Setup script
```

## 🐳 Docker Services

### Running Containers
```bash
docker compose ps
```

### Service Details

| Service | Image | Status | Port |
|---------|-------|--------|------|
| PHP-FPM | php:8.3-fpm | ✅ | 9000 (internal) |
| Nginx | nginx:alpine | ✅ | 8007 |
| MySQL | central_mysql | ✅ | 3306 |
| Redis | central_redis | ✅ | 6379 |

## 🔧 Common Commands

### Start/Stop Services
```bash
# Start
docker compose up -d

# Stop
docker compose down

# Restart
docker compose restart
```

### Laravel Artisan Commands
```bash
# In container
docker compose exec app php artisan <command>

# Examples
docker compose exec app php artisan migrate
docker compose exec app php artisan make:model Post -m
docker compose exec app php artisan make:controller PostController -r
docker compose exec app php artisan tinker
docker compose exec app php artisan test
```

### Database Operations
```bash
# Fresh migration (WARNING: deletes all data)
docker compose exec app php artisan migrate:fresh

# Rollback last migration
docker compose exec app php artisan migrate:rollback

# View all migrations
docker compose exec app php artisan migrate:status

# Seed database
docker compose exec app php artisan db:seed
```

### Assets & NPM
```bash
# Install dependencies
docker compose exec app npm install

# Build for development
docker compose exec app npm run dev

# Build for production
docker compose exec app npm run build

# Watch for changes
docker compose exec app npm run watch
```

### Logs
```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f app
docker compose logs -f nginx

# Tail last 50 lines
docker compose logs --tail=50 app
```

## 📊 Database Configuration

### Connection Details
```env
DB_CONNECTION=mysql
DB_HOST=central_mysql
DB_PORT=3306
DB_DATABASE=farm_glow
DB_USERNAME=admin
DB_PASSWORD=admin123
```

### Connect from Host
```bash
mysql -h 127.0.0.1 -u admin -p farm_glow
# Password: admin123
```

### Connect from Container
```bash
docker compose exec app mysql -h central_mysql -u admin -p farm_glow
```

## 💾 Redis Configuration

### Connection Details
```env
REDIS_HOST=central_redis
REDIS_PORT=6379
```

### Connect from Host
```bash
redis-cli -h localhost
```

### Check Redis Connection
```bash
docker compose exec app php artisan tinker
> redis()
> redis()->ping()
```

## 📝 Environment Variables (.env)

Key settings already configured:
- `APP_NAME=Farm Glow`
- `APP_DEBUG=true` (for development)
- `APP_URL=http://localhost:8007`
- `DB_CONNECTION=mysql` (using central_mysql)
- `CACHE_STORE=redis` (using central_redis)
- `QUEUE_CONNECTION=redis`
- `SESSION_DRIVER=cookie`

## 🔐 Security Notes

For production:
1. Change `APP_DEBUG=false`
2. Change `APP_ENV=production`
3. Generate new `APP_KEY` (already done)
4. Use strong database passwords
5. Configure SSL/HTTPS properly
6. Keep dependencies updated

## 📦 Installed Packages

Laravel 13 includes:
- Laravel Framework
- Laravel Tinker (REPL)
- PHPUnit (testing)
- Laravel Pint (code style)
- Vite (asset bundling)

## 🐛 Troubleshooting

### Containers Won't Start
```bash
# Check docker status
docker ps

# View detailed logs
docker compose logs app
docker compose logs nginx

# Rebuild images
docker compose build --no-cache
```

### Database Connection Error
```bash
# Check if central_mysql is running
docker ps | grep mysql

# Test connection
docker compose exec app php artisan tinker
> DB::connection()->getPdo()
```

### Port Already in Use
Edit `docker-compose.yml`:
```yaml
ports:
  - "8007:80"  # Change from 8006 to any available port
```

### Permission Denied
```bash
# Fix file permissions
docker compose exec app chown -R www-data:www-data /var/www
docker compose exec app chmod -R 755 /var/www/storage
```

## 📚 Useful Resources

- [Laravel 13 Documentation](https://laravel.com/docs/13)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Nginx Configuration](https://nginx.org/en/docs/)
- [Redis Documentation](https://redis.io/documentation)

## 🎯 Next Steps

1. **Verify Application**
   ```bash
   curl http://localhost:8006
   ```

2. **Run Migrations**
   ```bash
   docker compose exec app php artisan migrate
   ```

3. **Create a Model**
   ```bash
   docker compose exec app php artisan make:model Post -m
   ```

4. **Build Assets**
   ```bash
   docker compose exec app npm run dev
   ```

5. **Access Tinker (REPL)**
   ```bash
   docker compose exec app php artisan tinker
   ```

## 📞 Support

For issues:
1. Check logs: `docker compose logs -f`
2. Review `.env` configuration
3. Verify Docker containers running: `docker compose ps`
4. Check disk space: `df -h`
5. Review Laravel documentation

---

**Created**: April 19, 2026
**Framework**: Laravel 13.5.0
**Status**: Ready for Development ✅

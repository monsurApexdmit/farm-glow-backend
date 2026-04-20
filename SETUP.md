# Farm Glow Backend - Laravel 13 Setup Guide

This is a Laravel 13 project with Docker and Docker Compose setup for easy development.



## Quick Start

### 1. Initial Setup (First Time Only)

Run the automated setup script:

```bash
cd /home/monsur/Documents/farm-glow-backend
./setup.sh
```

This script will:
- Build Docker images
- Start all services (PHP, Nginx)
- Create a new Laravel 13 project
- Generate the app key
- Run database migrations
- Install npm dependencies

**Note**: Uses shared `central_mysql` and `central_redis` containers

**Access the application at http://localhost:8007**

### 2. Manual Setup (Alternative)

If the script doesn't work, follow these steps:

```bash
# Build images
docker-compose build

# Start services
docker-compose up -d

# Create Laravel project
docker-compose exec app composer create-project laravel/laravel . --no-interaction

# Copy environment file
docker-compose exec app cp .env.example .env

# Generate app key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate

# Install npm dependencies
docker-compose exec app npm install

# Access at http://localhost:8007
```

## Services

### PHP-FPM (app)
- **Port**: 9000 (internal)
- **Container**: farm-glow-app
- **User**: www-data
- **PHP Version**: 8.3

### Nginx
- **Port**: 8007 (http)
- **Container**: farm-glow-nginx
- **URL**: http://localhost:8007

### MySQL (Central)
- **Port**: 3306
- **Container**: central_mysql (external)
- **Database**: farm_glow
- **Root Password**: root
- **Connection**: Uses shared `database_db_network`

### Redis (Central)
- **Port**: 6379
- **Container**: central_redis (external)
- **Connection**: Uses shared `database_db_network`

## Common Commands

### Start Services
```bash
docker-compose up -d
```

### Stop Services
```bash
docker-compose down
```

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f nginx
```

### Execute Artisan Commands
```bash
docker-compose exec app php artisan <command>

# Examples:
docker-compose exec app php artisan make:model Post
docker-compose exec app php artisan make:migration create_posts_table
docker-compose exec app php artisan make:controller PostController
docker-compose exec app php artisan tinker
```

### Database
```bash
# Migrate
docker-compose exec app php artisan migrate

# Seed
docker-compose exec app php artisan db:seed

# Fresh migration
docker-compose exec app php artisan migrate:fresh

# Rollback
docker-compose exec app php artisan migrate:rollback
```

### NPM
```bash
# Install packages
docker-compose exec app npm install

# Build assets
docker-compose exec app npm run build

# Dev watch
docker-compose exec app npm run dev
```

### Testing
```bash
docker-compose exec app php artisan test
```

## Database Connection

### From PHP Application
Already configured in `.env`:
```
DB_HOST=central_mysql
DB_PORT=3306
DB_DATABASE=farm_glow
DB_USERNAME=admin
DB_PASSWORD=admin123
```

### From Host Machine
```bash
# Ensure central_mysql container is running
mysql -h 127.0.0.1 -u admin -p farm_glow
# Password: admin123

# Or with specific database
mysql -h localhost -u admin -p farm_glow
```

## Redis Connection

### From PHP Application
```
REDIS_HOST=central_redis
REDIS_PORT=6379
```

### From Host Machine
```bash
# Ensure central_redis container is running
redis-cli -h localhost -p 6379
```

## Environment Configuration

Update `.env` file for your needs:

```env
APP_NAME="Farm Glow"
APP_ENV=local
APP_DEBUG=true
APP_KEY=<generated automatically>
APP_URL=http://localhost:8007

DB_CONNECTION=mysql
DB_HOST=central_mysql
DB_PORT=3306
DB_DATABASE=farm_glow
DB_USERNAME=admin
DB_PASSWORD=admin123

REDIS_HOST=central_redis
REDIS_PORT=6379

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=cookie
```

## Troubleshooting

### Services Won't Start
```bash
# Check service status
docker-compose ps

# Check logs
docker-compose logs app
docker-compose logs nginx
docker-compose logs mysql

# Restart services
docker-compose restart
```

### Permission Denied
```bash
# Fix ownership
docker-compose exec app chown -R www-data:www-data /var/www
```

### Database Connection Error
```bash
# Check MySQL container
docker-compose logs mysql

# Ensure MySQL is running
docker-compose ps mysql

# Restart MySQL
docker-compose restart mysql
```

### Port Already in Use
Edit `docker-compose.yml` and change the port mappings:
```yaml
ports:
  - "8008:80"  # Change from 8007 to any available port
```

## File Permissions

The Docker setup handles permissions automatically, but if you need to fix them:

```bash
# From host
sudo chown -R www-data:www-data /home/monsur/Documents/farm-glow-backend/storage
sudo chmod -R 755 /home/monsur/Documents/farm-glow-backend/storage

# Or from container
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chmod -R 755 /var/www/storage
```

## Development Workflow

1. **Create Models & Migrations**
   ```bash
   docker-compose exec app php artisan make:model Post -m
   ```

2. **Create Controllers**
   ```bash
   docker-compose exec app php artisan make:controller PostController -r
   ```

3. **Run Migrations**
   ```bash
   docker-compose exec app php artisan migrate
   ```

4. **Watch Assets**
   ```bash
   docker-compose exec app npm run dev
   ```

5. **Access Application**
   - Open browser: http://localhost:8007

## Production Deployment

For production, update:
1. `APP_ENV=production`
2. `APP_DEBUG=false`
3. Database credentials
4. Redis configuration
5. Consider using a managed database service (AWS RDS, etc.)

## Additional Resources

- [Laravel 13 Documentation](https://laravel.com/docs/13)
- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Nginx Configuration](https://nginx.org/en/docs/)

## Support

For issues or questions, refer to:
- Laravel Documentation
- Docker Documentation
- This project's issue tracker

---

**Last Updated**: April 19, 2026

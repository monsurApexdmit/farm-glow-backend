#!/bin/bash

# Farm Glow Backend Setup Script

echo "🚀 Starting Farm Glow Backend Setup..."

# Build Docker images
echo "📦 Building Docker images..."
docker compose build

# Start services
echo "🐳 Starting Docker services..."
docker compose up -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 15

# Create Laravel project in a temp location then copy
echo "🎨 Creating Laravel 13 project..."
docker compose exec -T app bash -c "rm -rf /tmp/laravel-temp && mkdir -p /tmp/laravel-temp && cd /tmp/laravel-temp && composer create-project laravel/laravel . --no-interaction && cp -r /tmp/laravel-temp/* /var/www/ && cp -r /tmp/laravel-temp/.* /var/www/ 2>/dev/null || true && rm -rf /tmp/laravel-temp"

# Copy environment file
echo "⚙️  Setting up environment..."
docker compose exec -T app cp .env.example .env

# Generate app key
echo "🔑 Generating application key..."
docker compose exec -T app php artisan key:generate

# Install npm dependencies
echo "📚 Installing npm dependencies..."
docker compose exec -T app npm install

echo "✅ Setup complete!"
echo ""
echo "📝 Next steps:"
echo "1. Your Laravel 13 app is running at http://localhost:8006"
echo "2. Update .env file if needed"
echo "3. Database: central_mysql"
echo "4. Cache: central_redis"
echo ""
echo "Useful commands:"
echo "  docker compose up -d     # Start services"
echo "  docker compose down      # Stop services"
echo "  docker compose logs -f   # View logs"

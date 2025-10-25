# Docker Development Environment Setup

This guide will help you set up and run your Laravel application using Docker.

## Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+

## Quick Start

### 1. Initial Setup

```bash
# Copy the Docker environment file
cp .env.docker .env

# Generate application key
docker-compose -f docker-compose-dev.yml run --rm app php artisan key:generate

# Build and start all containers
docker-compose -f docker-compose-dev.yml up -d --build
```

### 2. Database Setup

```bash
# Run migrations
docker-compose -f docker-compose-dev.yml exec app php artisan migrate

# (Optional) Seed the database
docker-compose -f docker-compose-dev.yml exec app php artisan db:seed
```

### 3. Access the Application

- **Application**: http://localhost:8000
- **MySQL**: localhost:3306
  - Database: `ecom_ai`
  - Username: `ecom_user`
  - Password: `secret_password`
- **Redis**: localhost:6379

## Services Overview

The Docker setup includes the following services:

| Service | Description | Container Name |
|---------|-------------|----------------|
| **app** | Main Laravel application (Nginx + PHP-FPM) | ecom-ai-app |
| **mysql** | MySQL 8.0 database | ecom-ai-mysql |
| **redis** | Redis 7 for caching, sessions, and queues | ecom-ai-redis |
| **queue** | Laravel queue worker | ecom-ai-queue |
| **scheduler** | Laravel task scheduler | ecom-ai-scheduler |

## Common Commands

### Container Management

```bash
# Start all containers
docker-compose -f docker-compose-dev.yml up -d

# Stop all containers
docker-compose -f docker-compose-dev.yml down

# Restart containers
docker-compose -f docker-compose-dev.yml restart

# View logs
docker-compose -f docker-compose-dev.yml logs -f

# View logs for specific service
docker-compose -f docker-compose-dev.yml logs -f app
docker-compose -f docker-compose-dev.yml logs -f queue

# Rebuild containers
docker-compose -f docker-compose-dev.yml up -d --build
```

### Laravel Artisan Commands

```bash
# Run artisan commands
docker-compose -f docker-compose-dev.yml exec app php artisan [command]

# Examples:
docker-compose -f docker-compose-dev.yml exec app php artisan migrate
docker-compose -f docker-compose-dev.yml exec app php artisan cache:clear
docker-compose -f docker-compose-dev.yml exec app php artisan config:clear
docker-compose -f docker-compose-dev.yml exec app php artisan route:list
docker-compose -f docker-compose-dev.yml exec app php artisan tinker
```

### Composer Commands

```bash
# Install dependencies
docker-compose -f docker-compose-dev.yml exec app composer install

# Update dependencies
docker-compose -f docker-compose-dev.yml exec app composer update

# Require new package
docker-compose -f docker-compose-dev.yml exec app composer require [package]
```

### NPM Commands

```bash
# Install Node.js dependencies
docker-compose -f docker-compose-dev.yml run --rm app npm install

# Build assets
docker-compose -f docker-compose-dev.yml run --rm app npm run build

# Run dev server (for live asset compilation)
docker-compose -f docker-compose-dev.yml run --rm app npm run dev
```

### Database Commands

```bash
# Access MySQL CLI
docker-compose -f docker-compose-dev.yml exec mysql mysql -u ecom_user -psecret_password ecom_ai

# Backup database
docker-compose -f docker-compose-dev.yml exec mysql mysqldump -u ecom_user -psecret_password ecom_ai > backup.sql

# Restore database
docker-compose -f docker-compose-dev.yml exec -T mysql mysql -u ecom_user -psecret_password ecom_ai < backup.sql
```

### Redis Commands

```bash
# Access Redis CLI
docker-compose -f docker-compose-dev.yml exec redis redis-cli

# Clear cache
docker-compose -f docker-compose-dev.yml exec redis redis-cli FLUSHALL
```

## File Structure

```
.
├── Dockerfile                      # Multi-stage build configuration
├── docker-compose-dev.yml          # Development environment services
├── .dockerignore                   # Files to exclude from Docker build
├── .env.docker                     # Docker environment variables template
├── docker/
│   ├── nginx/
│   │   ├── nginx.conf             # Nginx main configuration
│   │   └── default.conf           # Laravel site configuration
│   └── supervisor/
│       └── supervisord.conf       # Process manager configuration
└── README.docker.md               # This file
```

## Volume Management

The setup uses named volumes for data persistence:

- `mysql-data`: MySQL database files
- `redis-data`: Redis persistence
- `storage-data`: Laravel storage directory

### Backup Volumes

```bash
# Backup MySQL volume
docker run --rm -v ecom-ai_mysql-data:/data -v $(pwd):/backup alpine tar czf /backup/mysql-backup.tar.gz -C /data .

# Restore MySQL volume
docker run --rm -v ecom-ai_mysql-data:/data -v $(pwd):/backup alpine tar xzf /backup/mysql-backup.tar.gz -C /data
```

### Remove Volumes

```bash
# Remove all volumes (WARNING: This will delete all data!)
docker-compose -f docker-compose-dev.yml down -v
```

## Troubleshooting

### Permission Issues

If you encounter permission issues with storage or cache:

```bash
docker-compose -f docker-compose-dev.yml exec app chmod -R 775 storage bootstrap/cache
docker-compose -f docker-compose-dev.yml exec app chown -R www-data:www-data storage bootstrap/cache
```

### Container Won't Start

Check container logs:
```bash
docker-compose -f docker-compose-dev.yml logs [service-name]
```

### Database Connection Issues

1. Ensure MySQL container is healthy:
   ```bash
   docker-compose -f docker-compose-dev.yml ps
   ```

2. Test database connection:
   ```bash
   docker-compose -f docker-compose-dev.yml exec app php artisan db:show
   ```

### Queue Not Processing Jobs

1. Check queue worker logs:
   ```bash
   docker-compose -f docker-compose-dev.yml logs -f queue
   ```

2. Restart queue worker:
   ```bash
   docker-compose -f docker-compose-dev.yml restart queue
   ```

### Clear All Caches

```bash
docker-compose -f docker-compose-dev.yml exec app php artisan optimize:clear
docker-compose -f docker-compose-dev.yml exec redis redis-cli FLUSHALL
```

## Development Workflow

### Making Code Changes

1. Your code is mounted as a volume, so changes are reflected immediately
2. For PHP changes, no restart needed
3. For configuration changes, clear config cache:
   ```bash
   docker-compose -f docker-compose-dev.yml exec app php artisan config:clear
   ```

### Adding New Dependencies

```bash
# PHP dependencies
docker-compose -f docker-compose-dev.yml exec app composer require [package]

# JavaScript dependencies
docker-compose -f docker-compose-dev.yml run --rm app npm install [package]
```

### Running Tests

```bash
# Run all tests
docker-compose -f docker-compose-dev.yml exec app php artisan test

# Run specific test
docker-compose -f docker-compose-dev.yml exec app php artisan test --filter=[TestName]
```

## Production Considerations

This setup is optimized for development. For production:

1. Use separate `docker-compose.prod.yml` with:
   - No volume mounts for code
   - Environment-specific configurations
   - Proper secrets management
   - Health checks and restart policies

2. Update `.env` with production values
3. Disable debug mode (`APP_DEBUG=false`)
4. Use proper database credentials
5. Configure SSL/TLS certificates
6. Set up proper logging and monitoring

## Security Notes

⚠️ **IMPORTANT**: The default credentials in this setup are for development only!

Before deploying to any shared or production environment:

1. Change all default passwords in `.env`
2. Use strong, unique passwords for database
3. Restrict port access (don't expose 3306, 6379 publicly)
4. Use proper firewall rules
5. Enable SSL/TLS
6. Review and update security headers in Nginx config

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)

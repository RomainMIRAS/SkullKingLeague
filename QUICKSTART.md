# 🐳 Quick Start Guide - Docker Development Environment

## Get Started in 30 Seconds

```bash
# 1. Clone the repository
git clone https://github.com/Brice19/SkullKingLeague.git
cd SkullKingLeague

# 2. Start the development environment
./docker-dev.sh up

# 3. Access the application
# 🌐 App: http://localhost:8080
# 🔧 Admin: http://localhost:8080/?page=admin (admin/admin123)
# 🗄️ Database: http://localhost:8081 (optional PHPMyAdmin)
```

## What You Get

- **Complete PHP Development Environment**: PHP 8.3 + Apache + MySQL 8.0
- **Instant Database Setup**: Pre-configured with test users and admin account
- **No Local Dependencies**: Everything runs in isolated containers
- **Hot Reload**: Changes to code are immediately reflected
- **Database Persistence**: Data survives container restarts

## Available Commands

```bash
./docker-dev.sh up          # Start everything
./docker-dev.sh down        # Stop everything
./docker-dev.sh logs        # View real-time logs
./docker-dev.sh shell       # Open app container shell
./docker-dev.sh db-shell    # Open MySQL shell
./docker-dev.sh test        # Run functionality tests
./docker-dev.sh status      # Check service status
./docker-dev.sh help        # See all commands
```

## For VS Code Users

1. Install the "Dev Containers" extension
2. Open the project in VS Code
3. Click "Reopen in Container" when prompted
4. Full PHP development environment with debugging ready!

## Testing Pull Requests

When you open a pull request:
- GitHub Actions automatically builds and tests your changes
- A preview environment is created with unique URL
- Automated tests verify web server and database connectivity
- Environment is cleaned up when PR is closed

## Troubleshooting

**Port conflicts?** Edit `docker-compose.yml` to change ports:
```yaml
ports:
  - "8081:80"  # Change 8080 to 8081
```

**Database issues?** Reinitialize:
```bash
./docker-dev.sh down
docker volume rm skullkingleague_mysql_data
./docker-dev.sh up
```

**Need help?** Check the logs:
```bash
./docker-dev.sh logs
```

## What's Running

| Service | URL | Purpose |
|---------|-----|---------|
| Web App | http://localhost:8080 | Main Skull King League application |
| Admin | http://localhost:8080/?page=admin | Admin panel (admin/admin123) |
| PHPMyAdmin | http://localhost:8081 | Database management (optional) |
| MySQL | localhost:3306 | Database server |

## Ready to Develop!

The development environment is now fully set up with:
- ✅ Working web application 
- ✅ Initialized database with test users
- ✅ Admin account ready
- ✅ All dependencies installed
- ✅ Hot reload for development

Start coding and your changes will be immediately visible at http://localhost:8080!
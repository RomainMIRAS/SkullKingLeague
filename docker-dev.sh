#!/bin/bash

# Skull King League - Docker Development Setup
# This script provides easy commands for Docker-based development

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if Docker is running
check_docker() {
    if ! docker info > /dev/null 2>&1; then
        print_error "Docker is not running. Please start Docker and try again."
        exit 1
    fi
}

# Function to show usage
show_usage() {
    echo "🏴‍☠️ Skull King League - Docker Development Helper"
    echo ""
    echo "Usage: $0 [COMMAND]"
    echo ""
    echo "Commands:"
    echo "  up          Start the development environment"
    echo "  down        Stop the development environment"
    echo "  restart     Restart the development environment"
    echo "  build       Build the Docker images"
    echo "  logs        Show logs from all services"
    echo "  shell       Open a shell in the app container"
    echo "  db-shell    Open a MySQL shell"
    echo "  init-db     Initialize the database"
    echo "  status      Show status of services"
    echo "  clean       Clean up containers and volumes"
    echo "  test        Run basic functionality tests"
    echo "  help        Show this help message"
}

# Function to start the environment
start_env() {
    print_status "Starting Skull King League development environment..."
    
    # Create .env from example if it doesn't exist
    if [ ! -f .env ]; then
        print_status "Creating .env file from template..."
        cp .env.example .env
    fi
    
    # Start services
    docker compose up -d
    
    # Wait for services to be healthy
    print_status "Waiting for services to start..."
    timeout 60 bash -c 'until docker compose ps | grep -q "healthy"; do sleep 2; done' || {
        print_error "Services failed to start within 60 seconds"
        docker compose logs
        exit 1
    }

    docker compose exec app php config/init_db.php
    
    print_success "Environment started successfully!"
    print_status "Application: http://localhost:8080"
    print_status "PHPMyAdmin: http://localhost:8081"
    print_status "Database: localhost:3306"
}

# Function to stop the environment
stop_env() {
    print_status "Stopping development environment..."
    docker compose down
    print_success "Environment stopped."
}

# Function to restart the environment
restart_env() {
    print_status "Restarting development environment..."
    docker compose restart
    docker compose exec app php config/init_db.php
    print_success "Environment restarted."
}

# Function to build images
build_images() {
    print_status "Building Docker images..."
    docker compose build --no-cache
    print_success "Images built successfully."
}

# Function to show logs
show_logs() {
    print_status "Showing logs (Ctrl+C to exit)..."
    docker compose logs -f
}

# Function to open shell
open_shell() {
    print_status "Opening shell in app container..."
    docker compose exec app bash
}

# Function to open database shell
open_db_shell() {
    print_status "Opening MySQL shell..."
    docker compose exec mysql mysql -u skullking_user -pSkullKing_2025! skull_king_league
}

# Function to initialize database
init_database() {
    print_status "Initializing database..."
    docker compose exec app php config/init_db.php
    print_success "Database initialized."
}

# Function to show status
show_status() {
    print_status "Service status:"
    docker compose ps
}

# Function to clean up
cleanup() {
    print_warning "This will remove all containers and volumes. Are you sure? (y/N)"
    read -r response
    if [[ "$response" =~ ^([yY][eE][sS]|[yY])+$ ]]; then
        print_status "Cleaning up..."
        docker compose down -v --remove-orphans
        docker system prune -f
        print_success "Cleanup completed."
    else
        print_status "Cleanup cancelled."
    fi
}

# Function to run tests
run_tests() {
    print_status "Running basic functionality tests..."
    
    # Check if services are running
    if ! docker compose ps | grep -q "Up"; then
        print_error "Services are not running. Start them first with: $0 up"
        exit 1
    fi
    
    # Test web server
    print_status "Testing web server..."
    if curl -f http://localhost:8080/ > /dev/null 2>&1; then
        print_success "Web server is responding"
    else
        print_error "Web server is not responding"
        exit 1
    fi
    
    # Test database
    print_status "Testing database connection..."
    if docker compose exec -T mysql mysql -u skullking_user -pSkullKing_2025! -e "SELECT 1;" skull_king_league > /dev/null 2>&1; then
        print_success "Database connection working"
    else
        print_error "Database connection failed"
        exit 1
    fi
    
    print_success "All tests passed!"
}

# Main script logic
check_docker

case "${1:-help}" in
    up)
        start_env
        ;;
    down)
        stop_env
        ;;
    restart)
        restart_env
        ;;
    build)
        build_images
        ;;
    logs)
        show_logs
        ;;
    shell)
        open_shell
        ;;
    db-shell)
        open_db_shell
        ;;
    init-db)
        init_database
        ;;
    status)
        show_status
        ;;
    clean)
        cleanup
        ;;
    test)
        run_tests
        ;;
    help|*)
        show_usage
        ;;
esac
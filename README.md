# 🏴‍☠️ Skull King League

A modern, responsive web application for managing Skull King card game tournaments with advanced ELO ranking system and seasonal competitions.

[![Docker](https://img.shields.io/badge/Docker-Ready-blue?style=flat&logo=docker)](https://docker.com)
[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql)](https://mysql.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## 🎯 Overview

**Skull King League** is a comprehensive tournament management system designed specifically for the Skull King card game. It features an intuitive mobile-first interface for game management, real-time scoring, and a sophisticated ELO ranking system to track player performance across multiple seasons.

Perfect for gaming groups, clubs, and communities who want to organize competitive Skull King tournaments with professional-grade statistics and rankings.

---

## ✨ Key Features

### 🎮 **Game Management**
- **Smart Player Selection**: Choose 1-6 players from registered users
- **Interactive Scoring**: Mobile-optimized interface for round-by-round score entry  
- **Real-time Calculations**: Live score totals and winner determination
- **10-Round Structure**: Complete tournament format with automatic progression
- **Starting Player Rotation**: Intelligent player order management

### 🏆 **Advanced ELO System**
- **Dynamic Ratings**: Sophisticated ELO calculations based on opponent strength
- **Rank-based Adjustments**: Multiple player support with proper tie handling
- **Performance Tracking**: Detailed win/loss ratios and game statistics
- **Historical Analysis**: Complete ELO change history for every game

### 📅 **Season Management**
- **Multi-Season Support**: Organize competitions across different time periods
- **Season Statistics**: Dedicated rankings and stats for each season
- **ELO Reset System**: Fresh starts with preserved historical data
- **Final Rankings**: Permanent record of season champions and standings
- **Seamless Transitions**: Automated season rollover with data preservation

### 📊 **Comprehensive Statistics**
- **Player Profiles**: Individual performance metrics and game history
- **Leaderboards**: Real-time rankings with detailed statistics
- **Game History**: Complete record of all matches with searchable filters
- **Visual Indicators**: Trophy icons, badges, and performance indicators
- **Win Rate Analysis**: Detailed percentage calculations and trends

### 🛠️ **Administrative Tools**
- **User Management**: Add, modify, and manage player accounts
- **Game Oversight**: Monitor ongoing games and manage completed matches
- **Season Control**: Create new seasons and manage transitions
- **Data Management**: Export capabilities and database maintenance
- **Security Features**: Protected admin access with session management

### 📱 **Mobile-First Design**
- **Responsive Interface**: Optimized for smartphones and tablets
- **Touch-Friendly Controls**: Large buttons and intuitive navigation
- **Real-time Updates**: Live score tracking and instant feedback
- **Offline Capability**: PWA support for offline gameplay (future enhancement)

---

## 🛠️ **Technology Stack**

- **Frontend**: HTML5, CSS3 (Bootstrap 5), Vanilla JavaScript
- **Backend**: PHP 8.0+ with MVC architecture
- **Database**: MySQL 8.0+ with optimized queries
- **Containerization**: Docker & Docker Compose
- **Web Server**: Apache with mod_rewrite
- **Security**: Session-based authentication with password hashing

---

## 🚀 **Quick Start with Docker**

### Prerequisites
- **Docker** 20.10+
- **Docker Compose** 2.0+
- **Git** (for cloning)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/RomainMIRAS/SkullKingLeague.git
   cd SkullKingLeague
   ```

2. **Start the application**
   ```bash
   ./docker-dev.sh up
   ```

3. **Access the application**
   - **Main Application**: http://localhost:8080
   - **Admin Panel**: http://localhost:8080/?page=admin
   - **PHPMyAdmin**: http://localhost:8081

The application will automatically:
- Build the Docker containers
- Initialize the MySQL database
- Create necessary tables and sample data
- Start all services with health checks

### Development Commands

Our custom `docker-dev.sh` script provides convenient commands for development:

```bash
# Start the environment
./docker-dev.sh up

# Stop the environment  
./docker-dev.sh down

# Restart services
./docker-dev.sh restart

# View real-time logs
./docker-dev.sh logs

# Open shell in app container
./docker-dev.sh shell

# Access MySQL shell
./docker-dev.sh db-shell

# Initialize/reset database
./docker-dev.sh init-db

# Check service status
./docker-dev.sh status

# Run health tests
./docker-dev.sh test

# Clean up containers and volumes
./docker-dev.sh clean

# View all available commands
./docker-dev.sh help
```

### Configuration

1. **Environment Variables**: Copy `.env.example` to `.env` and customize as needed
2. **Port Customization**: Modify `docker-compose.yml` if ports 8080/3306 are occupied
3. **Database Persistence**: MySQL data is automatically persisted in Docker volumes

---

## 📊 **Database Schema**

### Core Tables

| Table | Purpose | Key Features |
|-------|---------|--------------|
| `users` | Player profiles | ELO ratings, game statistics |
| `games` | Match records | Season linking, game status |
| `game_players` | Player participation | Score tracking, player order |
| `rounds` | Round-by-round scores | Detailed scoring history |
| `seasons` | Tournament periods | Current/historical seasons |
| `season_stats` | Season rankings | Final standings archive |
| `elo_history` | Rating changes | Complete ELO audit trail |

### Advanced Features
- **Foreign Key Constraints**: Ensure data integrity
- **Optimized Indexes**: Fast queries for rankings and statistics  
- **Automatic Timestamps**: Track all data changes
- **Soft Deletes**: Preserve historical data integrity

---

## 🎯 **Core Gameplay Flow**

1. **Game Creation**: Select players and start a new 10-round match
2. **Round Scoring**: Enter scores for each player with validation
3. **Progress Tracking**: View current standings and round progression
4. **Game Completion**: Automatic winner calculation and ELO updates
5. **Statistics Update**: Real-time ranking adjustments and history tracking

---

## 🏆 **ELO Rating System**

Our implementation uses a sophisticated multi-player ELO system:

- **Base Rating**: All players start at 1000 ELO
- **K-Factor**: 32 for balanced rating changes
- **Multi-player Support**: Proper handling of 3-6 player games
- **Tie Resolution**: Equal ELO changes for tied players
- **Expected Score**: Dynamic calculation based on all opponents

**Rating Tiers**:
- 🔴 **Master**: 1400+ ELO
- 🟡 **Expert**: 1200-1399 ELO  
- 🔵 **Advanced**: 1000-1199 ELO
- ⚫ **Beginner**: <1000 ELO

---

## 🔐 **Security Features**

- **Admin Authentication**: Secure login with hashed passwords
- **Session Management**: Proper session handling and timeouts
- **Input Validation**: SQL injection and XSS prevention
- **Database Security**: Parameterized queries and prepared statements
- **Access Control**: Role-based permissions for admin functions

---

## 🧪 **Development & Testing**

### Automated Health Checks
The Docker setup includes comprehensive health monitoring:
- Web server connectivity validation
- Database connection testing  
- Service startup verification
- Automatic error reporting

### Development Environment
- **Live Reloading**: Changes reflected immediately
- **Debug Mode**: Detailed error reporting in development
- **Database Tools**: Direct access via PHPMyAdmin
- **Log Monitoring**: Real-time application and system logs

---

## 🌟 **Future Enhancements**

- **PWA Support**: Offline gameplay capability
- **Data Export**: CSV export for external analysis
- **Player Ratings**: Sportsmanship and fair-play scores
- **Custom Rules**: Tournament-specific scoring modifications
- **API Development**: REST API for mobile app integration
- **Advanced Analytics**: Detailed performance insights and trends

---

## 📖 **Manual Installation**

For traditional server deployments without Docker, please refer to our [detailed installation guide](INSTALL.md) covering:
- PHP and MySQL configuration
- Apache/Nginx setup
- Manual database initialization
- Production security considerations

---

## 🤝 **Contributing**

We welcome contributions! Please see our contribution guidelines for:
- Code style standards
- Testing requirements  
- Pull request process
- Issue reporting

---

## 📄 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

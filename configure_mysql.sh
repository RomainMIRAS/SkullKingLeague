#!/bin/bash

echo "🔧 Configuration de MySQL pour Skull King League"
echo "================================================"

# Arrêter MySQL temporairement
echo "1. Arrêt de MySQL/MariaDB..."
sudo systemctl stop mysql

# Démarrer MySQL en mode sans vérification de mot de passe
echo "2. Démarrage en mode sûr..."
sudo mysqld_safe --skip-grant-tables --skip-networking &
sleep 5

# Se connecter et configurer l'utilisateur root
echo "3. Configuration de l'utilisateur root..."
mysql -u root << EOF
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY '';
FLUSH PRIVILEGES;
EXIT;
EOF

# Arrêter le processus MySQL sûr
echo "4. Redémarrage de MySQL..."
sudo pkill mysqld
sleep 3
sudo systemctl start mysql

# Créer la base de données et l'utilisateur pour l'application
echo "5. Création de la base de données..."
mysql -u root << EOF
CREATE DATABASE IF NOT EXISTS skull_king_league CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER IF NOT EXISTS 'skull_king'@'localhost' IDENTIFIED BY 'skull_king_pass';
GRANT ALL PRIVILEGES ON skull_king_league.* TO 'skull_king'@'localhost';
FLUSH PRIVILEGES;
SHOW DATABASES;
EOF

echo "✅ Configuration terminée !"
echo ""
echo "📋 Informations de connexion:"
echo "- Base de données: skull_king_league"
echo "- Utilisateur: skull_king"
echo "- Mot de passe: skull_king_pass"
echo ""
echo "🔄 Mettez à jour config/database.php avec ces informations"

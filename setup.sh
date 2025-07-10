#!/bin/bash

# Script de démarrage pour Skull King League
# Ce script configure automatiquement l'application

echo "🏴‍☠️ Initialisation de Skull King League..."

# Vérifier si nous sommes dans le bon répertoire
if [ ! -f "README.md" ] || [ ! -d "public" ]; then
    echo "❌ Erreur: Ce script doit être exécuté dans le répertoire racine de Skull King League"
    exit 1
fi

# Vérifier les prérequis
echo "🔍 Vérification des prérequis..."

# Vérifier PHP
if ! command -v php &> /dev/null; then
    echo "❌ PHP n'est pas installé"
    exit 1
fi

# Vérifier MySQL
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL n'est pas installé"
    exit 1
fi

echo "✅ Prérequis validés"

# Créer les répertoires de logs si nécessaire
echo "📁 Création des répertoires..."
mkdir -p logs
mkdir -p temp

# Définir les permissions
echo "🔐 Configuration des permissions..."
if [ "$EUID" -eq 0 ]; then
    chown -R www-data:www-data .
    chmod -R 755 .
    chmod -R 644 assets/
    chmod 755 public/
else
    echo "⚠️  Exécutez avec sudo pour configurer les permissions automatiquement"
fi

# Vérifier la configuration de la base de données
echo "🗄️  Configuration de la base de données..."

# Demander les paramètres de connexion si config par défaut
read -p "Nom de la base de données [skull_king_league]: " DB_NAME
DB_NAME=${DB_NAME:-skull_king_league}

read -p "Utilisateur MySQL [root]: " DB_USER
DB_USER=${DB_USER:-root}

read -s -p "Mot de passe MySQL: " DB_PASS
echo

read -p "Host MySQL [localhost]: " DB_HOST
DB_HOST=${DB_HOST:-localhost}

# Mettre à jour la configuration si nécessaire
if [ "$DB_NAME" != "skull_king_league" ] || [ "$DB_USER" != "root" ] || [ "$DB_HOST" != "localhost" ]; then
    echo "📝 Mise à jour de la configuration..."
    sed -i "s/private \$host = 'localhost';/private \$host = '$DB_HOST';/" config/database.php
    sed -i "s/private \$db_name = 'skull_king_league';/private \$db_name = '$DB_NAME';/" config/database.php
    sed -i "s/private \$username = 'root';/private \$username = '$DB_USER';/" config/database.php
    sed -i "s/private \$password = '';/private \$password = '$DB_PASS';/" config/database.php
fi

# Tester la connexion et initialiser la base
echo "🔗 Test de connexion et initialisation..."
if php -f config/init_db.php; then
    echo "✅ Base de données initialisée avec succès"
else
    echo "❌ Erreur lors de l'initialisation de la base de données"
    exit 1
fi

# Vérifier la configuration Apache/Nginx
echo "🌐 Vérification du serveur web..."

# Vérifier si Apache est en cours d'exécution
if systemctl is-active --quiet apache2; then
    echo "✅ Apache détecté et actif"
    
    # Vérifier mod_rewrite
    if apache2ctl -M | grep -q rewrite; then
        echo "✅ mod_rewrite activé"
    else
        echo "⚠️  mod_rewrite non activé - activation requise"
        if [ "$EUID" -eq 0 ]; then
            a2enmod rewrite
            systemctl reload apache2
            echo "✅ mod_rewrite activé"
        else
            echo "   Exécutez: sudo a2enmod rewrite && sudo systemctl reload apache2"
        fi
    fi
elif systemctl is-active --quiet nginx; then
    echo "✅ Nginx détecté et actif"
else
    echo "⚠️  Aucun serveur web détecté en cours d'exécution"
fi

# Créer un script de test
echo "🧪 Création du script de test..."
cat > test_install.php << 'EOF'
<?php
echo "🏴‍☠️ Test d'installation Skull King League\n\n";

// Test PHP
echo "PHP Version: " . PHP_VERSION . "\n";

// Test extensions
$extensions = ['pdo', 'pdo_mysql', 'mbstring', 'session'];
foreach ($extensions as $ext) {
    echo "Extension $ext: " . (extension_loaded($ext) ? "✅" : "❌") . "\n";
}

// Test base de données
try {
    require_once 'config/database.php';
    $db = new Database();
    $conn = $db->getConnection();
    echo "Connexion DB: ✅\n";
    
    // Test tables
    $tables = ['users', 'games', 'game_players', 'rounds', 'admin'];
    foreach ($tables as $table) {
        $stmt = $conn->query("SELECT COUNT(*) FROM $table");
        echo "Table $table: ✅ (" . $stmt->fetchColumn() . " enregistrements)\n";
    }
} catch (Exception $e) {
    echo "Connexion DB: ❌ " . $e->getMessage() . "\n";
}

echo "\n🎮 Installation terminée !\n";
echo "Accédez à votre application via votre navigateur.\n";
echo "Panneau admin: ?page=admin (admin/admin123)\n";
EOF

# Exécuter le test
echo ""
echo "🧪 Test de l'installation..."
php test_install.php

# Nettoyer
rm test_install.php

echo ""
echo "🎉 Installation terminée !"
echo ""
echo "📋 Prochaines étapes:"
echo "1. Accédez à votre application via un navigateur web"
echo "2. Connectez-vous à l'admin: ?page=admin"
echo "3. Changez le mot de passe administrateur"
echo "4. Ajoutez des joueurs et lancez votre première partie !"
echo ""
echo "🔗 Liens utiles:"
echo "- Application: http://localhost/skull-king-league/public/"
echo "- Admin: http://localhost/skull-king-league/public/?page=admin"
echo "- Documentation: README.md et INSTALL.md"
echo ""
echo "🏴‍☠️ Que la meilleure équipe gagne !"

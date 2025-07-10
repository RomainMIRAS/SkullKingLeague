#!/bin/bash

echo "🏴‍☠️ Configuration d'un utilisateur MySQL dédié pour Skull King League"
echo "=================================================================="
echo ""

# Vérifier si MySQL/MariaDB est en cours d'exécution
if ! systemctl is-active --quiet mysql && ! systemctl is-active --quiet mariadb; then
    echo "❌ MySQL/MariaDB n'est pas en cours d'exécution"
    echo "💡 Démarrage du service..."
    sudo systemctl start mysql || sudo systemctl start mariadb
    sleep 2
fi

echo "🔐 Création de l'utilisateur MySQL pour l'application..."
echo ""

# Demander le mot de passe root MySQL
echo "📝 Veuillez entrer le mot de passe root MySQL/MariaDB :"
read -s ROOT_PASSWORD

# Exécuter le script SQL
mysql -u root -p"$ROOT_PASSWORD" < create_db_user.sql

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Utilisateur 'skullking_user' créé avec succès !"
    echo ""
    echo "📋 Détails de l'utilisateur créé :"
    echo "   - Nom d'utilisateur: skullking_user"
    echo "   - Mot de passe: SkullKing_2025!"
    echo "   - Privilèges: SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, INDEX"
    echo "   - Base de données: skull_king_league"
    echo ""
    echo "🔄 Mise à jour des fichiers de configuration..."
    echo ""
    echo "⚠️  IMPORTANT: Modifiez maintenant les fichiers de configuration pour utiliser ce nouvel utilisateur"
    echo "   - config/init_db.php"
    echo "   - config/database.php"
    echo ""
    echo "🔐 Pour des raisons de sécurité, vous devriez changer le mot de passe dans le script SQL"
    echo "   et dans vos fichiers de configuration après la première utilisation."
else
    echo ""
    echo "❌ Erreur lors de la création de l'utilisateur"
    echo "💡 Vérifiez votre mot de passe root MySQL"
fi

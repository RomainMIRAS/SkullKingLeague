# Installation et Configuration - Skull King League

## 🔧 Prérequis

- **Serveur web** : Apache ou Nginx
- **PHP** : Version 7.4 ou supérieure
- **Base de données** : MySQL 5.7+ ou MariaDB 10.3+
- **Extensions PHP requises** :
  - PDO
  - PDO_MySQL
  - mbstring
  - session

## 📋 Installation

### 1. Configuration de la base de données

1. **Créer la base de données** (optionnel, le script peut la créer automatiquement) :
   ```sql
   CREATE DATABASE skull_king_league CHARACTER SET utf8 COLLATE utf8_general_ci;
   ```

2. **Configurer les paramètres** dans `config/database.php` :
   ```php
   private $host = 'localhost';
   private $db_name = 'skull_king_league';
   private $username = 'votre_utilisateur';
   private $password = 'votre_mot_de_passe';
   ```

3. **Initialiser la base de données** :
   Visitez : `http://votre-domaine/config/init_db.php`

### 2. Configuration du serveur web

#### Apache
Assurez-vous que le module `mod_rewrite` est activé :
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx
Configuration de base :
```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/html/Skull King League/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 3. Permissions

```bash
# Donner les bonnes permissions
sudo chown -R www-data:www-data /var/www/html/Skull\ King\ League/
sudo chmod -R 755 /var/www/html/Skull\ King\ League/
```

## 🔑 Accès administrateur

- **URL** : `http://votre-domaine/index.php?page=admin`
- **Utilisateur par défaut** : `admin`
- **Mot de passe par défaut** : `admin123`

⚠️ **IMPORTANT** : Changez le mot de passe administrateur après la première connexion !

## 📁 Structure du projet

```
Skull King League/
├── public/              # Point d'entrée web
│   └── index.php
├── src/
│   ├── controllers/     # Logique métier
│   ├── models/         # Modèles de données
│   └── views/          # Templates
├── config/
│   ├── database.php    # Configuration DB
│   └── init_db.php     # Script d'initialisation
├── assets/
│   ├── css/           # Styles
│   └── js/            # Scripts JavaScript
└── README.md
```

## 🚀 Utilisation

### Interface utilisateur
1. **Accueil** : Lancer une partie, voir classements et historique
2. **Nouvelle partie** : Sélectionner 1-6 joueurs et commencer
3. **Jeu** : Entrer les scores manche par manche (10 manches total)
4. **Classement** : Système ELO automatique
5. **Historique** : Toutes les parties avec détails

### Interface administrateur
1. **Gestion utilisateurs** : Ajouter/supprimer des joueurs
2. **Gestion parties** : Voir/supprimer des parties
3. **Statistiques** : Vue d'ensemble de la ligue

## 🛠️ Maintenance

### Sauvegarde
```bash
# Base de données
mysqldump -u username -p skull_king_league > backup.sql

# Fichiers
tar -czf skull_king_backup.tar.gz /var/www/html/Skull\ King\ League/
```

### Mise à jour
1. Sauvegarder la base de données
2. Remplacer les fichiers (sauf `config/database.php`)
3. Exécuter les migrations si nécessaire

## 🐛 Dépannage

### Erreur de connexion à la base
- Vérifiez les paramètres dans `config/database.php`
- Assurez-vous que MySQL/MariaDB fonctionne
- Vérifiez les permissions utilisateur

### Erreur 500
- Vérifiez les logs Apache/Nginx
- Vérifiez les permissions des fichiers
- Assurez-vous que PHP PDO est installé

### Pages non trouvées
- Vérifiez que `mod_rewrite` est activé (Apache)
- Vérifiez la configuration Nginx
- Vérifiez les fichiers `.htaccess`

## 📞 Support

Pour toute question ou problème :
1. Vérifiez les logs du serveur web
2. Consultez la documentation PHP/MySQL
3. Vérifiez les permissions des fichiers

## 🔒 Sécurité

- Changez le mot de passe admin par défaut
- Utilisez HTTPS en production
- Mettez à jour régulièrement PHP et MySQL
- Limitez l'accès au panneau d'administration par IP si possible

## 📱 Responsive Design

L'application est optimisée pour :
- Desktop (1200px+)
- Tablette (768px-1199px)
- Mobile (320px-767px)

## 🎮 Règles Skull King

L'application gère automatiquement :
- 10 manches par partie
- 1-6 joueurs
- Scores de -100 à +100 par manche
- Calcul automatique du gagnant
- Mise à jour ELO après chaque partie

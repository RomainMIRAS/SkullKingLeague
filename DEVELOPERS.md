# Documentation Développeur - Skull King League

## 🏗️ Architecture

### Structure MVC simplifiée
- **Models** (`src/models/`) : Logique de données et base de données
- **Views** (`src/views/`) : Templates HTML avec PHP intégré
- **Controllers** (`src/controllers/`) : Logique métier et routage

### Autoloader
Système d'autoloading simple dans `public/index.php` qui charge automatiquement les classes depuis :
- `src/models/`
- `src/controllers/`
- `config/`

## 📊 Base de données

### Tables principales

#### `users`
```sql
id (INT, AUTO_INCREMENT, PRIMARY KEY)
pseudo (VARCHAR(50), UNIQUE, NOT NULL)
elo (INT, DEFAULT 1000)
parties_jouees (INT, DEFAULT 0)
victoires (INT, DEFAULT 0)
created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
```

#### `games`
```sql
id (INT, AUTO_INCREMENT, PRIMARY KEY)
date_partie (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
gagnant_id (INT, FOREIGN KEY -> users.id)
status (ENUM('en_cours', 'terminee'), DEFAULT 'en_cours')
```

#### `game_players`
```sql
id (INT, AUTO_INCREMENT, PRIMARY KEY)
game_id (INT, FOREIGN KEY -> games.id)
user_id (INT, FOREIGN KEY -> users.id)
score_total (INT, DEFAULT 0)
```

#### `rounds`
```sql
id (INT, AUTO_INCREMENT, PRIMARY KEY)
game_id (INT, FOREIGN KEY -> games.id)
numero_manche (INT)
player_id (INT, FOREIGN KEY -> users.id)
score (INT)
```

#### `admin`
```sql
id (INT, AUTO_INCREMENT, PRIMARY KEY)
username (VARCHAR(50), UNIQUE, NOT NULL)
password (VARCHAR(255), NOT NULL)
```

## 🎯 Système ELO

### Algorithme utilisé
Basé sur le système ELO classique avec :
- **K-factor** : 32 (constante)
- **Calcul** : `ELO_nouveau = ELO_ancien + K × (résultat - espérance)`
- **Résultat** : 1 pour victoire, 0 pour défaite
- **Espérance** : Basée sur la différence d'ELO avec les adversaires

### Implémentation
Classe `EloCalculator` dans `src/models/EloCalculator.php` :
- `calculateNewElo()` : Calcule le nouvel ELO d'un joueur
- `updateElosAfterGame()` : Met à jour tous les ELO après une partie

## 🔀 Routage

### URL Structure
- `?page=home` : Page d'accueil
- `?page=game&action=create` : Créer une partie
- `?page=game&action=play&id=X` : Jouer une partie
- `?page=ranking` : Classement
- `?page=history` : Historique
- `?page=admin` : Administration

### Contrôleurs
- **GameController** : Gestion des parties (création, jeu, fin)
- **AdminController** : Gestion administrative

## 🛡️ Sécurité

### Authentification Admin
- Sessions PHP pour maintenir la connexion
- Mots de passe hashés avec `password_hash()`
- Vérification `isAdminLoggedIn()` sur toutes les pages admin

### Protection des données
- Échappement HTML avec `htmlspecialchars()`
- Requêtes préparées PDO pour éviter les injections SQL
- Validation des entrées utilisateur

### Headers de sécurité
Configurés dans `.htaccess` :
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `X-XSS-Protection: 1; mode=block`

## 📱 Frontend

### Framework CSS
- **Bootstrap 5.3** : Framework responsive
- **Bootstrap Icons** : Icônes

### JavaScript
- **Vanilla JS** : Pas de framework lourd
- **Alpine.js** compatible si besoin d'ajouts
- Auto-sauvegarde locale avec `localStorage`
- Validation côté client

### Responsive Design
- Mobile-first approach
- Breakpoints Bootstrap standards
- Optimisé pour écrans tactiles

## 🔧 Développement

### Standards de code
- **PHP** : PSR-1 et PSR-2 compatible
- **HTML** : HTML5 valide
- **CSS** : Organisation modulaire
- **JS** : ES6+ avec fallbacks

### Structure des fichiers
```
src/
├── models/
│   ├── User.php           # Gestion utilisateurs
│   ├── Game.php           # Gestion parties
│   └── EloCalculator.php  # Calculs ELO
├── controllers/
│   ├── GameController.php # Contrôleur parties
│   └── AdminController.php # Contrôleur admin
└── views/
    ├── header.php         # En-tête commun
    ├── footer.php         # Pied de page
    ├── home.php           # Page d'accueil
    ├── ranking.php        # Classement
    ├── history.php        # Historique
    ├── game_*.php         # Vues de jeu
    └── admin_*.php        # Vues admin
```

## 🚀 Déploiement

### Environnement de développement
1. Cloner le projet
2. Configurer `config/database.php`
3. Exécuter `./setup.sh`
4. Accéder via `http://localhost/`

### Production
1. Configurer serveur web (Apache/Nginx)
2. Sécuriser `config/database.php`
3. Activer HTTPS
4. Configurer sauvegardes automatiques

## 🧪 Tests

### Tests manuels recommandés
1. **Création partie** : 1-6 joueurs
2. **Jeu complet** : 10 manches, scores variés
3. **Calcul ELO** : Vérifier cohérence
4. **Administration** : CRUD utilisateurs/parties
5. **Responsive** : Tester sur mobile/tablette

### Points de test critique
- Validation des scores (-100 à +100)
- Gestion des égalités
- Persistance des données
- Sécurité admin

## 🔍 Debug

### Logs
- Erreurs PHP : `/var/log/apache2/error.log`
- Erreurs MySQL : `/var/log/mysql/error.log`
- Erreurs applicatives : Console navigateur

### Outils de debug
```php
// Activer le debug PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug PDO
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

## 🎨 Personnalisation

### Thème
Variables CSS dans `assets/css/style.css` :
```css
:root {
    --skull-primary: #1a1a1a;
    --skull-secondary: #d4d4d4;
    --skull-accent: #ff6b6b;
}
```

### Règles de jeu
Modifiables dans :
- `GameController.php` : Nombre de manches
- `game_play.php` : Validation des scores
- `EloCalculator.php` : Paramètres ELO

## 📈 Optimisations futures

### Base de données
- Index sur colonnes fréquemment requêtées
- Partitioning pour l'historique
- Cache Redis pour les classements

### Frontend
- PWA pour utilisation hors ligne
- WebSocket pour parties temps réel
- Lazy loading pour l'historique

### Fonctionnalités
- Export CSV/PDF
- Statistiques avancées
- Système de tournois
- API REST

## 🤝 Contribution

### Guidelines
1. Créer une branche pour chaque feature
2. Respecter les standards de code
3. Tester sur mobile et desktop
4. Documenter les nouvelles fonctionnalités

### Pull Request
1. Description claire des changements
2. Tests manuels effectués
3. Capture d'écran si UI modifiée
4. Pas de breaking changes sans version majeure

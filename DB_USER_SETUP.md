# Configuration de l'utilisateur MySQL pour Skull King League

## Pourquoi créer un utilisateur dédié ?

Utiliser un utilisateur MySQL dédié améliore la sécurité de votre application en :
- Limitant les privilèges aux seules permissions nécessaires
- Évitant l'utilisation d'un compte administrateur
- Facilitant la maintenance et la gestion des accès

## Étapes d'installation

### 1. Exécuter le script de configuration

```bash
cd "/var/www/html/Skull King League"
./setup_db_user.sh
```

### 2. Alternative manuelle

Si vous préférez exécuter manuellement :

```bash
# Se connecter à MySQL en tant que root
mysql -u root -p

# Exécuter le script SQL
source create_db_user.sql

# Quitter MySQL
exit
```

## Détails de l'utilisateur créé

- **Nom d'utilisateur** : `skullking_user`
- **Mot de passe** : `SkullKing_2025!`
- **Hôte** : `localhost`
- **Base de données** : `skull_king_league`

## Privilèges accordés

L'utilisateur a les privilèges suivants sur la base de données `skull_king_league` :
- `SELECT` - Lecture des données
- `INSERT` - Insertion de nouvelles données
- `UPDATE` - Modification des données existantes
- `DELETE` - Suppression des données
- `CREATE` - Création de nouvelles tables
- `ALTER` - Modification de la structure des tables
- `DROP` - Suppression de tables
- `INDEX` - Création/suppression d'index

## Sécurité

⚠️ **Important** : Pour une utilisation en production, vous devriez :

1. Changer le mot de passe par défaut
2. Utiliser un mot de passe plus complexe
3. Envisager de créer un utilisateur avec des privilèges encore plus restreints si possible

## Fichiers modifiés

Les fichiers suivants ont été mis à jour pour utiliser le nouvel utilisateur :
- `config/init_db.php`
- `config/database.php`

## Test de la configuration

Pour tester que tout fonctionne correctement :

```bash
php config/init_db.php
```

Si vous voyez des messages de succès, la configuration est correcte !

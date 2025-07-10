-- Script pour créer un utilisateur dédié à l'application Skull King League
-- Exécuter ce script en tant que root MySQL

-- Créer l'utilisateur pour l'application
CREATE USER IF NOT EXISTS 'skullking_user'@'localhost' IDENTIFIED BY 'SkullKing_2025!';

-- Accorder les privilèges nécessaires uniquement sur la base de données skull_king_league
GRANT SELECT, INSERT, UPDATE, DELETE ON skull_king_league.* TO 'skullking_user'@'localhost';

-- Privilèges spécifiques pour la création de tables (nécessaire pour init_db.php)
GRANT CREATE, ALTER, DROP, INDEX ON skull_king_league.* TO 'skullking_user'@'localhost';

-- Appliquer les changements
FLUSH PRIVILEGES;

-- Vérifier l'utilisateur créé
SELECT User, Host FROM mysql.user WHERE User = 'skullking_user';

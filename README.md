# Skull King League

## 📌 **1. Objectif du projet**

Créer une application web légère et responsive permettant :

* De gérer facilement les parties du jeu **Skull King**.
* De suivre les scores et classements des joueurs.
* D’historiser les parties et les statistiques (avec système de classement ELO).

---

## 📱 **2. Fonctionnalités principales**

### 🎮 Partie Joueur

#### a. Lancer une partie

* Sélectionner de **1 à 6 joueurs** depuis la liste d’utilisateurs enregistrés.
* Générer une **nouvelle partie** de 10 manches.
* Interface responsive mobile pour entrer les points manche par manche.
* Affichage du score total en temps réel.

#### b. Gestion des manches

* Pour chaque manche :

  * Entrée des points par joueur (positifs ou négatifs).
  * Bouton pour passer à la manche suivante.
* À la 10ème manche :

  * Calcul automatique du gagnant.
  * Mise à jour du classement ELO.
  * Sauvegarde de la partie.

#### c. Classements et historique

* Affichage d’un **tableau de classement ELO** (pseudo, nombre de parties, victoires, ELO).
* Historique des parties (date, joueurs, scores, gagnant).
* Fiche joueur : performances, ELO, parties jouées.

---

### 🛠️ Panneau Administrateur

* Connexion admin (identifiant/mot de passe).
* Ajout / modification / suppression d’**utilisateurs** (pseudo uniquement).
* Suppression de parties en base (facultatif).

---

## 🗃️ **3. Structure de la base de données (MySQL)**

### Table `users`

| id | pseudo | elo | parties\_jouées | victoires |
| -- | ------ | --- | --------------- | --------- |

### Table `games`

\| id | date\_partie | gagnant\_id |

### Table `game_players`

\| id | game\_id | user\_id | score\_total |

### Table `rounds`

\| id | game\_id | numero\_manche | player\_id | score |

---

## 💻 **4. Stack technique**

* **Frontend** : HTML + CSS (Bootstrap) + JS (vanilla ou Alpine.js si tu veux du simple réactif).
* **Backend** : PHP (procédural ou MVC léger).
* **Base de données** : MySQL.
* **Hébergement** : Raspberry Pi (Apache ou Nginx recommandé, avec PHP et MySQL/MariaDB).

---

## 🔒 **5. Authentification (admin seulement)**

* Authentification simple (session PHP + mot de passe hashé).
* Page d’administration sécurisée.
* Pas de gestion de compte utilisateur côté joueur (accès libre via mobile).

---

## 🧮 **6. Algorithme ELO simplifié (exemple)**

À chaque partie :

* Le gagnant prend des points en fonction de l’ELO des adversaires.
* Tu peux utiliser une base de 1000 ELO et l’ajuster ainsi :

```text
ELO = ELO + K × (résultat - espérance)
```

Tu peux simplifier le calcul avec :

* K = 32
* Résultat : 1 pour le gagnant, 0 pour les perdants (ou 0.5 si égalité)
* Espérance calculée via formule classique ELO.

---

## 🧪 **7. Bonus / Extensions possibles**

* Mode hors ligne PWA.
* Export CSV des scores / parties.
* Système de notation des joueurs (fun, fair-play).
* Ajout de règles personnalisées Skull King (bonus/malus par manche ?).

---

## 📋 **8. Ébauche de plan de pages (UI)**

1. **Accueil**

   * Bouton : Lancer une partie
   * Bouton : Voir le classement
   * Bouton : Historique des parties

2. **Page de création de partie**

   * Liste déroulante ou boutons avec pseudos (checkbox)
   * Bouton : Démarrer la partie

3. **Page de partie**

   * Manche N/10
   * Formulaire score par joueur
   * Bouton : Valider / Manche suivante

4. **Page de classement**

   * Tableau trié par ELO

5. **Page historique**

   * Liste des parties
   * Détail d’une partie (joueurs, scores)

6. **Admin**

   * Ajout utilisateur
   * Liste utilisateurs
   * Suppression utilisateur (optionnel)
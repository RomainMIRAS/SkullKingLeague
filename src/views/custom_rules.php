<div class="row rules-page">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h4><i class="bi bi-gear-fill"></i> Règles Custom & Extensions</h4>
                <p class="mb-0"><i class="bi bi-lightning-charge"></i> Pour les parties non classées et personnalisées</p>
            </div>
            <div class="card-body">
                <!-- Introduction -->
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Note importante :</strong> Ces règles sont des variantes et extensions du jeu de base. 
                    Elles reprennent les règles officielles mais ajoutent des mécaniques supplémentaires pour plus de fun !
                    Elles ne sont pas applicables aux parties classées et n'affectent pas le système ELO.
                </div>

                <!-- Événements de manche -->
                <h5><i class="bi bi-calendar-event-fill"></i> Événements de Manche</h5>
                <div class="alert alert-info">
                    <i class="bi bi-dice-5-fill"></i>
                    <strong>Nouvelle mécanique :</strong> À chaque manche, un événement aléatoire peut modifier les règles !
                    Consultez le <a href="https://romainmiras.me/Skull%20King%20Event/" target="_blank" class="alert-link">
                        <i class="bi bi-link-45deg"></i> Générateur d'événements Skull King
                    </a> pour découvrir des surprises.
                </div>

                <!-- Variantes de manche -->
                <h5><i class="bi bi-arrow-repeat"></i> Variantes de Structure</h5>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-info rules-card">
                            <div class="card-header bg-info text-white">
                                <h6><i class="bi bi-speedometer"></i> Partie Rapide</h6>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li><strong>5 manches</strong> au lieu de 10</li>
                                    <li>Manches 1, 3, 5, 7, 9 du jeu classique</li>
                                    <li>Idéal pour des sessions courtes</li>
                                    <li>Durée : 15-20 minutes</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success rules-card">
                            <div class="card-header bg-success text-white">
                                <h6><i class="bi bi-hourglass-split"></i> Partie Marathon</h6>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li><strong>15 manches</strong> au lieu de 10</li>
                                    <li>Manches supplémentaires : 11 à 15 cartes</li>
                                    <li>Plus de stratégie et de rebondissements</li>
                                    <li>Durée : 90-120 minutes</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Règles de scoring alternatives -->
                <h5><i class="bi bi-calculator-fill"></i> Variantes de Scoring</h5>
                
                <div class="accordion custom-rules-accordion" id="scoringAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#scoring1">
                                <i class="bi bi-plus-circle-fill text-primary me-2"></i>
                                Scoring Progressif
                            </button>
                        </h2>
                        <div id="scoring1" class="accordion-collapse collapse show" data-bs-parent="#scoringAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Annonce réussie :</h6>
                                        <ul>
                                            <li><strong>Base :</strong> 20 points</li>
                                            <li><strong>Bonus manche :</strong> +5 × numéro de manche</li>
                                            <li><strong>Bonus plis :</strong> +10 × plis remportés</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-light">
                                            <strong>Exemple manche 7 :</strong><br>
                                            Annoncer 3 et faire 3 =<br>
                                            20 + (5×7) + (10×3) = 85 points
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#scoring2">
                                <i class="bi bi-bullseye text-warning me-2"></i>
                                Scoring de Précision
                            </button>
                        </h2>
                        <div id="scoring2" class="accordion-collapse collapse" data-bs-parent="#scoringAccordion">
                            <div class="accordion-body">
                                <ul>
                                    <li><strong>Annonce exacte :</strong> 30 + (15 × plis)</li>
                                    <li><strong>Écart de 1 pli :</strong> 10 points</li>
                                    <li><strong>Écart de 2 plis :</strong> 0 point</li>
                                    <li><strong>Écart de 3+ plis :</strong> -10 × écart</li>
                                </ul>
                                <div class="alert alert-info">
                                    <strong>Avantage :</strong> Récompense la précision sans trop pénaliser les erreurs mineures
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#scoring3">
                                <i class="bi bi-graph-up text-success me-2"></i>
                                Scoring Exponentiel
                            </button>
                        </h2>
                        <div id="scoring3" class="accordion-collapse collapse" data-bs-parent="#scoringAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6>Formule de base :</h6>
                                        <ul>
                                            <li><strong>Annonce réussie :</strong> (plis)² × 10 + 20</li>
                                            <li><strong>Annonce ratée :</strong> -(écart)² × 10</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-warning">
                                            <strong>Exemples :</strong><br>
                                            3 plis réussis = 110 pts<br>
                                            Écart 2 = -40 pts
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-danger">
                                    <strong>Attention :</strong> Pénalise fortement les grosses erreurs !
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Extensions de cartes officielles -->
                <h5 class="mt-4"><i class="bi bi-stars"></i> Cartes d'Extension Officielles</h5>
                
                <div class="alert alert-success">
                    <i class="bi bi-info-circle-fill"></i>
                    <strong>Règles avancées :</strong> Ces cartes proviennent du jeu officiel et peuvent être ajoutées pour plus de complexité.
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-primary special-card">
                            <div class="card-header bg-primary text-white">
                                <h6><i class="bi bi-tsunami"></i> Kraken (1 carte)</h6>
                            </div>
                            <div class="card-body">
                                <ul class="small mb-2">
                                    <li>Remplace une carte Fuite</li>
                                    <li>Annule complètement un pli</li>
                                    <li>Aucun joueur ne remporte le pli</li>
                                    <li>Le même joueur recommence</li>
                                </ul>
                                <div class="alert alert-warning py-1 px-2 small">
                                    <strong>Stratégie :</strong> Parfait pour sauver une annonce compromise !
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-info special-card">
                            <div class="card-header bg-info text-white">
                                <h6><i class="bi bi-water"></i> Baleine Blanche (1 carte)</h6>
                            </div>
                            <div class="card-body">
                                <ul class="small mb-2">
                                    <li>Bat toutes les cartes de couleur</li>
                                    <li>Perd contre tous les personnages</li>
                                    <li>Peut être jouée comme Fuite</li>
                                    <li>Bonus +40 pts si elle gagne un pli</li>
                                </ul>
                                <div class="alert alert-info py-1 px-2 small">
                                    <strong>Légendaire :</strong> Rare mais très puissante !
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning special-card">
                            <div class="card-header bg-warning text-dark">
                                <h6><i class="bi bi-gem"></i> Butin (2 cartes)</h6>
                            </div>
                            <div class="card-body">
                                <ul class="small mb-2">
                                    <li>Se comportent comme des Fuites</li>
                                    <li>+20 points si possédées en fin de manche</li>
                                    <li>-10 points si perdues dans un pli</li>
                                    <li>Peuvent être échangées entre joueurs</li>
                                </ul>
                                <div class="alert alert-warning py-1 px-2 small">
                                    <strong>Dilemme :</strong> Garder ou jouer ?
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cartes custom créatives -->
                <h5 class="mt-4"><i class="bi bi-palette-fill"></i> Cartes Custom Créatives</h5>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-danger special-card">
                            <div class="card-header bg-danger text-white">
                                <h6><i class="bi bi-lightning-charge"></i> Tempête (1 carte)</h6>
                            </div>
                            <div class="card-body">
                                <ul class="small">
                                    <li>Inverse l'ordre de force des couleurs</li>
                                    <li>Vert > Violet > Jaune > Noir</li>
                                    <li>Actif jusqu'à la fin de la manche</li>
                                    <li>Cumulative avec d'autres Tempêtes</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success special-card">
                            <div class="card-header bg-success text-white">
                                <h6><i class="bi bi-magic"></i> Sorcière de Mer (1 carte)</h6>
                            </div>
                            <div class="card-body">
                                <ul class="small">
                                    <li>Copie la dernière carte spéciale jouée</li>
                                    <li>Prend ses caractéristiques</li>
                                    <li>Si aucune : agit comme Fuite</li>
                                    <li>+30 pts si elle imite Skull King</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-dark special-card">
                            <div class="card-header bg-dark text-white">
                                <h6><i class="bi bi-hourglass-split"></i> Sablier Maudit (1 carte)</h6>
                            </div>
                            <div class="card-body">
                                <ul class="small">
                                    <li>Force un nouveau pli immédiatement</li>
                                    <li>Même joueur redistribue 1 carte à tous</li>
                                    <li>Annonces restent identiques</li>
                                    <li>Chaos garanti !</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modes de jeu spéciaux -->
                <h5 class="mt-4"><i class="bi bi-joystick"></i> Modes de Jeu Spéciaux</h5>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6><i class="bi bi-eye-slash-fill"></i> Mode Aveugle</h6>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li>Les annonces se font sans voir ses cartes</li>
                                    <li>Augmente la difficulté et le hasard</li>
                                    <li>Scores × 1.5 pour compenser</li>
                                    <li>Très amusant en groupe détendu</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6><i class="bi bi-people-fill"></i> Mode Équipes</h6>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li>Joueurs en équipes de 2 ou 3</li>
                                    <li>Les points sont partagés dans l'équipe</li>
                                    <li>Communication limitée autorisée</li>
                                    <li>Stratégie collective</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6><i class="bi bi-arrow-left-right"></i> Mode Échange</h6>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li>Après distribution, chaque joueur choisit 1-2 cartes</li>
                                    <li>Les cartes sont échangées avec le voisin</li>
                                    <li>Puis annonces et jeu normal</li>
                                    <li>Ajoute une dimension stratégique</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6><i class="bi bi-hourglass"></i> Mode Chrono</h6>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li>Limite de temps pour chaque action</li>
                                    <li>30 secondes pour l'annonce</li>
                                    <li>15 secondes pour jouer une carte</li>
                                    <li>Pénalité en cas de dépassement</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Règles de tournoi custom -->
                <h5 class="mt-4"><i class="bi bi-trophy"></i> Tournois Custom</h5>
                
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle-fill"></i> Format Swiss</h6>
                    <ul class="mb-0">
                        <li>Nombre de rondes = log₂(nombre de joueurs) + 1</li>
                        <li>Appariements basés sur le score actuel</li>
                        <li>Évite l'élimination directe</li>
                        <li>Système de tie-break par différentiel de points</li>
                    </ul>
                </div>

                <div class="alert alert-success">
                    <h6><i class="bi bi-lightning-fill"></i> Format Élimination Express</h6>
                    <ul class="mb-0">
                        <li>Parties courtes (5 manches max)</li>
                        <li>Élimination du dernier à chaque partie</li>
                        <li>Finale avec les 3 derniers survivants</li>
                        <li>Très dynamique pour grands groupes</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="bi bi-dice-6-fill"></i> Format Événements Aléatoires</h6>
                    <ul class="mb-0">
                        <li>Un événement tiré au sort à chaque manche</li>
                        <li>Utilisez le <a href="https://romainmiras.me/Skull%20King%20Event/" target="_blank" class="alert-link">générateur d'événements</a></li>
                        <li>Parties imprévisibles et mémorables</li>
                        <li>Idéal pour les soirées entre amis</li>
                    </ul>
                </div>

                <!-- Générateur d'événements -->
                <h5 class="mt-4"><i class="bi bi-shuffle"></i> Générateur d'Événements</h5>
                
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6><i class="bi bi-link-45deg"></i> Intégration avec le site officiel</h6>
                        <p>Découvrez des centaines d'événements uniques pour pimenter vos parties !</p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="https://romainmiras.me/Skull%20King%20Event/" target="_blank" class="btn btn-primary">
                                <i class="bi bi-dice-5-fill"></i> Générateur d'Événements
                            </a>
                            <button class="btn btn-outline-secondary" onclick="generateRandomEvent()">
                                <i class="bi bi-arrow-clockwise"></i> Événement Aléatoire
                            </button>
                        </div>
                        
                        <!-- Affichage de l'événement aléatoire -->
                        <div id="randomEvent" class="alert alert-info mt-3" style="display: none;">
                            <h6 id="eventTitle"></h6>
                            <p id="eventDescription" class="mb-0"></p>
                        </div>
                    </div>
                </div>

                <!-- Conseils pour organiser -->
                <h5 class="mt-4"><i class="bi bi-lightbulb-fill"></i> Guide d'Organisation des Parties Custom</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header text-success">
                                <h6><i class="bi bi-check-circle-fill"></i> Bonnes Pratiques</h6>
                            </div>
                            <div class="card-body">
                                <ul class="small">
                                    <li>Commencez par les règles de base</li>
                                    <li>Ajoutez 1-2 cartes d'extension maximum</li>
                                    <li>Testez les événements sur 2-3 manches</li>
                                    <li>Gardez une feuille de référence visible</li>
                                    <li>Adaptez selon l'expérience des joueurs</li>
                                    <li>Prévoyez 15-30 min supplémentaires</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-warning">
                            <div class="card-header text-warning">
                                <h6><i class="bi bi-exclamation-triangle-fill"></i> Pièges à Éviter</h6>
                            </div>
                            <div class="card-body">
                                <ul class="small">
                                    <li>Trop de variantes en même temps</li>
                                    <li>Changer les règles en cours de partie</li>
                                    <li>Événements trop complexes pour débutants</li>
                                    <li>Oublier de noter les modificateurs</li>
                                    <li>Négliger l'équilibrage du jeu</li>
                                    <li>Forcer des règles non appréciées</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section intégration -->
                <h5 class="mt-4"><i class="bi bi-puzzle-fill"></i> Intégration dans vos Parties</h5>
                
                <div class="accordion" id="integrationAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#integration1">
                                <i class="bi bi-1-circle-fill text-success me-2"></i>
                                Niveau Débutant
                            </button>
                        </h2>
                        <div id="integration1" class="accordion-collapse collapse show" data-bs-parent="#integrationAccordion">
                            <div class="accordion-body">
                                <p><strong>Recommandations :</strong></p>
                                <ul>
                                    <li>Règles officielles + 1 variante de scoring maximum</li>
                                    <li>Pas d'événements pour la première partie</li>
                                    <li>Peut-être ajouter la Tigresse pour commencer</li>
                                    <li>Parties courtes (5-7 manches) pour apprendre</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#integration2">
                                <i class="bi bi-2-circle-fill text-warning me-2"></i>
                                Niveau Intermédiaire
                            </button>
                        </h2>
                        <div id="integration2" class="accordion-collapse collapse" data-bs-parent="#integrationAccordion">
                            <div class="accordion-body">
                                <p><strong>Ajouts possibles :</strong></p>
                                <ul>
                                    <li>2-3 cartes d'extension (Kraken, Baleine Blanche)</li>
                                    <li>1 événement par partie (tiré au hasard)</li>
                                    <li>Mode Échange ou Équipes occasionnellement</li>
                                    <li>Scoring de Précision pour plus de challenge</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#integration3">
                                <i class="bi bi-3-circle-fill text-danger me-2"></i>
                                Niveau Expert
                            </button>
                        </h2>
                        <div id="integration3" class="accordion-collapse collapse" data-bs-parent="#integrationAccordion">
                            <div class="accordion-body">
                                <p><strong>Chaos total :</strong></p>
                                <ul>
                                    <li>Toutes les cartes d'extension disponibles</li>
                                    <li>Événement à chaque manche (générateur automatique)</li>
                                    <li>Modes de jeu combinés (Aveugle + Équipes + Chrono)</li>
                                    <li>Tournois Swiss avec événements aléatoires</li>
                                    <li>Parties Marathon (15+ manches)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                    <a href="index.php" class="btn btn-primary">
                        <i class="bi bi-house-fill"></i> Retour à l'accueil
                    </a>
                    <a href="index.php?page=rules" class="btn btn-outline-primary">
                        <i class="bi bi-book-fill"></i> Voir les règles officielles
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript pour améliorer l'expérience utilisateur
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips Bootstrap si nécessaire
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Base d'événements exemple (peut être étendue)
const skullKingEvents = [
    {
        title: "🌊 Mer Agitée",
        description: "Tous les Pirates gagnent +1 en force. Les Sirènes deviennent nerveuses !"
    },
    {
        title: "🌫️ Brouillard Mystique", 
        description: "Les annonces se font secrètement (écrites sur papier). Révélation simultanée."
    },
    {
        title: "💎 Coffre au Trésor",
        description: "Tous les points sont doublés cette manche. Que la fortune vous sourie !"
    },
    {
        title: "🏴‍☠️ Mutinerie",
        description: "Changez de place avec le joueur à votre droite avant de commencer."
    },
    {
        title: "⚡ Tempête Électrique",
        description: "L'ordre de jeu est inversé (sens anti-horaire) pour cette manche."
    },
    {
        title: "🐙 Attaque de Kraken",
        description: "Le premier pli de la manche est automatiquement annulé."
    },
    {
        title: "🧜‍♀️ Chant des Sirènes",
        description: "Toutes les cartes Sirène battent aussi les Pirates cette manche."
    },
    {
        title: "💀 Malédiction Noire",
        description: "Tous les points négatifs sont doublés. Attention aux erreurs !"
    },
    {
        title: "🎯 Précision du Capitaine",
        description: "Annonce exacte = +20 points bonus. Écart = -5 points supplémentaires."
    },
    {
        title: "🌟 Bénédiction Maritime",
        description: "Chaque joueur pioche une carte supplémentaire et en défausse une."
    },
    {
        title: "🔄 Échange Forcé",
        description: "Avant les annonces, échangez 2 cartes avec le joueur en face."
    },
    {
        title: "⏰ Partie Éclair",
        description: "10 secondes maximum pour jouer chaque carte. Stress garanti !"
    },
    {
        title: "🎭 Mascarade",
        description: "Montrez vos cartes aux autres mais cachez les vôtres !"
    },
    {
        title: "🌊 Raz-de-Marée",
        description: "Toutes les cartes 1 deviennent des 14 et vice-versa."
    },
    {
        title: "💰 Prime du Gouverneur",
        description: "Le joueur avec le moins de points gagne +30 points cette manche."
    }
];

function generateRandomEvent() {
    const randomIndex = Math.floor(Math.random() * skullKingEvents.length);
    const event = skullKingEvents[randomIndex];
    
    document.getElementById('eventTitle').innerHTML = '<i class="bi bi-dice-5-fill"></i> ' + event.title;
    document.getElementById('eventDescription').textContent = event.description;
    document.getElementById('randomEvent').style.display = 'block';
    
    // Animation d'apparition
    const eventDiv = document.getElementById('randomEvent');
    eventDiv.style.opacity = '0';
    eventDiv.style.transform = 'translateY(-10px)';
    
    setTimeout(() => {
        eventDiv.style.transition = 'all 0.3s ease';
        eventDiv.style.opacity = '1';
        eventDiv.style.transform = 'translateY(0)';
    }, 100);
}

// Auto-génération d'événement toutes les 30 secondes (optionnel)
let autoEventInterval;

function startAutoEvents() {
    autoEventInterval = setInterval(generateRandomEvent, 30000);
}

function stopAutoEvents() {
    if (autoEventInterval) {
        clearInterval(autoEventInterval);
    }
}

// Fonction pour intégrer avec votre site externe
function loadEventFromSite() {
    // Cette fonction pourrait faire un appel à votre API/site
    // Pour l'instant, on utilise notre base locale
    generateRandomEvent();
}
</script>

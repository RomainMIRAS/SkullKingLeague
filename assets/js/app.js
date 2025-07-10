// JavaScript principal pour Skull King League

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Gestion du formulaire de nouvelle partie
    initNewGameForm();
    
    // Animations d'apparition
    animateElements();
});

// Fonction pour gérer le formulaire de nouvelle partie
function initNewGameForm() {
    const playerCheckboxes = document.querySelectorAll('.player-checkbox');
    const startGameBtn = document.getElementById('start-game-btn');
    const playerCountWarning = document.getElementById('player-count-warning');

    if (playerCheckboxes.length > 0 && startGameBtn) {
        playerCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                validatePlayerSelection();
            });
        });
    }

    function validatePlayerSelection() {
        const checkedBoxes = document.querySelectorAll('.player-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count >= 1 && count <= 6) {
            startGameBtn.disabled = false;
            startGameBtn.classList.remove('btn-secondary');
            startGameBtn.classList.add('btn-success');
            playerCountWarning.style.display = 'none';
        } else {
            startGameBtn.disabled = true;
            startGameBtn.classList.remove('btn-success');
            startGameBtn.classList.add('btn-secondary');
            
            if (count > 0) {
                playerCountWarning.style.display = 'block';
            } else {
                playerCountWarning.style.display = 'none';
            }
        }
        
        // Mettre à jour le texte du bouton
        if (count > 0) {
            startGameBtn.innerHTML = `<i class="bi bi-play-fill"></i> Commencer avec ${count} joueur${count > 1 ? 's' : ''}`;
        } else {
            startGameBtn.innerHTML = '<i class="bi bi-play-fill"></i> Commencer la partie';
        }
    }
}

// Fonction pour animer l'apparition des éléments
function animateElements() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in');
        }, index * 100);
    });
}

// Fonction pour valider les scores dans le jeu
function validateGameScores() {
    const scoreInputs = document.querySelectorAll('.score-input');
    let isValid = true;
    let totalScore = 0;

    scoreInputs.forEach(input => {
        const value = parseInt(input.value);
        
        // Validation de base
        if (isNaN(value) || value < -100 || value > 100) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
            totalScore += value;
        }
    });

    // Afficher un résumé des scores
    const summaryElement = document.getElementById('score-summary');
    if (summaryElement) {
        summaryElement.innerHTML = `Score total de la manche: ${totalScore}`;
    }

    return isValid;
}

// Fonction pour auto-sauvegarder les scores (localStorage)
function autoSaveScores() {
    const gameId = document.querySelector('input[name="game_id"]')?.value;
    const roundNumber = document.querySelector('input[name="round_number"]')?.value;
    
    if (gameId && roundNumber) {
        const scores = {};
        const scoreInputs = document.querySelectorAll('.score-input');
        
        scoreInputs.forEach(input => {
            const playerId = input.name.match(/\[(\d+)\]/)?.[1];
            if (playerId) {
                scores[playerId] = input.value;
            }
        });

        const saveData = {
            gameId: gameId,
            roundNumber: roundNumber,
            scores: scores,
            timestamp: Date.now()
        };

        localStorage.setItem(`skull_king_autosave_${gameId}_${roundNumber}`, JSON.stringify(saveData));
    }
}

// Fonction pour restaurer les scores sauvegardés
function restoreScores() {
    const gameId = document.querySelector('input[name="game_id"]')?.value;
    const roundNumber = document.querySelector('input[name="round_number"]')?.value;
    
    if (gameId && roundNumber) {
        const saveKey = `skull_king_autosave_${gameId}_${roundNumber}`;
        const savedData = localStorage.getItem(saveKey);
        
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                
                // Vérifier que les données ne sont pas trop anciennes (1 heure)
                if (Date.now() - data.timestamp < 3600000) {
                    for (const playerId in data.scores) {
                        const input = document.querySelector(`input[name="scores[${playerId}]"]`);
                        if (input) {
                            input.value = data.scores[playerId];
                        }
                    }
                    
                    // Afficher une notification
                    showNotification('Scores restaurés automatiquement', 'info');
                }
            } catch (e) {
                console.error('Erreur lors de la restauration des scores:', e);
            }
        }
    }
}

// Fonction pour afficher des notifications
function showNotification(message, type = 'info') {
    // Créer l'élément de notification
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    
    notification.innerHTML = `
        <i class="bi bi-info-circle-fill"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Fonction pour confirmer la fin de partie
function confirmEndGame() {
    return confirm('Êtes-vous sûr de vouloir terminer cette partie ? Cette action ne peut pas être annulée.');
}

// Fonctions utilitaires pour les statistiques
function calculateWinRate(victories, totalGames) {
    return totalGames > 0 ? Math.round((victories / totalGames) * 100) : 0;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Fonction pour exporter les données (future feature)
function exportGameData(gameId) {
    // Cette fonction pourra être utilisée pour exporter les données en CSV
    showNotification('Fonctionnalité d\'export en cours de développement', 'warning');
}

// Gestion des raccourcis clavier
document.addEventListener('keydown', function(e) {
    // Echap pour fermer les modals
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    }
    
    // Ctrl+S pour sauvegarder (dans les formulaires de jeu)
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        const gameForm = document.querySelector('form[action*="add_round"]');
        if (gameForm) {
            autoSaveScores();
            showNotification('Scores sauvegardés localement', 'success');
        }
    }
});

// Auto-sauvegarde des scores toutes les 30 secondes
setInterval(function() {
    if (document.querySelector('.score-input')) {
        autoSaveScores();
    }
}, 30000);

// Restaurer les scores au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(restoreScores, 100);
});

// Fonction pour mettre à jour les statistiques en temps réel
function updateLiveStats() {
    const scoreInputs = document.querySelectorAll('.score-input');
    if (scoreInputs.length === 0) return;
    
    let totalScore = 0;
    let validScores = 0;
    
    scoreInputs.forEach(input => {
        const value = parseInt(input.value) || 0;
        totalScore += value;
        if (!isNaN(parseInt(input.value))) {
            validScores++;
        }
    });
    
    // Mettre à jour l'affichage en temps réel
    const statsElement = document.getElementById('live-stats');
    if (statsElement) {
        const averageScore = validScores > 0 ? Math.round(totalScore / validScores) : 0;
        statsElement.innerHTML = `
            <small class="text-muted">
                Total: ${totalScore} | Moyenne: ${averageScore}
            </small>
        `;
    }
}

// Attacher les événements aux inputs de score
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('score-input')) {
        updateLiveStats();
    }
});

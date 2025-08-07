<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4><i class="bi bi-controller"></i> Partie en cours - Manche <?php echo $current_round; ?>/10</h4>
                    <div>
                        <span class="badge bg-warning">
                            <i class="bi bi-trophy"></i> Classée
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Erreur :</strong> <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($current_round <= 10): ?>
                
                <!-- Scores actuels -->
                <div class="mb-4">
                    <h5>Scores actuels :</h5>
                    <div class="row">
                        <?php
                        // Récupérer d'abord toutes les données des manches
                        $rounds = $game->getRounds($game_id);
                        $rounds_data = [];
                        $player_totals = [];
                        
                        // Initialiser les totaux à zéro pour tous les joueurs
                        $players->execute();
                        while ($player = $players->fetch(PDO::FETCH_ASSOC)) {
                            $player_totals[$player['user_id']] = 0;
                        }
                        
                        // Calculer les totaux en additionnant les scores de chaque manche
                        while ($round = $rounds->fetch(PDO::FETCH_ASSOC)) {
                            $rounds_data[$round['numero_manche']][$round['player_id']] = [
                                'pseudo' => $round['pseudo'],
                                'score' => $round['score']
                            ];
                            
                            // Ajouter le score de cette manche au total du joueur
                            $player_totals[$round['player_id']] += $round['score'];
                        }
                        
                        // Afficher les scores dans l'ordre des joueurs (pas par score)
                        $players->execute();
                        while ($player = $players->fetch(PDO::FETCH_ASSOC)): 
                            $is_starting_player = $starting_player && $player['user_id'] == $starting_player['user_id'];
                        ?>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <div class="card <?php echo $is_starting_player ? 'border-warning bg-warning bg-opacity-10' : 'border-info'; ?>">
                                <div class="card-body text-center py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge <?php echo $is_starting_player ? 'bg-warning text-dark' : 'bg-secondary'; ?> rounded-pill"><?php echo $player['player_order']; ?></span>
                                        <h6 class="mb-1 flex-grow-1 <?php echo $is_starting_player ? 'fw-bold' : ''; ?>"><?php echo htmlspecialchars($player['pseudo']); ?></h6>
                                        <span class="badge bg-info fs-6"><?php echo $player_totals[$player['user_id']] ?? 0; ?> pts</span>
                                    </div>
                                    <?php if ($is_starting_player): ?>
                                    <small class="text-warning fw-bold">
                                        <i class="bi bi-play-fill"></i> Commence cette manche
                                    </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Formulaire pour la manche -->
                <form action="index.php?page=game&action=add_round" method="POST" onsubmit="return validateScores()">
                    <input type="hidden" name="game_id" value="<?php echo $game_id; ?>">
                    <input type="hidden" name="round_number" value="<?php echo $current_round; ?>">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Manche <?php echo $current_round; ?> :</h5>
                        <div class="badge bg-info">
                            <i class="bi bi-info-circle"></i> Multiples de 10 uniquement
                        </div>
                    </div>
                    
                    <div class="alert alert-light border">
                        <div class="row">
                            <div class="col-md-8">
                                <small class="text-muted">
                                    <i class="bi bi-lightbulb"></i> 
                                    <strong>Règle :</strong> Entrez les points gagnés ou perdus pour cette manche (pas les points cumulés). 
                                    Les scores doivent être des multiples de 10 (exemples : 0, 10, 20, -10, -30, 50...).
                                </small>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">
                                    <i class="bi bi-arrow-clockwise"></i> 
                                    <strong>Ordre :</strong> Chaque manche, c'est au tour du joueur suivant de commencer.
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <?php 
                        $players->execute(); // Reset pour parcourir à nouveau
                        while ($player = $players->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="col-md-6 mb-3">
                            <label for="score_<?php echo $player['user_id']; ?>" class="form-label">
                                <div class="d-flex align-items-center gap-2">
                                    <span>Points pour <?php echo htmlspecialchars($player['pseudo']); ?></span>
                                </div>
                            </label>
                            <input type="number" 
                                   class="form-control form-control-lg score-input" 
                                   id="score_<?php echo $player['user_id']; ?>"
                                   name="scores[<?php echo $player['user_id']; ?>]" 
                                   step="10"
                                   required>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <?php if ($current_round < 10): ?>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-arrow-right-circle"></i> Manche suivante
                        </button>
                        <?php else: ?>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-flag-fill"></i> Terminer la partie
                        </button>
                        <?php endif; ?>
                    </div>
                </form>

                <?php else: ?>
                <div class="alert alert-info text-center">
                    <h5>Partie terminée !</h5>
                    <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Historique des manches -->
        <?php if ($current_round > 1): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="bi bi-list-ol"></i> Historique des manches</h5>
            </div>
            <div class="card-body">
                <?php
                // Utilisation des données déjà chargées dans la section des scores actuels
                ?>
                
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Manche</th>
                                <?php 
                                $players->execute();
                                while ($player = $players->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                <th><?php echo htmlspecialchars($player['pseudo']); ?></th>
                                <?php endwhile; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = $current_round - 1; $i >= 1; $i--): ?>
                            <tr class="edit-round-row" style="cursor:pointer;" 
                                data-round="<?php echo $i; ?>"
                                data-scores='<?php
                                    $scores = [];
                                    $players->execute();
                                    while ($player = $players->fetch(PDO::FETCH_ASSOC)) {
                                        $scores[$player['user_id']] = isset($rounds_data[$i][$player['user_id']]['score'])
                                            ? $rounds_data[$i][$player['user_id']]['score']
                                            : "";
                                    }
                                    echo json_encode($scores);
                                ?>'>
                                <td><strong>Manche <?php echo $i; ?></strong></td>
                                <?php 
                                $players->execute();
                                while ($player = $players->fetch(PDO::FETCH_ASSOC)): 
                                    $score = $rounds_data[$i][$player['user_id']]['score'] ?? 0;
                                ?>
                                <td>
                                    <span class="badge <?php echo $score >= 0 ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $score; ?>
                                    </span>
                                </td>
                                <?php endwhile; ?>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="editRoundModal" tabindex="-1" aria-labelledby="editRoundModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="editRoundForm" method="POST" action="index.php?page=game&action=edit_round" onsubmit="return validateEditScores()">
      <div class="modal-header">
        <h5 class="modal-title" id="editRoundModalLabel">Modifier la manche <span id="editRoundNumber"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="game_id" value="<?php echo $game_id; ?>">
        <input type="hidden" name="round_number" id="edit_round_number" value="">
        <div id="editScoresInputs"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<script>
function validateScores() {
    const inputs = document.querySelectorAll('.score-input');
    let valid = true;
    let totalScore = 0;
    
    inputs.forEach(input => {
        const value = parseInt(input.value);
        
        // Vérifier que c'est un nombre valide
        if (isNaN(value)) {
            valid = false;
            input.classList.add('is-invalid');
            return;
        }
        
        // Vérifier que c'est un multiple de 10
        if (value % 10 !== 0) {
            valid = false;
            input.classList.add('is-invalid');
            input.setAttribute('title', 'Doit être un multiple de 10');
        } else {
            input.classList.remove('is-invalid');
            input.removeAttribute('title');
            totalScore += value;
        }
    });
    
    if (!valid) {
        alert('Veuillez entrer des scores valides (multiples de 10 uniquement).\nExemples: 0, 10, 20, -10, -30, 50...');
        return false;
    }
    
    return true;
}

// Auto-focus sur le premier input (joueur qui commence)
document.addEventListener('DOMContentLoaded', function() {
    // Donner le focus au joueur qui commence (celui avec la bordure warning)
    const startingInput = document.querySelector('.score-input.border-warning');
    const firstInput = document.querySelector('.score-input');
    
    if (startingInput) {
        startingInput.focus();
    } else if (firstInput) {
        firstInput.focus();
    }
    
    // Validation en temps réel pour les multiples de 10
    const scoreInputs = document.querySelectorAll('.score-input');
    scoreInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = parseInt(this.value);
            
            if (isNaN(value)) {
                this.classList.remove('is-valid', 'is-invalid');
                return;
            }
            
            if (value % 10 === 0) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                this.setAttribute('title', 'Score valide');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
                this.setAttribute('title', 'Doit être un multiple de 10');
            }
            
            updateLiveTotal();
        });
        
        // Correction automatique lors de la perte de focus
        input.addEventListener('blur', function() {
            const value = parseInt(this.value);
            if (!isNaN(value) && value % 10 !== 0) {
                // Arrondir au multiple de 10 le plus proche
                const rounded = Math.round(value / 10) * 10;
                this.value = rounded;
                this.dispatchEvent(new Event('input'));
            }
        });
    });

    // Edit round modal logic (row click)
    const editModal = new bootstrap.Modal(document.getElementById('editRoundModal'));
    document.querySelectorAll('.edit-round-row').forEach(row => {
        row.addEventListener('click', function() {
            const roundNumber = this.getAttribute('data-round');
            const scores = JSON.parse(this.getAttribute('data-scores'));
            document.getElementById('editRoundNumber').textContent = roundNumber;
            document.getElementById('edit_round_number').value = roundNumber;
            // Build inputs for each player
            let html = '';
            <?php $players->execute(); while ($player = $players->fetch(PDO::FETCH_ASSOC)): ?>
                html += `<div class=\"mb-3\">
                    <label for=\"edit_score_<?php echo $player['user_id']; ?>\" class=\"form-label\"><?php echo htmlspecialchars($player['pseudo']); ?></label>
                    <input type=\"number\" class=\"form-control edit-score-input\" id=\"edit_score_<?php echo $player['user_id']; ?>\" name=\"scores[<?php echo $player['user_id']; ?>]\" value=\"${scores['<?php echo $player['user_id']; ?>'] ?? 0}\" step=\"10\" required>
                </div>`;
            <?php endwhile; ?>
            document.getElementById('editScoresInputs').innerHTML = html;
            editModal.show();
        });
    });
});

function validateEditScores() {
    const inputs = document.querySelectorAll('.edit-score-input');
    let valid = true;
    inputs.forEach(input => {
        const value = parseInt(input.value);
        if (isNaN(value) || value % 10 !== 0) {
            valid = false;
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });
    if (!valid) {
        alert('Veuillez entrer des scores valides (multiples de 10 uniquement).');
        return false;
    }
    return true;
}

// Fonction pour afficher le total en temps réel
function updateLiveTotal() {
    const inputs = document.querySelectorAll('.score-input');
    let total = 0;
    let validInputs = 0;
    
    inputs.forEach(input => {
        const value = parseInt(input.value);
        if (!isNaN(value) && value % 10 === 0) {
            total += value;
            validInputs++;
        }
    });
    
    // Afficher le total si au moins un score valide
    if (validInputs > 0) {
        let totalDisplay = document.getElementById('live-total');
        if (!totalDisplay) {
            totalDisplay = document.createElement('div');
            totalDisplay.id = 'live-total';
            totalDisplay.className = 'alert alert-info mt-2';
            const form = document.querySelector('form');
            form.insertBefore(totalDisplay, form.querySelector('.d-grid'));
        }
        
        totalDisplay.innerHTML = `
            <i class="bi bi-calculator"></i> 
            <strong>Total de la manche: ${total} points</strong>
            <small class="d-block">Scores valides: ${validInputs}/${inputs.length}</small>
        `;
        
        // Changer la couleur selon le total
        totalDisplay.className = total === 0 ? 'alert alert-info mt-2' : 
                                 total > 0 ? 'alert alert-success mt-2' : 
                                 'alert alert-warning mt-2';
    }
}
</script>

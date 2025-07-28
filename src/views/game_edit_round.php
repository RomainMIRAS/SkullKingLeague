<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h4><i class="bi bi-pencil-square"></i> Modifier la Manche <?php echo $round_number; ?></h4>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Erreur :</strong> <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Attention :</strong> Modifier une manche précédente recalculera automatiquement tous les scores totaux.
                </div>

                <!-- Affichage des scores actuels -->
                <div class="mb-4">
                    <h5>Scores actuels de la manche <?php echo $round_number; ?> :</h5>
                    <div class="row">
                        <?php foreach ($round_scores as $score_data): ?>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <div class="card border-info">
                                <div class="card-body text-center py-2">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($score_data['pseudo']); ?></h6>
                                    <span class="badge bg-info fs-6"><?php echo $score_data['score']; ?> pts</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Formulaire pour modifier la manche -->
                <form action="index.php?page=game&action=update_round" method="POST" onsubmit="return validateScores()">
                    <input type="hidden" name="game_id" value="<?php echo $game_id; ?>">
                    <input type="hidden" name="round_number" value="<?php echo $round_number; ?>">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Nouveaux scores :</h5>
                        <div class="badge bg-info">
                            <i class="bi bi-info-circle"></i> Multiples de 10 uniquement
                        </div>
                    </div>
                    
                    <div class="alert alert-light border">
                        <small class="text-muted">
                            <i class="bi bi-lightbulb"></i> 
                            <strong>Règle :</strong> Entrez les points gagnés ou perdus pour cette manche (pas les points cumulés). 
                            Les scores doivent être des multiples de 10 (exemples : 10, 20, -10, -30, 50...) et ne peuvent pas être zéro.
                        </small>
                    </div>
                    
                    <div class="row">
                        <?php 
                        // Créer un tableau associatif pour un accès facile aux scores actuels
                        $current_scores = [];
                        foreach ($round_scores as $score_data) {
                            $current_scores[$score_data['player_id']] = $score_data['score'];
                        }
                        
                        $players->execute(); // Reset pour parcourir à nouveau
                        while ($player = $players->fetch(PDO::FETCH_ASSOC)): 
                            $current_score = $current_scores[$player['user_id']] ?? 0;
                        ?>
                        <div class="col-md-6 mb-3">
                            <label for="score_<?php echo $player['user_id']; ?>" class="form-label">
                                Points pour <?php echo htmlspecialchars($player['pseudo']); ?>
                            </label>
                            <input type="number" 
                                   class="form-control form-control-lg score-input" 
                                   id="score_<?php echo $player['user_id']; ?>"
                                   name="scores[<?php echo $player['user_id']; ?>]" 
                                   value="<?php echo $current_score; ?>"
                                   step="10"
                                   required>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php?page=game&action=play&id=<?php echo $game_id; ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Sauvegarder les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function validateScores() {
    const inputs = document.querySelectorAll('.score-input');
    let valid = true;
    
    inputs.forEach(input => {
        const value = parseInt(input.value);
        
        // Vérifier que c'est un nombre valide
        if (isNaN(value)) {
            valid = false;
            input.classList.add('is-invalid');
            return;
        }
        
        // Vérifier que c'est différent de zéro
        if (value === 0) {
            valid = false;
            input.classList.add('is-invalid');
            input.setAttribute('title', 'Le score ne peut pas être zéro');
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
        }
    });
    
    if (!valid) {
        alert('Veuillez entrer des scores valides (multiples de 10 uniquement, zéro non autorisé).\nExemples: 10, 20, -10, -30, 50...');
        return false;
    }
    
    return true;
}

// Validation en temps réel pour les multiples de 10
document.addEventListener('DOMContentLoaded', function() {
    const scoreInputs = document.querySelectorAll('.score-input');
    scoreInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = parseInt(this.value);
            
            if (isNaN(value)) {
                this.classList.remove('is-valid', 'is-invalid');
                return;
            }
            
            if (value === 0) {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
                this.setAttribute('title', 'Le score ne peut pas être zéro');
            } else if (value % 10 === 0) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                this.setAttribute('title', 'Score valide');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
                this.setAttribute('title', 'Doit être un multiple de 10');
            }
        });
        
        // Correction automatique lors de la perte de focus
        input.addEventListener('blur', function() {
            const value = parseInt(this.value);
            if (!isNaN(value)) {
                if (value === 0) {
                    // Si c'est zéro, remplacer par 10 pour éviter le 0
                    this.value = 10;
                    this.dispatchEvent(new Event('input'));
                } else if (value % 10 !== 0) {
                    // Arrondir au multiple de 10 le plus proche
                    const rounded = Math.round(value / 10) * 10;
                    // Si l'arrondi donne 0, choisir 10 ou -10 selon le signe original
                    this.value = rounded === 0 ? (value > 0 ? 10 : -10) : rounded;
                    this.dispatchEvent(new Event('input'));
                }
            }
        });
    });
});
</script>
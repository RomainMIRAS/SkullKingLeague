<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-success text-white text-center">
                <h3><i class="bi bi-trophy-fill"></i> Partie Terminée !</h3>
            </div>
            <div class="card-body">
                <?php if ($game_data && $players): ?>
                
                <!-- Podium -->
                <div class="text-center mb-4">
                    <h4>🏆 Félicitations <?php echo htmlspecialchars($game_data['gagnant_pseudo']); ?> !</h4>
                    <p class="lead">Partie terminée le <?php echo date('d/m/Y à H:i', strtotime($game_data['date_partie'])); ?></p>
                    <?php if ($game_data['is_ranked']): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-trophy"></i> <strong>Partie classée</strong> - Les ELO ont été mis à jour
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-heart"></i> <strong>Partie amicale</strong> - Aucun impact sur les classements ELO
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Classement final -->
                <h5>Classement final :</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Position</th>
                                <th>Joueur</th>
                                <th>Score Final</th>
                                <?php if ($game_data['is_ranked']): ?>
                                <th>Évolution ELO</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $position = 1;
                            $players_array = [];
                            while ($player = $players->fetch(PDO::FETCH_ASSOC)) {
                                $players_array[] = $player;
                            }
                            
                            // Trier par score décroissant
                            usort($players_array, function($a, $b) {
                                return $b['score_total'] - $a['score_total'];
                            });
                            
                            // Gérer les égalités pour l'affichage
                            $last_score = null;
                            $last_position = 0;
                            $display_positions = [];
                            
                            foreach ($players_array as $index => $player) {
                                $score = $player['score_total'];
                                
                                if ($last_score !== null && $score == $last_score) {
                                    // Égalité avec le joueur précédent, même position
                                    $display_positions[$index] = $last_position;
                                } else {
                                    // Nouveau score
                                    $last_position = $position;
                                    $display_positions[$index] = $position;
                                }
                                
                                $last_score = $score;
                                $position++;
                            }
                            
                            $position = 1;
                            foreach ($players_array as $index => $player): 
                                // Récupérer les données ELO pour ce joueur
                                $user_data = $user->getById($player['user_id']);
                                $display_position = $display_positions[$index];
                                
                                // Récupérer le rang réel depuis l'historique ELO s'il existe
                                $elo_data = isset($elo_changes[$player['user_id']]) ? $elo_changes[$player['user_id']] : null;
                                $rank_info = $elo_data && isset($elo_data['rank']) ? " (rang " . $elo_data['rank'] . ")" : "";
                            ?>
                            <tr class="<?php echo $display_position == 1 ? 'table-warning' : ''; ?>">
                                <td>
                                    <?php if ($display_position == 1): ?>
                                        <i class="bi bi-trophy-fill text-warning"></i> 1er<?php echo $rank_info; ?>
                                    <?php elseif ($display_position == 2): ?>
                                        <i class="bi bi-award-fill text-secondary"></i> 2ème<?php echo $rank_info; ?>
                                    <?php elseif ($display_position == 3): ?>
                                        <i class="bi bi-award-fill" style="color: #CD7F32;"></i> 3ème<?php echo $rank_info; ?>
                                    <?php else: ?>
                                        <?php echo $display_position; ?>ème<?php echo $rank_info; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($player['pseudo']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary fs-6"><?php echo $player['score_total']; ?> points</span>
                                </td>
                                <?php if ($game_data['is_ranked']): ?>
                                <td>
                                    <?php
                                    // Ranked game - show ELO change
                                    $elo_data = isset($elo_changes[$player['user_id']]) ? $elo_changes[$player['user_id']] : null;
                                    $old_elo = $elo_data ? $elo_data['old_elo'] : $user_data['elo'];
                                    $new_elo = $elo_data ? $elo_data['new_elo'] : $user_data['elo'];
                                    $change = $elo_data ? $elo_data['elo_change'] : 0;
                                    
                                    // Définir la couleur et l'icône en fonction du changement d'ELO
                                    $badge_class = "bg-info";
                                    $icon_class = "bi-dash";
                                    $text_class = "text-secondary";
                                    
                                    if ($change > 0) {
                                        $badge_class = "bg-success";
                                        $icon_class = "bi-arrow-up";
                                        $text_class = "text-success";
                                    } elseif ($change < 0) {
                                        $badge_class = "bg-danger";
                                        $icon_class = "bi-arrow-down";
                                        $text_class = "text-danger";
                                    }
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>"><?php echo $new_elo; ?> ELO</span>
                                        <i class="bi <?php echo $icon_class; ?> <?php echo $text_class; ?>"></i>
                                        <small class="<?php echo $text_class; ?>">
                                            <?php echo $change > 0 ? '+' . $change : $change; ?>
                                        </small>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php 
                            $position++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Actions -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                    <a href="index.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-house-fill"></i> Retour à l'accueil
                    </a>
                    <a href="index.php?page=ranking" class="btn btn-warning btn-lg">
                        <i class="bi bi-trophy-fill"></i> Voir le classement
                    </a>
                    <button class="btn btn-success btn-lg" 
                            onclick="showGameDetails(<?php echo $game_data['id']; ?>)" 
                            data-bs-toggle="modal" 
                            data-bs-target="#gameDetailsModal">
                        <i class="bi bi-eye"></i> Détails de la partie
                    </button>
                </div>

                <?php else: ?>
                <div class="alert alert-danger">
                    Erreur lors du chargement des données de la partie.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Détails de la partie -->
<div class="modal fade" id="gameDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la partie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="gameDetailsContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
function showGameDetails(gameId) {
    const content = document.getElementById('gameDetailsContent');
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    `;
    
    fetch(`index.php?page=game&action=details&id=${gameId}`)
        .then(response => response.text())
        .then(data => {
            content.innerHTML = data;
        })
        .catch(error => {
            content.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des détails.</div>';
        });
}
</script>

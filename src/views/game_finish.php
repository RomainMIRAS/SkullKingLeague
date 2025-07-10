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
                                <th>Évolution ELO</th>
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
                            
                            foreach ($players_array as $player): 
                                // Récupérer l'ELO actuel pour calculer l'évolution
                                $user_data = $user->getById($player['user_id']);
                                $elo_evolution = $user_data['elo'] - 1000; // Approximation simplifiée
                            ?>
                            <tr class="<?php echo $position == 1 ? 'table-warning' : ''; ?>">
                                <td>
                                    <?php if ($position == 1): ?>
                                        <i class="bi bi-trophy-fill text-warning"></i> 1er
                                    <?php elseif ($position == 2): ?>
                                        <i class="bi bi-award-fill text-secondary"></i> 2ème
                                    <?php elseif ($position == 3): ?>
                                        <i class="bi bi-award-fill" style="color: #CD7F32;"></i> 3ème
                                    <?php else: ?>
                                        <?php echo $position; ?>ème
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($player['pseudo']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary fs-6"><?php echo $player['score_total']; ?> points</span>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo $user_data['elo']; ?> ELO</span>
                                    <?php if ($position == 1): ?>
                                        <i class="bi bi-arrow-up text-success"></i>
                                    <?php else: ?>
                                        <i class="bi bi-arrow-down text-danger"></i>
                                    <?php endif; ?>
                                </td>
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

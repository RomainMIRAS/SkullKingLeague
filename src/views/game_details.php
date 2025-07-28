<?php if ($game_data): ?>
<div class="card">
    <div class="card-header">
        <h5>
            <i class="bi bi-calendar3"></i> 
            Partie du <?php echo date('d/m/Y à H:i', strtotime($game_data['date_partie'])); ?>
            <?php if (isset($game_data['is_ranked'])): ?>
                <?php if ($game_data['is_ranked']): ?>
                    <span class="badge bg-warning text-dark ms-2">
                        <i class="bi bi-trophy"></i> Classée
                    </span>
                <?php else: ?>
                    <span class="badge bg-secondary ms-2">
                        <i class="bi bi-heart"></i> Amicale
                    </span>
                <?php endif; ?>
            <?php endif; ?>
        </h5>
    </div>
    <div class="card-body">
        <!-- Gagnant -->
        <div class="alert alert-success text-center">
            <h5><i class="bi bi-trophy-fill"></i> Gagnant : <?php echo htmlspecialchars($game_data['gagnant_pseudo']); ?></h5>
        </div>

        <!-- Scores finaux -->
        <h6>Scores finaux :</h6>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Joueur</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Récupérer tous les scores des manches
                    $rounds_data = [];
                    $player_totals = [];
                    $starting_players = []; // Pour stocker qui commence chaque manche
                    
                    // Initialiser les totaux à zéro et récupérer les données des manches
                    while ($round = $rounds->fetch(PDO::FETCH_ASSOC)) {
                        $rounds_data[$round['numero_manche']][$round['player_id']] = [
                            'pseudo' => $round['pseudo'],
                            'score' => $round['score']
                        ];
                        
                        // Stocker qui commence chaque manche
                        if ($round['starting_player_id'] && !isset($starting_players[$round['numero_manche']])) {
                            $starting_players[$round['numero_manche']] = $round['starting_player_id'];
                        }
                        
                        // Calculer le total pour chaque joueur
                        if (!isset($player_totals[$round['player_id']])) {
                            $player_totals[$round['player_id']] = 0;
                        }
                        $player_totals[$round['player_id']] += $round['score'];
                    }
                    
                    $position = 1;
                    $players_array = [];
                    $players_with_order = []; // Pour maintenir l'ordre original
                    
                    while ($player = $players->fetch(PDO::FETCH_ASSOC)) {
                        // Remplacer le score_total par le total calculé
                        $player['score_total'] = $player_totals[$player['user_id']] ?? 0;
                        $players_array[] = $player;
                        
                        // Conserver l'ordre original des joueurs aussi
                        $players_with_order[$player['player_order']] = $player;
                    }
                    
                    // Trier l'ordre original des joueurs par player_order
                    ksort($players_with_order);
                    
                    // Trier par score décroissant
                    usort($players_array, function($a, $b) {
                        return $b['score_total'] - $a['score_total'];
                    });
                    
                    foreach ($players_array as $player): 
                    ?>
                    <tr class="<?php echo $position == 1 ? 'table-warning' : ''; ?>">
                        <td>
                            <?php if ($position == 1): ?>
                                <i class="bi bi-trophy-fill text-warning"></i>
                            <?php else: ?>
                                <?php echo $position; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($player['pseudo']); ?></td>
                        <td>
                            <span class="badge <?php echo $player['score_total'] >= 0 ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo $player['score_total']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php 
                    $position++;
                    endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Détail par manche -->
        <?php
        // Utilisation des données déjà chargées plus haut
        if (!empty($rounds_data)):
        ?>
        <h6 class="mt-4">Détail par manche :</h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Manche</th>
                        <th>Commence</th>
                        <?php foreach ($players_with_order as $player): ?>
                        <th>
                            <span class="badge bg-secondary rounded-pill me-1"><?php echo $player['player_order']; ?></span>
                            <?php echo htmlspecialchars($player['pseudo']); ?>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                    <?php if (isset($rounds_data[$i])): ?>
                    <tr>
                        <td><strong><?php echo $i; ?></strong></td>
                        <td>
                            <?php 
                            $starting_player_id = $starting_players[$i] ?? null;
                            if ($starting_player_id) {
                                // Trouver le pseudo du joueur qui commence
                                foreach ($players_with_order as $player) {
                                    if ($player['user_id'] == $starting_player_id) {
                                        echo '<span class="badge bg-warning">' . htmlspecialchars($player['pseudo']) . '</span>';
                                        break;
                                    }
                                }
                            } else {
                                echo '<span class="text-muted">-</span>';
                            }
                            ?>
                        </td>
                        <?php foreach ($players_with_order as $player): ?>
                        <td>
                            <?php 
                            $score = $rounds_data[$i][$player['user_id']]['score'] ?? 0;
                            $is_starting = isset($starting_players[$i]) && $starting_players[$i] == $player['user_id'];
                            ?>
                            <span class="badge <?php echo $score >= 0 ? 'bg-success' : 'bg-danger'; ?> <?php echo $is_starting ? 'border border-warning' : ''; ?>">
                                <?php echo $score; ?>
                            </span>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endif; ?>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <!-- Section ELO History -->
        <?php if (isset($elo_history) && !empty($elo_history)): ?>
        <hr>
        <h6><i class="bi bi-graph-up text-info"></i> Changements ELO :</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-info">
                    <tr>
                        <th>Rang</th>
                        <th>Joueur</th>
                        <th>ELO Avant</th>
                        <th>ELO Après</th>
                        <th>Changement</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($elo_history as $history): ?>
                    <tr>
                        <td>
                            <?php if ($history['rank'] == 1): ?>
                                <i class="bi bi-trophy-fill text-warning"></i> <?php echo $history['rank']; ?>
                            <?php else: ?>
                                <?php echo $history['rank']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($history['pseudo']); ?></strong>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?php echo $history['old_elo']; ?></span>
                        </td>
                        <td>
                            <span class="badge bg-primary"><?php echo $history['new_elo']; ?></span>
                        </td>
                        <td>
                            <?php if ($history['elo_change'] > 0): ?>
                                <span class="badge bg-success">+<?php echo $history['elo_change']; ?></span>
                            <?php elseif ($history['elo_change'] < 0): ?>
                                <span class="badge bg-danger"><?php echo $history['elo_change']; ?></span>
                            <?php else: ?>
                                <span class="badge bg-warning">0</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php elseif (isset($game_data['is_ranked']) && !$game_data['is_ranked']): ?>
        <hr>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Cette partie amicale n'affecte pas les classements ELO.
        </div>
        <?php endif; ?>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">
    Partie non trouvée.
</div>
<?php endif; ?>

<?php if ($game_data): ?>
<div class="card">
    <div class="card-header">
        <h5>
            <i class="bi bi-calendar3"></i> 
            Partie du <?php echo date('d/m/Y à H:i', strtotime($game_data['date_partie'])); ?>
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
        $rounds_data = [];
        while ($round = $rounds->fetch(PDO::FETCH_ASSOC)) {
            $rounds_data[$round['numero_manche']][$round['player_id']] = [
                'pseudo' => $round['pseudo'],
                'score' => $round['score']
            ];
        }
        
        if (!empty($rounds_data)):
        ?>
        <h6 class="mt-4">Détail par manche :</h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Manche</th>
                        <?php foreach ($players_array as $player): ?>
                        <th><?php echo htmlspecialchars($player['pseudo']); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                    <?php if (isset($rounds_data[$i])): ?>
                    <tr>
                        <td><strong><?php echo $i; ?></strong></td>
                        <?php foreach ($players_array as $player): ?>
                        <td>
                            <?php 
                            $score = $rounds_data[$i][$player['user_id']]['score'] ?? 0;
                            ?>
                            <span class="badge <?php echo $score >= 0 ? 'bg-success' : 'bg-danger'; ?>">
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
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">
    Partie non trouvée.
</div>
<?php endif; ?>

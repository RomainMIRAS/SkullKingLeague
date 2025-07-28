// JavaScript pour la gestion améliorée de création de partie
document.addEventListener('DOMContentLoaded', function() {
    initNewGameInterface();
});

function initNewGameInterface() {
    const availablePlayersContainer = document.getElementById('available-players');
    const selectedPlayersContainer = document.getElementById('selected-players');
    const emptyPlaceholder = document.getElementById('empty-placeholder');
    const startGameBtn = document.getElementById('start-game-btn');
    const playerCountInfo = document.getElementById('player-count-info');
    const playerCountWarning = document.getElementById('player-count-warning');
    const selectedCountSpan = document.getElementById('selected-count');
    const randomizeBtn = document.getElementById('randomizeOrder');
    const clearAllBtn = document.getElementById('clearAll');
    const newGameForm = document.getElementById('newGameForm');
    const playerSearchInput = document.getElementById('player-search');
    const clearSearchBtn = document.getElementById('clear-search');

    let selectedPlayers = [];
    let draggedElement = null;
    let allPlayers = []; // Store all players for search filtering

    // Vérifier que tous les éléments existent
    if (!availablePlayersContainer || !selectedPlayersContainer) {
        console.error('Éléments requis manquants pour l\'interface de création de partie');
        return;
    }

    // Initialize all players from the container
    initializeAllPlayers();

    // Search functionality
    if (playerSearchInput) {
        playerSearchInput.addEventListener('input', function() {
            filterPlayers(this.value);
        });
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            playerSearchInput.value = '';
            filterPlayers('');
            playerSearchInput.focus();
        });
    }

    // Événement de clic sur les joueurs disponibles
    availablePlayersContainer.addEventListener('click', function(e) {
        const playerItem = e.target.closest('.available-player');
        if (playerItem && selectedPlayers.length < 8) {
            selectPlayer(playerItem);
        }
    });

    // Fonction pour sélectionner un joueur
    function selectPlayer(playerElement) {
        const playerId = playerElement.getAttribute('data-player-id');
        const playerName = playerElement.getAttribute('data-player-name');
        const playerElo = playerElement.getAttribute('data-player-elo');

        // Vérifier si le joueur n'est pas déjà sélectionné
        if (selectedPlayers.find(p => p.id === playerId)) {
            return;
        }

        // Ajouter aux joueurs sélectionnés
        const playerData = {
            id: playerId,
            name: playerName,
            elo: playerElo
        };
        selectedPlayers.push(playerData);

        // Masquer de la liste disponible
        playerElement.style.display = 'none';

        // Ajouter à la liste sélectionnée
        addPlayerToSelectedList(playerData);

        updateInterface();
        
        // Update search results after selection
        if (playerSearchInput && playerSearchInput.value) {
            filterPlayers(playerSearchInput.value);
        }
    }

    // Fonction pour ajouter un joueur à la liste sélectionnée
    function addPlayerToSelectedList(playerData) {
        hideEmptyPlaceholder();

        const position = selectedPlayers.length;
        const playerElement = document.createElement('div');
        playerElement.className = 'list-group-item d-flex justify-content-between align-items-center selected-player';
        playerElement.setAttribute('data-player-id', playerData.id);
        playerElement.setAttribute('draggable', 'true');
        playerElement.style.cursor = 'move';

        playerElement.innerHTML = `
            <div class="d-flex align-items-center flex-grow-1">
                <span class="badge bg-primary rounded-pill me-2 player-position">${position}</span>
                <div class="flex-grow-1">
                    <strong>${playerData.name}</strong>
                    <small class="text-muted d-block">${playerData.elo} ELO</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <div class="btn-group-vertical" role="group" style="height: 40px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary move-up" data-player-id="${playerData.id}" style="padding: 2px 6px; font-size: 10px;" title="Monter">
                        <i class="bi bi-chevron-up"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary move-down" data-player-id="${playerData.id}" style="padding: 2px 6px; font-size: 10px;" title="Descendre">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
                <i class="bi bi-grip-vertical text-muted mx-1" style="cursor: grab;" title="Glisser-déposer"></i>
                <button type="button" class="btn btn-sm btn-outline-danger remove-player" data-player-id="${playerData.id}" title="Supprimer">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `;

        // Événement de suppression
        const removeBtn = playerElement.querySelector('.remove-player');
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            removePlayer(playerData.id);
        });

        // Événements de montée/descente
        const moveUpBtn = playerElement.querySelector('.move-up');
        const moveDownBtn = playerElement.querySelector('.move-down');
        
        moveUpBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            movePlayerUp(playerData.id);
        });
        
        moveDownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            movePlayerDown(playerData.id);
        });

        // Événements de drag and drop simplifiés
        playerElement.addEventListener('dragstart', handleDragStart);
        playerElement.addEventListener('dragover', handleDragOver);
        playerElement.addEventListener('drop', handleDrop);
        playerElement.addEventListener('dragend', handleDragEnd);

        selectedPlayersContainer.appendChild(playerElement);
    }

    // Gestionnaires de drag and drop
    function handleDragStart(e) {
        draggedElement = this;
        this.style.opacity = '0.5';
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.outerHTML);
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        return false;
    }

    function handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }

        if (draggedElement !== this) {
            // Réorganiser les éléments
            const draggedId = draggedElement.getAttribute('data-player-id');
            const targetId = this.getAttribute('data-player-id');
            
            reorderPlayersArray(draggedId, targetId);
            rebuildSelectedList();
        }
        return false;
    }

    function handleDragEnd(e) {
        this.style.opacity = '1';
        draggedElement = null;
    }

    // Fonction pour réorganiser le tableau des joueurs
    function reorderPlayersArray(draggedId, targetId) {
        const draggedIndex = selectedPlayers.findIndex(p => p.id === draggedId);
        const targetIndex = selectedPlayers.findIndex(p => p.id === targetId);
        
        if (draggedIndex !== -1 && targetIndex !== -1) {
            // Retirer l'élément déplacé
            const draggedPlayer = selectedPlayers.splice(draggedIndex, 1)[0];
            // L'insérer à la nouvelle position
            selectedPlayers.splice(targetIndex, 0, draggedPlayer);
        }
    }

    // Fonctions pour les boutons de déplacement
    function movePlayerUp(playerId) {
        const playerIndex = selectedPlayers.findIndex(p => p.id === playerId);
        if (playerIndex > 0) {
            // Échanger avec le joueur précédent
            [selectedPlayers[playerIndex - 1], selectedPlayers[playerIndex]] = 
            [selectedPlayers[playerIndex], selectedPlayers[playerIndex - 1]];
            
            rebuildSelectedList();
        }
    }

    function movePlayerDown(playerId) {
        const playerIndex = selectedPlayers.findIndex(p => p.id === playerId);
        if (playerIndex < selectedPlayers.length - 1) {
            // Échanger avec le joueur suivant
            [selectedPlayers[playerIndex], selectedPlayers[playerIndex + 1]] = 
            [selectedPlayers[playerIndex + 1], selectedPlayers[playerIndex]];
            
            rebuildSelectedList();
        }
    }

    // Fonction pour supprimer un joueur
    function removePlayer(playerId) {
        // Retirer de la liste sélectionnée
        selectedPlayers = selectedPlayers.filter(p => p.id !== playerId);

        // Supprimer de l'interface
        const selectedElement = selectedPlayersContainer.querySelector(`[data-player-id="${playerId}"]`);
        if (selectedElement) {
            selectedElement.remove();
        }

        // Remettre dans la liste disponible
        const availableElement = availablePlayersContainer.querySelector(`[data-player-id="${playerId}"]`);
        if (availableElement) {
            availableElement.style.display = 'block';
        }

        updateInterface();
        
        // Update search results after removal
        if (playerSearchInput && playerSearchInput.value) {
            filterPlayers(playerSearchInput.value);
        }
    }

    // Fonction pour mettre à jour l'interface
    function updateInterface() {
        const count = selectedPlayers.length;
        
        // Mettre à jour les positions
        updatePlayerPositions();

        // Afficher/masquer le placeholder
        if (count === 0) {
            showEmptyPlaceholder();
        } else {
            hideEmptyPlaceholder();
        }

        // Mettre à jour les informations
        if (selectedCountSpan) {
            selectedCountSpan.textContent = count;
        }
        if (playerCountInfo) {
            playerCountInfo.style.display = count > 0 ? 'block' : 'none';
        }

        // Validation
        const isValid = count >= 1 && count <= 8;
        if (startGameBtn) {
            startGameBtn.disabled = !isValid;
        }
        
        if (count > 0 && !isValid) {
            if (playerCountWarning) playerCountWarning.style.display = 'block';
            if (playerCountInfo) playerCountInfo.style.display = 'none';
        } else {
            if (playerCountWarning) playerCountWarning.style.display = 'none';
        }

        // Mettre à jour le texte du bouton
        if (startGameBtn) {
            if (count > 0 && isValid) {
                startGameBtn.innerHTML = `<i class="bi bi-play-fill"></i> Commencer avec ${count} joueur${count > 1 ? 's' : ''}`;
            } else {
                startGameBtn.innerHTML = '<i class="bi bi-play-fill"></i> Commencer la partie';
            }
        }
    }

    // Fonction pour mettre à jour les numéros de position
    function updatePlayerPositions() {
        const selectedElements = selectedPlayersContainer.querySelectorAll('.selected-player');
        selectedElements.forEach((element, index) => {
            const positionBadge = element.querySelector('.player-position');
            if (positionBadge) {
                positionBadge.textContent = index + 1;
            }
        });
    }

    // Fonctions pour le placeholder
    function showEmptyPlaceholder() {
        if (emptyPlaceholder) {
            emptyPlaceholder.style.display = 'block';
        }
    }

    function hideEmptyPlaceholder() {
        if (emptyPlaceholder) {
            emptyPlaceholder.style.display = 'none';
        }
    }

    // Événement pour mélanger l'ordre
    if (randomizeBtn) {
        randomizeBtn.addEventListener('click', function() {
            if (selectedPlayers.length > 1) {
                // Mélanger l'array avec l'algorithme Fisher-Yates
                for (let i = selectedPlayers.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [selectedPlayers[i], selectedPlayers[j]] = [selectedPlayers[j], selectedPlayers[i]];
                }
                
                // Refaire l'affichage
                rebuildSelectedList();
            }
        });
    }

    // Événement pour tout effacer
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            // Remettre tous les joueurs dans la liste disponible
            selectedPlayers.forEach(player => {
                const availableElement = availablePlayersContainer.querySelector(`[data-player-id="${player.id}"]`);
                if (availableElement) {
                    availableElement.style.display = 'block';
                }
            });
            
            selectedPlayers = [];
            
            // Vider la liste sélectionnée
            const selectedElements = selectedPlayersContainer.querySelectorAll('.selected-player');
            selectedElements.forEach(element => element.remove());
            
            updateInterface();
            
            // Clear search after clearing all players
            if (playerSearchInput) {
                playerSearchInput.value = '';
                filterPlayers('');
            }
        });
    }

    // Fonction pour reconstruire la liste sélectionnée
    function rebuildSelectedList() {
        // Supprimer tous les éléments sélectionnés
        const selectedElements = selectedPlayersContainer.querySelectorAll('.selected-player');
        selectedElements.forEach(element => element.remove());

        // Recréer la liste dans le bon ordre
        selectedPlayers.forEach((player) => {
            addPlayerToSelectedList(player);
        });

        updateInterface();
    }

    // Soumission du formulaire
    if (newGameForm) {
        newGameForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (selectedPlayers.length >= 1 && selectedPlayers.length <= 8) {
                // Supprimer les anciens champs cachés s'ils existent
                const existingInputs = newGameForm.querySelectorAll('input[name="players[]"]');
                existingInputs.forEach(input => input.remove());
                
                // Créer les champs cachés pour l'ordre des joueurs
                selectedPlayers.forEach((player) => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'players[]';
                    hiddenInput.value = player.id;
                    newGameForm.appendChild(hiddenInput);
                });
                
                // Soumettre le formulaire
                newGameForm.submit();
            }
        });
    }

    // Initialize all players data from the DOM
    function initializeAllPlayers() {
        const playerElements = availablePlayersContainer.querySelectorAll('.available-player');
        allPlayers = Array.from(playerElements).map(element => ({
            id: element.getAttribute('data-player-id'),
            name: element.getAttribute('data-player-name'),
            elo: element.getAttribute('data-player-elo'),
            element: element
        }));
    }

    // Filter players based on search term
    function filterPlayers(searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        
        allPlayers.forEach(player => {
            const matchesSearch = player.name.toLowerCase().includes(term);
            const isSelected = selectedPlayers.find(p => p.id === player.id);
            
            if (matchesSearch && !isSelected) {
                player.element.style.display = 'block';
            } else if (!isSelected) {
                player.element.style.display = 'none';
            }
        });

        // Show "no results" message if no players are visible
        const visiblePlayers = allPlayers.filter(player => 
            player.element.style.display !== 'none' && 
            !selectedPlayers.find(p => p.id === player.id)
        );

        showNoResultsMessage(visiblePlayers.length === 0 && term !== '');
    }

    // Show/hide no results message
    function showNoResultsMessage(show) {
        let noResultsMsg = document.getElementById('no-results-message');
        
        if (show && !noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.id = 'no-results-message';
            noResultsMsg.className = 'text-center text-muted p-3';
            noResultsMsg.innerHTML = '<i class="bi bi-search"></i><br>Aucun joueur trouvé';
            availablePlayersContainer.appendChild(noResultsMsg);
        } else if (!show && noResultsMsg) {
            noResultsMsg.remove();
        }
    }

    // Initialisation
    updateInterface();
}

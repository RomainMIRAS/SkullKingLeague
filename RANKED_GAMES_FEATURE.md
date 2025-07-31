# Ranked/Unranked Games Feature

## Overview

This feature adds the ability to create both **ranked** and **unranked** games in the Skull King League application.

- **Ranked games**: Affect player ELO ratings and statistics (default behavior)
- **Unranked games**: Do not affect player ELO ratings or statistics (friendly games)

## Implementation Details

### Database Changes

1. **New column**: Added `is_ranked` BOOLEAN column to the `games` table
   - Default value: `TRUE` (maintains backward compatibility)
   - Location: After `season_id` column

2. **Migration script**: `config/migrate_ranked_games.php`
   - Safely adds the column to existing installations
   - Marks all existing games as ranked by default

### Code Changes

#### 1. Game Model (`src/models/Game.php`)
- Added `public $is_ranked` property
- Updated `create($player_ids, $is_ranked = true)` method to accept ranked flag
- Modified SQL query to insert `is_ranked` value

#### 2. Game Controller (`src/controllers/GameController.php`)
- **Create action**: Processes `is_ranked` POST parameter (defaults to `true`)
- **Finish action**: Skips ELO updates and statistics for unranked games
- Uses `$game_data['is_ranked']` to conditionally update player stats

#### 3. User Interface (`src/views/home.php`)
- Added "Type de partie" section to game creation modal
- Bootstrap switch component for ranked/unranked selection
- Clear descriptions explaining the difference

#### 4. JavaScript (`assets/js/new-game.js`)
- Handles ranked checkbox interaction
- Updates description text based on selection:
  - Checked: "Cette partie affectera le classement ELO des joueurs."
  - Unchecked: "Cette partie ne sera pas comptée dans le classement ELO (partie amicale)."

#### 5. Game Views
- **Game Play** (`src/views/game_play.php`): Shows ranked/unranked badge in header
- **Game Finish** (`src/views/game_finish.php`): 
  - Displays appropriate message (ranked vs unranked)
  - Conditionally shows/hides ELO columns for unranked games

## User Experience

### Game Creation
1. User opens "Nouvelle Partie" modal
2. Selects players as usual
3. **NEW**: Toggles "Partie classée" switch
   - ✅ Checked (default): Ranked game - affects ELO
   - ❌ Unchecked: Unranked game - friendly match
4. Creates game with chosen settings

### During Game
- Header badge indicates game type:
  - 🏆 **"Classée"** (yellow badge)
  - ❤️ **"Amicale"** (gray badge)

### Game Completion
- **Ranked games**: Show ELO changes and statistics updates
- **Unranked games**: Show only final scores, no ELO impact

## Technical Benefits

1. **Backward Compatibility**: All existing games remain ranked
2. **Minimal Changes**: Only 8 files modified
3. **Clear UI**: Obvious visual distinctions between game types
4. **Performance**: No additional queries for ranked games
5. **Data Integrity**: Unranked games are stored but don't affect rankings

## Migration Instructions

For existing installations:

```bash
# Run the migration script
php config/migrate_ranked_games.php
```

For new installations:
- The `config/init_db.php` script already includes the `is_ranked` column

## Testing

The implementation was tested with:
- ✅ PHP syntax validation for all modified files
- ✅ UI component functionality (checkbox interaction)
- ✅ Visual design and user experience
- ✅ Screenshots demonstrating both states

## Files Modified

1. `config/init_db.php` - Database schema
2. `config/migrate_ranked_games.php` - **NEW** Migration script
3. `src/models/Game.php` - Model logic
4. `src/controllers/GameController.php` - Controller logic
5. `src/views/home.php` - Game creation UI
6. `src/views/game_play.php` - Game play UI
7. `src/views/game_finish.php` - Game finish UI
8. `assets/js/new-game.js` - Frontend interaction
9. `.gitignore` - Updated to exclude test files
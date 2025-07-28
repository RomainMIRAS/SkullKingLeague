-- Add is_ranked column to games table
-- Default to TRUE to maintain compatibility with existing games
ALTER TABLE games ADD COLUMN is_ranked BOOLEAN DEFAULT TRUE COMMENT 'Whether the game affects ELO ratings';

-- Add index for performance when filtering by ranked status
CREATE INDEX idx_games_ranked ON games(is_ranked);
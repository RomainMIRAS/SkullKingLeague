# Database Migration for Round Editing Feature

## Overview
The round editing feature requires a new column in the `rounds` table to track when rounds have been modified.

## Migration Required
Add the `modified_at` column to the existing `rounds` table:

```sql
ALTER TABLE rounds ADD COLUMN modified_at TIMESTAMP NULL DEFAULT NULL;
```

## What this enables
- Track when a round was last modified
- Provide visual indicators for edited rounds
- Maintain audit trail of changes

## Implementation Notes
- The column is nullable (NULL by default for existing rounds)
- Only gets set when a round is actually modified via the edit interface
- Used by `isRoundModified()` method to highlight edited rounds in yellow

## For New Installations
The column is already included in the updated `init_db.php` script, so new installations will automatically have this column.

## For Existing Installations
Run the ALTER TABLE statement above on your existing database before using the round editing feature.
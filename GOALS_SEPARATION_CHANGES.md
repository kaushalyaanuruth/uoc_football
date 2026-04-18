# Match Results Goals Separation Implementation

## Changes Made:

### 1. **Backend - Controller (teamResult.php)**
   - Updated `addMatchResult()` to use separate `goals_scored` and `goals_conceded` fields
   - Updated `editMatchResult()` to use separate `goals_scored` and `goals_conceded` fields
   - Removed requirement for "score" field (now using goals fields)

### 2. **Database Model (MatchResultModel.php)**
   - Updated `create()` method to insert `goals_scored` and `goals_conceded` instead of `score`

### 3. **Frontend - View (teamResult.view.php)**
   - **Add Match Result Modal:**
     - Replaced single `matchScore` input with two inputs:
       - `goalsScored` (number input, min 0)
       - `goalsConceded` (number input, min 0)
   
   - **Edit Match Result Modal:**
     - Replaced single `editMatchScore` input with two inputs:
       - `editGoalsScored` (number input, min 0)
       - `editGoalsConceded` (number input, min 0)
   
   - **Match Results Table:**
     - Updated table headers from `[..., Score, Date, ...]` to `[..., Goals Scored, Goals Conceded, Date, ...]`
     - Updated table rows to display `goals_scored` and `goals_conceded` separately

### 4. **Frontend - JavaScript (script.js)**
   - Updated `populateEditMatchResultModal()` to populate the new `editGoalsScored` and `editGoalsConceded` fields
   - Form submission already handles FormData automatically, so no changes needed there

### 5. **Database Migration**
   - Created migration script: `public/migration_match_results_goals.php`

## Required Next Steps:

### Step 1: Run the Database Migration
Navigate to: `http://localhost/UOC_Football/public/migration_match_results_goals.php`

This will:
- Add `goals_scored` column to match_results table
- Add `goals_conceded` column to match_results table
- Initialize both columns to 0
- Keep the old `score` column for reference

### Step 2: Update Existing Data (Optional)
If you have existing match results with the old `score` format (e.g., "2 - 1"), you can manually update them by:
- Editing each match result
- Entering the goals scored and goals conceded separately
- Saving the changes

### Step 3: Test the New Functionality
1. Add a new match result with separate goals
2. Verify it displays correctly in the table
3. Edit the match result to verify both fields populate correctly
4. Check that filtering still works properly

## Form Field Mapping:

| Old Field | New Fields |
|-----------|-----------|
| matchScore (text) | goalsScored (number) + goalsConceded (number) |
| editMatchScore (text) | editGoalsScored (number) + editGoalsConceded (number) |

## API Changes:

**POST /teamResult/addMatchResult**
- Old: `matchScore` (string, e.g., "2 - 1")
- New: `goalsScored` (number), `goalsConceded` (number)

**POST /teamResult/editMatchResult**
- Old: `matchScore` (string, e.g., "2 - 1")
- New: `goalsScored` (number), `goalsConceded` (number)

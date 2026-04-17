-- Create Tournaments Table
CREATE TABLE IF NOT EXISTS tournaments (
    tournament_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    team_id INT(32) NOT NULL,
    tournament_name VARCHAR(255) NOT NULL,
    description TEXT,
    tournament_date DATE,
    result VARCHAR(50),
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create Achievements Table
CREATE TABLE IF NOT EXISTS achievements (
    achievement_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    team_id INT(32) NOT NULL,
    achievement_name VARCHAR(255) NOT NULL,
    description TEXT,
    achievement_date DATE,
    award_type VARCHAR(100),
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Create indexes for faster queries
CREATE INDEX idx_tournaments_team_id ON tournaments(team_id);
CREATE INDEX idx_achievements_team_id ON achievements(team_id);

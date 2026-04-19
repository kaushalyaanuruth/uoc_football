CREATE DATABASE UOC_FOOTBALL;
USE UOC_FOOTBALL;

CREATE TABLE IF NOT EXISTS users (
    nic VARCHAR(16) PRIMARY KEY,
    user_id VARCHAR(16) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(20) NOT NULL,
    image VARCHAR(255),
    lane_1 VARCHAR(255),
    lane_2 VARCHAR(255),
    city VARCHAR(100),
    district VARCHAR(100),
    zip_code VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS admins (
    admin_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    nic VARCHAR(16) NOT NULL,
    FOREIGN KEY (nic) REFERENCES users(nic) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS players (
    player_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    position VARCHAR(50) NOT NULL,
    role ENUM('Captain', 'Vice-Captain', 'Player') NOT NULL,
    nic VARCHAR(16) NOT NULL,
    FOREIGN KEY (nic) REFERENCES users(nic) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS coaches (
    coach_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    license VARCHAR(50) NOT NULL,
    nic VARCHAR(16) NOT NULL,
    FOREIGN KEY (nic) REFERENCES users(nic) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS teams (
    team_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    season VARCHAR(20) NOT NULL,
    status ENUM('present', 'past') NOT NULL
);

CREATE TABLE IF NOT EXISTS inventory_items (
    item_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    item_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL DEFAULT 'General',
    total_count INT(32) NOT NULL,
    available_count INT(32) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Available',
    location VARCHAR(100) DEFAULT '',
    description TEXT,
    unit VARCHAR(20) NOT NULL DEFAULT 'pcs',
    icon VARCHAR(100) NOT NULL DEFAULT 'inventory_2',
    team_id INT(32),
    last_updated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(16) NOT NULL,
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(nic) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS events (
    event_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    location VARCHAR(255) NOT NULL,
    date DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS team_players (
    team_id INT(32) NOT NULL,
    player_id INT(32) NOT NULL,
    PRIMARY KEY (team_id, player_id),
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS team_coaches (
    team_id INT(32) NOT NULL,
    coach_id INT(32) NOT NULL,
    PRIMARY KEY (team_id, coach_id),
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (coach_id) REFERENCES coaches(coach_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS budgets (
    budget_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    team_id INT(32) NOT NULL,
    date DATE NOT NULL,
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS captains (
    captain_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    player_id INT(32) NOT NULL,
    FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS test_results (
    test_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    test_type VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    remarks VARCHAR(255),
    player_id INT(32) NOT NULL,
    FOREIGN KEY (player_id) REFERENCES players(player_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS inventory_log (
    log_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    item_id INT(32) NOT NULL,
    taken_by INT(32) NOT NULL,
    quantity INT(32) NOT NULL,
    taken_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    FOREIGN KEY (item_id) REFERENCES inventory_items(item_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (taken_by) REFERENCES players(player_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS practices (
    practice_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    time TIME NOT NULL,
    event_id INT(32) NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS matches (
    match_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    time TIME NOT NULL,
    event_id INT(32) NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS gyms (
    gym_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    time TIME NOT NULL,
    event_id INT(32) NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS specials (
    special_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    time TIME NOT NULL,
    event_id INT(32) NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS incomes (
    income_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    amount FLOAT(23, 2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    budget_id INT(32) NOT NULL,
    FOREIGN KEY (budget_id) REFERENCES budgets(budget_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS expenses (
    expense_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    amount FLOAT(23, 2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    budget_id INT(32) NOT NULL,
    FOREIGN KEY (budget_id) REFERENCES budgets(budget_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS meal_plans (
    meal_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    updated_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    team_id INT(32) NOT NULL,
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS breakfast (
    breakfast_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    meal VARCHAR(255) NOT NULL,
    amount VARCHAR(255) NOT NULL,
    meal_id INT(32) NOT NULL,
    FOREIGN KEY (meal_id) REFERENCES meal_plans(meal_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS lunch (
    lunch_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    meal VARCHAR(255) NOT NULL,
    amount VARCHAR(255) NOT NULL,
    meal_id INT(32) NOT NULL,
    FOREIGN KEY (meal_id) REFERENCES meal_plans(meal_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS dinner (
    dinner_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    meal VARCHAR(255) NOT NULL,
    amount VARCHAR(255) NOT NULL,
    meal_id INT(32) NOT NULL,
    FOREIGN KEY (meal_id) REFERENCES meal_plans(meal_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS exercises (
    exercise_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    exercise VARCHAR(100) NOT NULL,
    reps INT(32) NOT NULL,
    sets INT(32) NOT NULL,
    gym_id INT(32) NOT NULL,
    FOREIGN KEY (gym_id) REFERENCES gyms(gym_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS news (
    id INT(32) PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS tournaments (
    tournament_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(20) NOT NULL,
    team_id INT(32),
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Achievements (
    achievement_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    achievement VARCHAR(20) NOT NULL,
    team_id INT(32),
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE RESTRICT ON UPDATE CASCADE
);
CREATE TABLE IF NOT EXISTS match_results (
    result_id INT(32) PRIMARY KEY AUTO_INCREMENT,
    opponent_team VARCHAR(100) NOT NULL,
    result ENUM('Won', 'Draw', 'Lost') NOT NULL,
    goals_scored INT(32) DEFAULT 0,
    goals_conceded INT(32) DEFAULT 0,
    shots INT(32) DEFAULT 0,
    shots_on_target INT(32) DEFAULT 0,
    possession DECIMAL(5,2) DEFAULT 0,
    passes INT(32) DEFAULT 0,
    passes_accuracy DECIMAL(5,2) DEFAULT 0,
    corners INT(32) DEFAULT 0,
    date DATE NOT NULL,
    notes VARCHAR(255),
    team_id INT(32) NOT NULL,
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE RESTRICT ON UPDATE CASCADE
);


CREATE TABLE player_match_stats (
    stat_id INT AUTO_INCREMENT PRIMARY KEY,

    match_id INT NOT NULL,
    player_id INT NOT NULL,

    position_played VARCHAR(100),
    minutes_played INT DEFAULT 0,
    substitution_status ENUM('Started', 'Substitute', 'Unused') DEFAULT 'Started',


    goals_scored INT DEFAULT 0,
    assists INT DEFAULT 0,
    shots_on_target INT DEFAULT 0,
    shots_off_target INT DEFAULT 0,
    key_passes INT DEFAULT 0,
    successful_dribbles INT DEFAULT 0,

    completed_passes INT DEFAULT 0,
    line_breaking_passes INT DEFAULT 0,

    tackles_won INT DEFAULT 0,
    interceptions INT DEFAULT 0,
    defensive_duels_won INT DEFAULT 0,
    aerial_duels_won INT DEFAULT 0,

    yellow_cards INT DEFAULT 0,
    red_cards INT DEFAULT 0,
    fouls_committed INT DEFAULT 0,
    fouls_won INT DEFAULT 0,

    notes TEXT,

    CONSTRAINT fk_stats_match
        FOREIGN KEY (match_id) REFERENCES match_results(result_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_stats_player
        FOREIGN KEY (player_id) REFERENCES players(player_id)
        ON DELETE CASCADE,

    UNIQUE (match_id, player_id)
);

CREATE TABLE IF NOT EXISTS store_management (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    item_image VARCHAR(255),
    status ENUM('Available', 'Sold Out') DEFAULT 'Available'
);
CREATE DATABASE IF NOT EXISTS UOC_FOOTBALL;
USE UOC_FOOTBALL;

-- USERS
CREATE TABLE users (
    nic VARCHAR(16) PRIMARY KEY,
    user_id VARCHAR(16) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    phone_number VARCHAR(20),
    image VARCHAR(255),
    lane_1 VARCHAR(255),
    lane_2 VARCHAR(255),
    city VARCHAR(100),
    district VARCHAR(100),
    zip_code VARCHAR(20)
);

-- ADMINS / PLAYERS / COACHES
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    nic VARCHAR(16),
    FOREIGN KEY (nic) REFERENCES users(nic)
);

CREATE TABLE players (
    player_id INT AUTO_INCREMENT PRIMARY KEY,
    position VARCHAR(50),
    role ENUM('Captain','Vice-Captain','Player'),
    nic VARCHAR(16),
    FOREIGN KEY (nic) REFERENCES users(nic)
);

CREATE TABLE coaches (
    coach_id INT AUTO_INCREMENT PRIMARY KEY,
    license VARCHAR(50),
    nic VARCHAR(16),
    FOREIGN KEY (nic) REFERENCES users(nic)
);

-- TEAMS
CREATE TABLE teams (
    team_id INT AUTO_INCREMENT PRIMARY KEY,
    season VARCHAR(20)
);

-- MEAL PLAN
CREATE TABLE meal_plans (
    meal_id INT AUTO_INCREMENT PRIMARY KEY,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    team_id INT,
    FOREIGN KEY (team_id) REFERENCES teams(team_id)
);

CREATE TABLE breakfast (
    breakfast_id INT AUTO_INCREMENT PRIMARY KEY,
    meal_id INT,
    meal VARCHAR(255),
    amount VARCHAR(255),
    FOREIGN KEY (meal_id) REFERENCES meal_plans(meal_id)
);

CREATE TABLE lunch (
    lunch_id INT AUTO_INCREMENT PRIMARY KEY,
    meal_id INT,
    meal VARCHAR(255),
    amount VARCHAR(255),
    FOREIGN KEY (meal_id) REFERENCES meal_plans(meal_id)
);

CREATE TABLE dinner (
    dinner_id INT AUTO_INCREMENT PRIMARY KEY,
    meal_id INT,
    meal VARCHAR(255),
    amount VARCHAR(255),
    FOREIGN KEY (meal_id) REFERENCES meal_plans(meal_id)
);

-- INVENTORY
CREATE TABLE inventory_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100),
    total_count INT,
    available_count INT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(16),
    FOREIGN KEY (updated_by) REFERENCES users(nic)
);

-- EVENTS
CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(255),
    date DATE
);

-- TEAM RELATIONS
CREATE TABLE team_players (
    team_id INT,
    player_id INT,
    PRIMARY KEY (team_id, player_id),
    FOREIGN KEY (team_id) REFERENCES teams(team_id),
    FOREIGN KEY (player_id) REFERENCES players(player_id)
);

CREATE TABLE team_coaches (
    team_id INT,
    coach_id INT,
    PRIMARY KEY (team_id, coach_id),
    FOREIGN KEY (team_id) REFERENCES teams(team_id),
    FOREIGN KEY (coach_id) REFERENCES coaches(coach_id)
);

-- BUDGET
CREATE TABLE budgets (
    budget_id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT,
    date DATE,
    FOREIGN KEY (team_id) REFERENCES teams(team_id)
);

-- TEST RESULTS
CREATE TABLE test_results (
    test_id INT AUTO_INCREMENT PRIMARY KEY,
    test_type VARCHAR(50),
    date DATE,
    remarks VARCHAR(255),
    player_id INT,
    FOREIGN KEY (player_id) REFERENCES players(player_id)
);

-- INVENTORY LOG
CREATE TABLE inventory_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    taken_by INT,
    quantity INT,
    taken_date DATE,
    return_date DATE,
    FOREIGN KEY (item_id) REFERENCES inventory_items(item_id),
    FOREIGN KEY (taken_by) REFERENCES players(player_id)
);

-- EVENTS TYPES
CREATE TABLE practices (
    practice_id INT AUTO_INCREMENT PRIMARY KEY,
    time TIME,
    event_id INT,
    FOREIGN KEY (event_id) REFERENCES events(event_id)
);

CREATE TABLE matches (
    match_id INT AUTO_INCREMENT PRIMARY KEY,
    time TIME,
    event_id INT,
    FOREIGN KEY (event_id) REFERENCES events(event_id)
);

CREATE TABLE gyms (
    gym_id INT AUTO_INCREMENT PRIMARY KEY,
    time TIME,
    event_id INT,
    FOREIGN KEY (event_id) REFERENCES events(event_id)
);

CREATE TABLE specials (
    special_id INT AUTO_INCREMENT PRIMARY KEY,
    time TIME,
    event_id INT,
    FOREIGN KEY (event_id) REFERENCES events(event_id)
);

-- FINANCE
CREATE TABLE incomes (
    income_id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255),
    image VARCHAR(255),
    budget_id INT,
    FOREIGN KEY (budget_id) REFERENCES budgets(budget_id)
);

CREATE TABLE expenses (
    expense_id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255),
    image VARCHAR(255),
    budget_id INT,
    FOREIGN KEY (budget_id) REFERENCES budgets(budget_id)
);

-- EXERCISES
CREATE TABLE exercises (
    exercise_id INT AUTO_INCREMENT PRIMARY KEY,
    exercise VARCHAR(100),
    reps INT,
    sets INT,
    gym_id INT,
    FOREIGN KEY (gym_id) REFERENCES gyms(gym_id)
);
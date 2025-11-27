<?php

require_once __DIR__ . '/../core/Database.php';

class User
{
    use Database; // Import DB trait

    protected $table = "users";

    // Update user
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }
        
        $fieldString = implode(', ', $fields);
        $query = "UPDATE $this->table SET $fieldString WHERE id = :id";
        
        return $this->query($query, $params);
    }

    // Fetch all users
    public function getAllUsers()
    {
        $query = "SELECT * FROM $this->table";
        return $this->query($query);
    }

    // Fetch a user by username (Example for login)
    public function getUserByUsername($username)
    {
        $query = "SELECT * FROM $this->table WHERE username = :username LIMIT 1";
        return $this->query($query, ['username' => $username]);
    }

    // Check if username already exists
    public function usernameExists($username)
    {
        $query = "SELECT COUNT(*) as count FROM $this->table WHERE username = :username";
        $result = $this->query($query, ['username' => $username]);
        return $result && $result[0]->count > 0;
    }

    // Create a player user account
    public function createPlayerUser($nic, $full_name, $email = null)
    {
        // Check if user already exists
        if ($this->usernameExists($nic)) {
            return ['success' => false, 'message' => 'User account already exists for this NIC'];
        }

        // Generate email if not provided
        if (empty($email)) {
            $email = strtolower(str_replace(' ', '.', $full_name)) . '@uocfootball.com';
        }

        // Hash the default password
        $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);

        $query = "INSERT INTO $this->table (username, email, password, role, full_name) 
                  VALUES (:username, :email, :password, :role, :full_name)";

        try {
            $result = $this->query($query, [
                'username' => $nic,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'player',
                'full_name' => $full_name
            ]);

            return ['success' => true, 'message' => 'Player user account created'];
        } catch (Exception $e) {
            error_log("Error creating player user: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create user account'];
        }
    }

    // Create a coach user account
    public function createCoachUser($nic, $full_name, $phone_number = null, $email = null)
    {
        // Check if user already exists
        if ($this->usernameExists($nic)) {
            return ['success' => false, 'message' => 'User account already exists for this NIC'];
        }

        // Generate email if not provided
        if (empty($email)) {
            $email = strtolower(str_replace(' ', '.', $full_name)) . '@uocfootball.com';
        }

        // Hash the default password
        $hashedPassword = password_hash('123456', PASSWORD_BCRYPT);

        $query = "INSERT INTO $this->table (username, email, password, role, full_name, phone_number) 
                  VALUES (:username, :email, :password, :role, :full_name, :phone_number)";

        try {
            $result = $this->query($query, [
                'username' => $nic,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'coach',
                'full_name' => $full_name,
                'phone_number' => $phone_number
            ]);

            return ['success' => true, 'message' => 'Coach user account created'];
        } catch (Exception $e) {
            error_log("Error creating coach user: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create user account'];
        }
    }

    public function insertUser($username, $password, $user_id, $email, $user_type)
    {
        // Hash the password before storing
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $query = "INSERT INTO $this->table (username, password, email, role, full_name) 
                VALUES (:username, :password, :email, :role, :full_name)";

        // Run insert query
        $result = $this->query($query, [
            'username' => $username,
            'password' => $hashedPassword,
            'email' => $email,
            'role' => $user_type,
            'full_name' => $username // You might want to pass this as a parameter
        ]);

        // Redirect based on insert result
        if ($result !== false) {
            header('Location: http://localhost/UOC_Football/public/login');
        } else {
            header('Location: http://localhost/UOC_Football/public/landingPage');
        }
        exit();
    }
    
    public function loginUser($username, $password)
    {
        error_log("=== LOGIN ATTEMPT ===");
        error_log("Username: " . $username);
        error_log("Password provided: " . ($password ? 'YES' : 'NO'));
        
        $user = $this->getUserByUsername($username);
        
        error_log("User found: " . ($user ? 'YES' : 'NO'));
        error_log("User data: " . print_r($user, true));

        if ($user && !empty($user) && isset($user[0]->password)) {
            error_log("Password hash from DB: " . substr($user[0]->password, 0, 30));
            
            // Verify password using the correct column name 'password'
            if (password_verify($password, $user[0]->password)) {
                error_log("Password verification: SUCCESS");
                
                // Password is correct, start session
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['user_id'] = $user[0]->id ?? $user[0]->user_id;
                $_SESSION['username'] = $user[0]->username;
                $_SESSION['user_type'] = $user[0]->role ?? $user[0]->user_type;

                error_log("Session user_id: " . $_SESSION['user_id']);
                error_log("Session user_type: " . $_SESSION['user_type']);

                // Redirect based on user role
                $userType = $user[0]->role ?? $user[0]->user_type;
                error_log("User role/type: " . $userType);
                
                // Check if user is a player and get their specific role (captain/vice-captain)
                if ($userType === 'player') {
                    // Check if player is captain or vice-captain
                    $playerQuery = "SELECT role FROM players WHERE nic = :nic LIMIT 1";
                    $playerResult = $this->query($playerQuery, ['nic' => $username]);
                    
                    if ($playerResult && !empty($playerResult)) {
                        $playerRole = $playerResult[0]->role;
                        error_log("Player role: " . $playerRole);
                        
                        if ($playerRole === 'captain' || $playerRole === 'vice_captain') {
                            $_SESSION['player_role'] = $playerRole;
                            header('Location: ' . ROOT . '/captainDashboard');
                            exit();
                        }
                    }
                    
                    // Regular player
                    header('Location: ' . ROOT . '/playerDashboard');
                    exit();
                }
                
                // Redirect based on user type
                if($userType === 'admin') {
                    header('Location: ' . ROOT . '/adminDashboard');
                } elseif($userType === 'coach') {
                    header('Location: ' . ROOT . '/coachDashboard');
                } else {
                    header('Location: ' . ROOT . '/playerDashboard');
                }
                exit();
            } else {
                error_log("Password verification: FAILED");
            }
        } else {
            error_log("User validation failed - user empty or password not set");
        }
        
        // Invalid credentials, redirect back to login with error
        error_log("Redirecting to login with error");
        header('Location: http://localhost/UOC_Football/public/login?error=invalid_credentials');
        exit();
    }

}

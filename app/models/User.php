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

    // Update user by NIC
    public function updateByNic($nic, $data)
    {
        $fields = [];
        $params = ['nic' => $nic];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }
        
        $fieldString = implode(', ', $fields);
        $query = "UPDATE $this->table SET $fieldString WHERE nic = :nic";
        
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
        $query = "SELECT * FROM $this->table WHERE user_id = :user_id LIMIT 1";
        return $this->query($query, ['user_id' => $username]);
    }

    // Check if username already exists
    public function usernameExists($username)
    {
        $query = "SELECT COUNT(*) as count FROM $this->table WHERE user_id = :user_id";
        $result = $this->query($query, ['user_id' => $username]);
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

        $query = "INSERT INTO $this->table (user_id, nic, email, password, first_name, last_name) 
                  VALUES (:user_id, :nic, :email, :password, :first_name, :last_name)";

        try {
            $result = $this->query($query, [
                'user_id' => $nic,
                'nic' => $nic,
                'email' => $email,
                'password' => $hashedPassword,
                'first_name' => $full_name,
                'last_name' => ''
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

        $query = "INSERT INTO $this->table (user_id, nic, email, password, first_name, last_name, phone_number) 
                  VALUES (:user_id, :nic, :email, :password, :first_name, :last_name, :phone_number)";

        try {
            $result = $this->query($query, [
                'user_id' => $nic,
                'nic' => $nic,
                'email' => $email,
                'password' => $hashedPassword,
                'first_name' => $full_name,
                'last_name' => '',
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
        
        $query = "INSERT INTO $this->table (user_id, nic, password, email, first_name, last_name) 
                VALUES (:user_id, :nic, :password, :email, :first_name, :last_name)";

        // Run insert query
        $result = $this->query($query, [
            'user_id' => $username,
            'nic' => $username,
            'password' => $hashedPassword,
            'email' => $email,
            'first_name' => $username,
            'last_name' => ''
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
            
            // Verify password
            if (password_verify($password, $user[0]->password)) {
                error_log("Password verification: SUCCESS");
                
                // Password is correct, start session
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $nic = $user[0]->nic;
                $_SESSION['user_id'] = $user[0]->user_id;
                $_SESSION['nic'] = $nic;
                
                error_log("Session user_id: " . $_SESSION['user_id']);
                error_log("Session nic: " . $_SESSION['nic']);

                // Determine user type by checking various tables
                // Check if admin
                $adminQuery = "SELECT * FROM admins WHERE nic = :nic LIMIT 1";
                $adminResult = $this->query($adminQuery, ['nic' => $nic]);
                
                if ($adminResult && !empty($adminResult)) {
                    $_SESSION['user_type'] = 'admin';
                    error_log("User type: ADMIN");
                    header('Location: ' . ROOT . '/adminDashboard');
                    exit();
                }
                
                // Check if coach
                $coachQuery = "SELECT * FROM coaches WHERE nic = :nic LIMIT 1";
                $coachResult = $this->query($coachQuery, ['nic' => $nic]);
                
                if ($coachResult && !empty($coachResult)) {
                    $_SESSION['user_type'] = 'coach';
                    error_log("User type: COACH");
                    header('Location: ' . ROOT . '/coachDashboard');
                    exit();
                }
                
                // Check if player
                $playerQuery = "SELECT role FROM players WHERE nic = :nic LIMIT 1";
                $playerResult = $this->query($playerQuery, ['nic' => $nic]);
                
                if ($playerResult && !empty($playerResult)) {
                    $_SESSION['user_type'] = 'player';
                    $playerRole = $playerResult[0]->role;
                    error_log("Player role: " . $playerRole);
                    
                    if ($playerRole === 'Captain' || $playerRole === 'Vice-Captain') {
                        $_SESSION['player_role'] = $playerRole;
                        header('Location: ' . ROOT . '/captainDashboard');
                        exit();
                    }
                    
                    // Regular player
                    header('Location: ' . ROOT . '/playerDashboard');
                    exit();
                }
                
                // Default to player dashboard
                $_SESSION['user_type'] = 'player';
                header('Location: ' . ROOT . '/playerDashboard');
                exit();
            } else {
                error_log("Password verification: FAILED");
            }
        } else {
            error_log("User validation failed - user empty or password not set");
        }
        
        error_log("Redirecting to login with error");
        header('Location: http://localhost/UOC_Football/public/login?error=invalid_credentials');
        exit();
    }

    public function logout()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Log the logout
        error_log("=== LOGOUT ===");
        error_log("User: " . ($_SESSION['user_id'] ?? 'Unknown'));

        // Clear all session data
        $_SESSION = [];
        session_destroy();
        
        // Prevent browser caching to avoid back button issues
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: 0");
        
        // Redirect to landing page
        header('Location: http://localhost/UOC_Football/public/index.php?url=landingPage');
        exit();
    }

}

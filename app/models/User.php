<?php

require_once __DIR__ . '/../core/Database.php';

class User
{
    use Database; // Import DB trait

    protected $table = "users";

    private function verifyPasswordCompat($plainPassword, $storedPassword)
    {
        if (!is_string($storedPassword) || $storedPassword === '') {
            return false;
        }

        // Normal path for properly hashed passwords.
        if (password_verify($plainPassword, $storedPassword)) {
            return true;
        }

        // Backward compatibility for legacy plaintext passwords in DB.
        return hash_equals($storedPassword, $plainPassword);
    }

    private function needsRehashOrPlaintext($storedPassword)
    {
        $info = password_get_info((string) $storedPassword);

        // algo = 0 means this is not a password_hash()-generated hash.
        if (($info['algo'] ?? 0) === 0) {
            return true;
        }

        return password_needs_rehash($storedPassword, PASSWORD_BCRYPT);
    }

    private function isCaptainLikeRole($role)
    {
        $normalized = strtolower(trim((string) $role));
        $normalized = str_replace(['_', ' '], '-', $normalized);

        return in_array($normalized, ['captain', 'vice-captain', 'vicecaptain'], true);
    }

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
        $params = ['where_nic' => $nic];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }
        
        $fieldString = implode(', ', $fields);
        $query = "UPDATE $this->table SET $fieldString WHERE nic = :where_nic";
        
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
        $query = "SELECT * FROM $this->table WHERE user_id = :user_id OR nic = :nic LIMIT 1";
        return $this->query($query, [
            'user_id' => $username,
            'nic' => $username
        ]);
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
        $username = trim((string) $username);
        $password = (string) $password;

        // Allow quick coach access with canonical local credentials.
        if (strtolower($username) === 'coach' && $password === 'coach') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $coachAliasRows = $this->query(
                "SELECT c.nic, u.user_id
                 FROM coaches c
                 LEFT JOIN users u ON u.nic = c.nic
                 ORDER BY c.coach_id ASC
                 LIMIT 1"
            );

            $aliasNic = 'coach';
            $aliasUserId = 'coach';
            if (!empty($coachAliasRows)) {
                $aliasNic = (string) ($coachAliasRows[0]->nic ?? 'coach');
                $aliasUserId = (string) ($coachAliasRows[0]->user_id ?? $aliasNic);
            }

            $_SESSION['user_id'] = $aliasUserId;
            $_SESSION['nic'] = $aliasNic;
            $_SESSION['user_type'] = 'coach';
            $_SESSION['must_change_password'] = false;

            header('Location: ' . ROOT . '/coachDashboard');
            exit();
        }

        error_log("=== LOGIN ATTEMPT ===");
        error_log("Username: " . $username);
        error_log("Password provided: " . ($password ? 'YES' : 'NO'));
        
        $user = $this->getUserByUsername($username);
        
        error_log("User found: " . ($user ? 'YES' : 'NO'));
        error_log("User data: " . print_r($user, true));

        if ($user && !empty($user) && isset($user[0]->password)) {
            error_log("Password hash from DB: " . substr($user[0]->password, 0, 30));
            
            // Verify password (supports hashed and legacy plaintext records).
            if ($this->verifyPasswordCompat($password, $user[0]->password)) {
                error_log("Password verification: SUCCESS");

                // Auto-upgrade legacy/plaintext password rows to bcrypt hash.
                if ($this->needsRehashOrPlaintext($user[0]->password)) {
                    $this->updateByNic($user[0]->nic, [
                        'password' => password_hash($password, PASSWORD_BCRYPT)
                    ]);
                }
                
                // Password is correct, start session
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $nic = $user[0]->nic;
                $_SESSION['user_id'] = $user[0]->user_id;
                $_SESSION['nic'] = $nic;
                
                error_log("Session user_id: " . $_SESSION['user_id']);
                error_log("Session nic: " . $_SESSION['nic']);

                // Allow canonical admin account even if admins row is missing.
                $isAdminAlias = strtolower((string) $user[0]->user_id) === 'admin'
                    || strtolower((string) $user[0]->nic) === 'admin'
                    || strtolower($username) === 'admin';

                if ($isAdminAlias) {
                    $_SESSION['user_type'] = 'admin';
                    $_SESSION['must_change_password'] = false;
                    error_log("User type: ADMIN (alias fallback)");
                    header('Location: ' . ROOT . '/adminDashboard');
                    exit();
                }

                // Determine user type by checking various tables
                // Check if admin
                $adminQuery = "SELECT * FROM admins WHERE nic = :nic LIMIT 1";
                $adminResult = $this->query($adminQuery, ['nic' => $nic]);
                
                if ($adminResult && !empty($adminResult)) {
                    $_SESSION['user_type'] = 'admin';
                    $_SESSION['must_change_password'] = false;
                    error_log("User type: ADMIN");
                    header('Location: ' . ROOT . '/adminDashboard');
                    exit();
                }
                
                // Check if coach
                $coachQuery = "SELECT * FROM coaches WHERE nic = :nic LIMIT 1";
                $coachResult = $this->query($coachQuery, ['nic' => $nic]);
                
                if ($coachResult && !empty($coachResult)) {
                    $_SESSION['user_type'] = 'coach';
                    $_SESSION['must_change_password'] = false;
                    error_log("User type: COACH");
                    header('Location: ' . ROOT . '/coachDashboard');
                    exit();
                }
                
                // Check if player
                $playerQuery = "SELECT player_id, role FROM players WHERE nic = :nic LIMIT 1";
                $playerResult = $this->query($playerQuery, ['nic' => $nic]);
                
                if ($playerResult && !empty($playerResult)) {
                    $_SESSION['user_type'] = 'player';
                    $_SESSION['player_id'] = (int)$playerResult[0]->player_id;
                    $playerRole = $playerResult[0]->role;
                    $_SESSION['player_role'] = $playerRole;
                    error_log("Player role: " . $playerRole);

                    $mustChangePassword = $this->verifyPasswordCompat('123456', $user[0]->password);
                    $_SESSION['must_change_password'] = $mustChangePassword;

                    if ($mustChangePassword) {
                        header('Location: ' . ROOT . '/PasswordChange');
                        exit();
                    }
                    
                    if ($this->isCaptainLikeRole($playerRole)) {
                        header('Location: ' . ROOT . '/captainDashboard');
                        exit();
                    }
                    
                    // Regular player
                    header('Location: ' . ROOT . '/playerDashboard');
                    exit();
                }
                
                // Default to player dashboard
                $_SESSION['user_type'] = 'player';
                $_SESSION['must_change_password'] = false;
                header('Location: ' . ROOT . '/playerDashboard');
                exit();
            } else {
                error_log("Password verification: FAILED");
            }
        } else {
            error_log("User validation failed - user empty or password not set");
        }
        
        error_log("Redirecting to login with error");
        header('Location: ' . ROOT . '/login?error=invalid_credentials');
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

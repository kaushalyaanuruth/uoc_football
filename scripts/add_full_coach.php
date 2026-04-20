<?php
require __DIR__ . '/../app/core/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $coach = [
        'nic' => '198745612345',
        'user_id' => '198745612345',
        'password' => '123456',
        'first_name' => 'Dilan',
        'last_name' => 'Rathnayaka',
        'email' => 'dilan.rathnayaka@uocfootball.lk',
        'phone_number' => '0712345678',
        'image' => null,
        'lane_1' => 'No 24, Temple Road',
        'lane_2' => 'Boralesgamuwa',
        'city' => 'Colombo',
        'district' => 'Colombo',
        'zip_code' => '10290',
        'license' => 'AFC-PRO-78421',
    ];

    $pdo->beginTransaction();

    $existingUserStmt = $pdo->prepare('SELECT nic FROM users WHERE nic = :nic LIMIT 1');
    $existingUserStmt->execute(['nic' => $coach['nic']]);
    $userExists = (bool) $existingUserStmt->fetchColumn();

    if ($userExists) {
        $updateUserStmt = $pdo->prepare(
            'UPDATE users
             SET user_id = :user_id,
                 password = :password,
                 first_name = :first_name,
                 last_name = :last_name,
                 email = :email,
                 phone_number = :phone_number,
                 image = :image,
                 lane_1 = :lane_1,
                 lane_2 = :lane_2,
                 city = :city,
                 district = :district,
                 zip_code = :zip_code
             WHERE nic = :nic'
        );

        $updateUserStmt->execute([
            'nic' => $coach['nic'],
            'user_id' => $coach['user_id'],
            'password' => password_hash($coach['password'], PASSWORD_BCRYPT),
            'first_name' => $coach['first_name'],
            'last_name' => $coach['last_name'],
            'email' => $coach['email'],
            'phone_number' => $coach['phone_number'],
            'image' => $coach['image'],
            'lane_1' => $coach['lane_1'],
            'lane_2' => $coach['lane_2'],
            'city' => $coach['city'],
            'district' => $coach['district'],
            'zip_code' => $coach['zip_code'],
        ]);

        $userAction = 'updated';
    } else {
        $insertUserStmt = $pdo->prepare(
            'INSERT INTO users (
                nic, user_id, password, first_name, last_name, email, phone_number,
                image, lane_1, lane_2, city, district, zip_code
             ) VALUES (
                :nic, :user_id, :password, :first_name, :last_name, :email, :phone_number,
                :image, :lane_1, :lane_2, :city, :district, :zip_code
             )'
        );

        $insertUserStmt->execute([
            'nic' => $coach['nic'],
            'user_id' => $coach['user_id'],
            'password' => password_hash($coach['password'], PASSWORD_BCRYPT),
            'first_name' => $coach['first_name'],
            'last_name' => $coach['last_name'],
            'email' => $coach['email'],
            'phone_number' => $coach['phone_number'],
            'image' => $coach['image'],
            'lane_1' => $coach['lane_1'],
            'lane_2' => $coach['lane_2'],
            'city' => $coach['city'],
            'district' => $coach['district'],
            'zip_code' => $coach['zip_code'],
        ]);

        $userAction = 'inserted';
    }

    $existingCoachStmt = $pdo->prepare('SELECT coach_id FROM coaches WHERE nic = :nic LIMIT 1');
    $existingCoachStmt->execute(['nic' => $coach['nic']]);
    $coachId = $existingCoachStmt->fetchColumn();

    if ($coachId) {
        $updateCoachStmt = $pdo->prepare(
            'UPDATE coaches SET license = :license WHERE coach_id = :coach_id'
        );
        $updateCoachStmt->execute([
            'license' => $coach['license'],
            'coach_id' => $coachId,
        ]);

        $coachAction = 'updated';
    } else {
        $insertCoachStmt = $pdo->prepare(
            'INSERT INTO coaches (license, nic) VALUES (:license, :nic)'
        );
        $insertCoachStmt->execute([
            'license' => $coach['license'],
            'nic' => $coach['nic'],
        ]);

        $coachId = (int) $pdo->lastInsertId();
        $coachAction = 'inserted';
    }

    $teamId = null;
    $teamColumns = $pdo->query('SHOW COLUMNS FROM teams')->fetchAll(PDO::FETCH_ASSOC);
    $hasStatus = false;
    foreach ($teamColumns as $column) {
        if (strtolower((string) ($column['Field'] ?? '')) === 'status') {
            $hasStatus = true;
            break;
        }
    }

    if ($hasStatus) {
        $teamIdStmt = $pdo->query(
            "SELECT team_id FROM teams WHERE status = 'present' ORDER BY team_id DESC LIMIT 1"
        );
        $teamId = $teamIdStmt->fetchColumn();
    }

    if (!$teamId) {
        $fallbackTeamStmt = $pdo->query('SELECT team_id FROM teams ORDER BY team_id DESC LIMIT 1');
        $teamId = $fallbackTeamStmt->fetchColumn();
    }

    $teamCoachAction = 'skipped';
    if ($teamId) {
        $linkExistsStmt = $pdo->prepare(
            'SELECT 1 FROM team_coaches WHERE team_id = :team_id AND coach_id = :coach_id LIMIT 1'
        );
        $linkExistsStmt->execute([
            'team_id' => $teamId,
            'coach_id' => $coachId,
        ]);

        if (!$linkExistsStmt->fetchColumn()) {
            $insertLinkStmt = $pdo->prepare(
                'INSERT INTO team_coaches (team_id, coach_id) VALUES (:team_id, :coach_id)'
            );
            $insertLinkStmt->execute([
                'team_id' => $teamId,
                'coach_id' => $coachId,
            ]);
            $teamCoachAction = 'inserted';
        } else {
            $teamCoachAction = 'already-linked';
        }
    }

    $pdo->commit();

    echo 'USER_ACTION: ' . $userAction . PHP_EOL;
    echo 'COACH_ACTION: ' . $coachAction . PHP_EOL;
    echo 'COACH_ID: ' . $coachId . PHP_EOL;
    echo 'TEAM_ID: ' . ($teamId ?: 'none') . PHP_EOL;
    echo 'TEAM_COACH_ACTION: ' . $teamCoachAction . PHP_EOL;
    echo 'LOGIN_NIC: ' . $coach['nic'] . PHP_EOL;
    echo 'LOGIN_PASSWORD: ' . $coach['password'] . PHP_EOL;
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

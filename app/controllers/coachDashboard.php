<?php

require_once __DIR__ . '/CoachBaseController.php';

class coachDashboard extends CoachBaseController {

    public function index() {
        $this->ensureCoachAccess();

        $eventModel = $this->model('EventModel');
        $upcomingRows = [];
        try {
            $upcomingRows = $eventModel->getUpcoming(3);
        } catch (Exception $e) {
            $upcomingRows = [];
        }

        $nextEvents = [];
        foreach ($upcomingRows as $event) {
            $dateValue = !empty($event->date) ? strtotime($event->date) : false;
            $timeValue = !empty($event->event_time) ? strtotime($event->event_time) : false;

            $dateText = $dateValue ? date('j F Y', $dateValue) : 'Date not set';
            $timeText = $timeValue ? date('g:i A', $timeValue) : 'TBA';
            $locationText = !empty($event->location) ? (string) $event->location : 'Location not set';

            $nextEvents[] = [
                'title' => !empty($event->title)
                    ? (string) $event->title
                    : ucfirst(strtolower((string) ($event->event_type ?? 'Event'))),
                'detail' => $dateText . ' - ' . $timeText . ' - ' . $locationText
            ];
        }

        if (empty($nextEvents)) {
            $nextEvents[] = [
                'title' => 'No upcoming events',
                'detail' => 'Events added by admin will appear here.'
            ];
        }

        $data = $this->buildCoachViewData([
            'next_events' => $nextEvents
        ]);
        $this->view('coachDashboard', $data);
    }

    public function updateProfile()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'], $_SESSION['nic']) || strtolower((string) ($_SESSION['user_type'] ?? '')) !== 'coach') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit();
        }

        try {
            $currentNic = $_SESSION['nic'];
            $firstName = trim((string) ($_POST['first_name'] ?? ''));
            $lastName = trim((string) ($_POST['last_name'] ?? ''));
            $idNumber = trim((string) ($_POST['id_number'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $phoneNumber = trim((string) ($_POST['phone_number'] ?? ''));

            if ($firstName === '' || $idNumber === '') {
                echo json_encode(['success' => false, 'message' => 'First name and ID number are required']);
                exit();
            }

            $userModel = $this->model('User');
            $coachModel = $this->model('CoachModel');

            $duplicateRows = $userModel->query(
                "SELECT nic FROM users WHERE user_id = :user_id AND nic != :nic LIMIT 1",
                [
                    'user_id' => $idNumber,
                    'nic' => $currentNic
                ]
            );

            if (!empty($duplicateRows)) {
                echo json_encode(['success' => false, 'message' => 'This ID number is already in use']);
                exit();
            }

            $newImagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . '/public/uploads/coaches/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileName = basename($_FILES['image']['name']);
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($fileExt, $allowedExts, true)) {
                    echo json_encode(['success' => false, 'message' => 'Invalid image format']);
                    exit();
                }

                $newFileName = uniqid('coach_profile_') . '.' . $fileExt;
                $uploadPath = $uploadDir . $newFileName;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
                    exit();
                }

                $newImagePath = 'uploads/coaches/' . $newFileName;
            }

            $updateFields = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'user_id' => $idNumber,
                'email' => $email,
                'phone_number' => $phoneNumber
            ];

            if (!empty($newImagePath)) {
                $updateFields['image'] = $newImagePath;
            }

            $userModel->updateByNic($currentNic, $updateFields);
            $_SESSION['user_id'] = $idNumber;

            $updatedRows = $coachModel->query(
                "SELECT u.first_name, u.last_name, u.user_id, u.email, u.phone_number, u.image
                 FROM users u
                 WHERE u.nic = :nic
                 LIMIT 1",
                ['nic' => $currentNic]
            );

            $updated = !empty($updatedRows) ? $updatedRows[0] : null;
            $fullName = trim((($updated->first_name ?? '') . ' ' . ($updated->last_name ?? '')));

            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully',
                'profile' => [
                    'name' => $fullName !== '' ? $fullName : ($updated->user_id ?? $idNumber),
                    'first_name' => $updated->first_name ?? $firstName,
                    'last_name' => $updated->last_name ?? $lastName,
                    'id_number' => $updated->user_id ?? $idNumber,
                    'email' => $updated->email ?? $email,
                    'phone_number' => $updated->phone_number ?? $phoneNumber,
                    'image_url' => $this->normalizeImageUrl($updated->image ?? $newImagePath, $fullName !== '' ? $fullName : ($updated->user_id ?? $idNumber))
                ]
            ]);
        } catch (Exception $e) {
            error_log('Coach profile update failed: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Profile update failed']);
        }

        exit();
    }

    public function profileData()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'], $_SESSION['nic']) || strtolower((string) ($_SESSION['user_type'] ?? '')) !== 'coach') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        $currentNic = (string) $_SESSION['nic'];
        $coachModel = $this->model('CoachModel');

        $rows = $coachModel->query(
            "SELECT u.first_name, u.last_name, u.user_id, u.nic, u.email, u.phone_number, u.image
             FROM users u
             WHERE u.nic = :nic
             LIMIT 1",
            ['nic' => $currentNic]
        );

        if (empty($rows)) {
            echo json_encode(['success' => false, 'message' => 'Profile not found']);
            exit();
        }

        $profile = $rows[0];
        $fullName = trim((($profile->first_name ?? '') . ' ' . ($profile->last_name ?? '')));

        echo json_encode([
            'success' => true,
            'profile' => [
                'name' => $fullName !== '' ? $fullName : ($profile->user_id ?? $currentNic),
                'first_name' => $profile->first_name ?? '',
                'last_name' => $profile->last_name ?? '',
                'id_number' => $profile->user_id ?? '',
                'nic' => $profile->nic ?? $currentNic,
                'email' => $profile->email ?? '',
                'phone_number' => $profile->phone_number ?? '',
                'image_url' => $this->normalizeImageUrl($profile->image ?? '', $fullName !== '' ? $fullName : ($profile->user_id ?? $currentNic))
            ]
        ]);
        exit();
    }
}


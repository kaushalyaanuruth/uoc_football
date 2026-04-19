<?php

class CaptainSchedule extends Controller
{
    private function buildInitialsAvatarUrl($displayName)
    {
        $name = trim((string) $displayName);
        if ($name === '') {
            $name = 'Captain';
        }

        $parts = preg_split('/\s+/', $name);
        $initials = '';
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            $initials .= strtoupper(substr($part, 0, 1));
            if (strlen($initials) >= 2) {
                break;
            }
        }

        if ($initials === '') {
            $initials = 'C';
        }

        $palette = ['#4f46e5', '#0ea5e9', '#059669', '#d97706', '#dc2626', '#7c3aed'];
        $color = $palette[abs(crc32($name)) % count($palette)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="320" height="320" viewBox="0 0 320 320">'
            . '<rect width="320" height="320" fill="' . $color . '"/>'
            . '<text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle" '
            . 'font-family="Arial, Helvetica, sans-serif" font-size="120" font-weight="700" fill="#ffffff">'
            . htmlspecialchars($initials, ENT_QUOTES, 'UTF-8')
            . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }

    private function normalizeImageUrl($imagePath, $displayName = '')
    {
        if (empty($imagePath)) {
            return $this->buildInitialsAvatarUrl($displayName);
        }

        $normalized = str_replace('\\', '/', ltrim((string) $imagePath, '/'));
        return ROOT . '/' . $normalized;
    }

    private function isCaptainLikeRole($role)
    {
        $normalized = strtolower(trim((string) $role));
        $normalized = str_replace(['_', ' '], '-', $normalized);

        return in_array($normalized, ['captain', 'vice-captain', 'vicecaptain'], true);
    }

    private function ensureCaptainAccess()
    {
        if (!isset($_SESSION['user_id'], $_SESSION['nic'])) {
            header('Location: ' . ROOT . '/login');
            exit();
        }

        $userType = strtolower((string) ($_SESSION['user_type'] ?? ''));
        $playerRole = (string) ($_SESSION['player_role'] ?? '');

        $isCaptain = $userType === 'captain' || ($userType === 'player' && $this->isCaptainLikeRole($playerRole));
        if (!$isCaptain) {
            header('Location: ' . ROOT . '/login');
            exit();
        }
    }

    private function getPlayerImageBySession()
    {
        $nic = $_SESSION['nic'] ?? '';
        if ($nic === '') {
            return $this->normalizeImageUrl('', 'Captain');
        }

        $playerModel = $this->model('PlayerModel');
        $rows = $playerModel->query(
            "SELECT u.image, u.first_name, u.last_name, u.user_id FROM users u WHERE u.nic = :nic LIMIT 1",
            ['nic' => $nic]
        );

        $row = $rows[0] ?? null;
        $fullName = trim((($row->first_name ?? '') . ' ' . ($row->last_name ?? '')));
        if ($fullName === '') {
            $fullName = (string) ($row->user_id ?? $nic);
        }

        return $this->normalizeImageUrl($row->image ?? '', $fullName);
    }

    private function getCaptainNotices($limit = 6)
    {
        $noticeModel = $this->model('NoticeModel');
        $rows = [];

        try {
            $rows = $noticeModel->getRecent($limit, 'present_team');
        } catch (Exception $e) {
            $rows = [];
        }

        $notices = [];
        foreach ($rows as $row) {
            $notices[] = [
                'id' => (int) ($row->notice_id ?? 0),
                'title' => $row->title ?? 'Notice',
                'content' => $row->content ?? '',
                'author' => $row->created_by ?? 'Admin',
                'date' => !empty($row->created_at) ? date('M d, Y h:i A', strtotime($row->created_at)) : '',
            ];
        }

        return $notices;
    }

    public function index()
    {
        $this->ensureCaptainAccess();

        $eventModel = $this->model('EventModel');
        $upcomingEvents = $eventModel->getUpcoming();

        $events = [];
        foreach ($upcomingEvents as $event) {
            $typeRaw = strtolower((string) ($event->event_type ?? 'other'));
            $typeLabel = ucfirst($typeRaw);
            if ($typeRaw === 'match') {
                $typeLabel = 'Match';
            } elseif ($typeRaw === 'training') {
                $typeLabel = 'Training';
            }

            $dateText = !empty($event->date) ? date('F d, Y', strtotime($event->date)) : 'Date not set';
            $timeText = !empty($event->event_time) ? date('g:i A', strtotime($event->event_time)) : 'Time not set';

            $events[] = [
                'id' => (int) ($event->event_id ?? 0),
                'title' => $event->title ?? 'UOC Football Event',
                'type' => $typeLabel,
                'type_key' => $typeRaw,
                'date' => $dateText,
                'time' => $timeText,
                'location' => $event->location ?? 'Ground'
            ];
        }

        $profileImage = $this->getPlayerImageBySession();
        $data = [
            'events' => $events,
            'captain_image' => $profileImage,
            'player_image' => $profileImage,
            'notices' => $this->getCaptainNotices(),
        ];

        $this->view('captain/schedule', $data);
    }
}

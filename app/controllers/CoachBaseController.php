<?php

class CoachBaseController extends Controller
{
    protected function ensureCoachAccess()
    {
        if (!isset($_SESSION['user_id'], $_SESSION['nic'])) {
            header('Location: ' . ROOT . '/login');
            exit();
        }

        if (strtolower((string) ($_SESSION['user_type'] ?? '')) !== 'coach') {
            header('Location: ' . ROOT . '/login');
            exit();
        }
    }

    protected function buildInitialsAvatarUrl($displayName)
    {
        $name = trim((string) $displayName);
        if ($name === '') {
            $name = 'Coach';
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

    protected function normalizeImageUrl($imagePath, $displayName = '')
    {
        if (empty($imagePath)) {
            return $this->buildInitialsAvatarUrl($displayName);
        }

        $normalized = str_replace('\\', '/', ltrim((string) $imagePath, '/'));
        return ROOT . '/' . $normalized;
    }

    protected function getCoachIdentity()
    {
        $coachModel = $this->model('CoachModel');
        $nic = (string) ($_SESSION['nic'] ?? '');

        $rows = $coachModel->query(
            "SELECT c.coach_id, c.license, c.nic, u.user_id, u.first_name, u.last_name, u.email, u.phone_number, u.image
             FROM coaches c
             JOIN users u ON u.nic = c.nic
             WHERE c.nic = :nic
             LIMIT 1",
            ['nic' => $nic]
        );

        if (empty($rows)) {
            $fallback = new stdClass();
            $fallback->coach_id = null;
            $fallback->license = '';
            $fallback->nic = $nic;
            $fallback->user_id = (string) ($_SESSION['user_id'] ?? 'coach');
            $fallback->first_name = 'Coach';
            $fallback->last_name = '';
            $fallback->email = '';
            $fallback->phone_number = '';
            $fallback->image = '';

            return [
                'coach' => $fallback,
                'name' => 'Coach',
                'image' => $this->normalizeImageUrl('', 'Coach')
            ];
        }

        $coach = $rows[0];
        $fullName = trim((($coach->first_name ?? '') . ' ' . ($coach->last_name ?? '')));
        if ($fullName === '') {
            $fullName = $coach->nic;
        }

        return [
            'coach' => $coach,
            'name' => $fullName,
            'image' => $this->normalizeImageUrl($coach->image ?? '', $fullName)
        ];
    }

    protected function getCoachNotices($limit = 6)
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
                'title' => $row->title ?? 'Notice',
                'content' => $row->content ?? '',
                'author' => $row->created_by ?? 'Admin',
                'date' => !empty($row->created_at) ? date('M d, Y h:i A', strtotime($row->created_at)) : ''
            ];
        }

        if (empty($notices)) {
            $notices[] = [
                'title' => 'No notices yet',
                'content' => 'Admin notices for present team members will appear here.',
                'author' => 'System',
                'date' => ''
            ];
        }

        return $notices;
    }

    protected function buildCoachViewData($extra = [])
    {
        $identity = $this->getCoachIdentity();
        if ($identity === null) {
            header('Location: ' . ROOT . '/login?error=invalid_credentials');
            exit();
        }

        $notices = $this->getCoachNotices(6);

        $base = [
            'coach_name' => $identity['name'],
            'coach_image' => $identity['image'],
            'coach_profile' => [
                'first_name' => $identity['coach']->first_name ?? '',
                'last_name' => $identity['coach']->last_name ?? '',
                'id_number' => $identity['coach']->user_id ?? ($identity['coach']->nic ?? ''),
                'nic' => $identity['coach']->nic ?? '',
                'email' => $identity['coach']->email ?? '',
                'phone_number' => $identity['coach']->phone_number ?? '',
                'license' => $identity['coach']->license ?? ''
            ],
            'notices' => $notices
        ];

        return array_merge($base, $extra);
    }
}

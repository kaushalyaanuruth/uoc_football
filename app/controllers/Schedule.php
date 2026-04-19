<?php

class Schedule extends Controller
{
    private function normalizeImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return ROOT . '/assets/images/adminDashboard/header/avatar.jpg';
        }

        $normalized = str_replace('\\', '/', ltrim((string)$imagePath, '/'));
        return ROOT . '/' . $normalized;
    }

    private function getPlayerImageBySession()
    {
        $nic = $_SESSION['nic'] ?? '';
        if ($nic === '') {
            return $this->normalizeImageUrl('');
        }

        $playerModel = $this->model('PlayerModel');
        $rows = $playerModel->query(
            "SELECT u.image FROM users u WHERE u.nic = :nic LIMIT 1",
            ['nic' => $nic]
        );

        return $this->normalizeImageUrl($rows[0]->image ?? '');
    }

    private function getPlayerNotices($limit = 6)
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
        if (!isset($_SESSION['user_id'], $_SESSION['nic'])) {
            header('Location: ' . ROOT . '/login');
            exit();
        }

        if (($_SESSION['user_type'] ?? '') !== 'player') {
            header('Location: ' . ROOT . '/login');
            exit();
        }

        $eventModel = $this->model('EventModel');
        $upcomingEvents = $eventModel->getUpcoming();

        $events = [];
        foreach ($upcomingEvents as $event) {
            $typeRaw = strtolower((string)($event->event_type ?? 'other'));
            $typeLabel = ucfirst($typeRaw);
            if ($typeRaw === 'match') {
                $typeLabel = 'Match';
            } elseif ($typeRaw === 'training') {
                $typeLabel = 'Training';
            }

            $dateText = !empty($event->date) ? date('F d, Y', strtotime($event->date)) : 'Date not set';
            $timeText = !empty($event->event_time) ? date('g:i A', strtotime($event->event_time)) : 'Time not set';

            $events[] = [
                'id' => (int)($event->event_id ?? 0),
                'title' => $event->title ?? 'UOC Football Event',
                'type' => $typeLabel,
                'type_key' => $typeRaw,
                'date' => $dateText,
                'time' => $timeText,
                'location' => $event->location ?? 'Ground'
            ];
        }

        $data = [
            'events' => $events,
            'player_image' => $this->getPlayerImageBySession(),
            'notices' => $this->getPlayerNotices(),
        ];

        $this->view('schedule', $data);
    }
}

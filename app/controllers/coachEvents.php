<?php

require_once __DIR__ . '/CoachBaseController.php';

class coachEvents extends CoachBaseController {

    public function index() {
        $this->ensureCoachAccess();

        $eventModel = $this->model('EventModel');
        $upcomingEvents = [];
        try {
            $upcomingEvents = $eventModel->getUpcoming();
        } catch (Exception $e) {
            $upcomingEvents = [];
        }

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

        $this->view('coachEvents', $this->buildCoachViewData([
            'events' => $events
        ]));
    }
}


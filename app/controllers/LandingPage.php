<?php
class LandingPage extends Controller {

    private function buildPersonCard($row, $roleLabel)
    {
        if (!$row) {
            return [
                'name' => 'Not Assigned',
                'role' => $roleLabel,
                'image' => '',
                'initials' => 'NA'
            ];
        }

        $fullName = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
        if ($fullName === '') {
            $fullName = $row->nic ?? 'Unknown';
        }

        $parts = preg_split('/\s+/', $fullName);
        $initials = '';
        foreach ($parts as $part) {
            if ($part !== '') {
                $initials .= strtoupper(substr($part, 0, 1));
            }
            if (strlen($initials) >= 2) {
                break;
            }
        }
        if ($initials === '') {
            $initials = 'NA';
        }

        return [
            'name' => $fullName,
            'role' => $roleLabel,
            'image' => !empty($row->image) ? ROOT . '/' . str_replace('\\', '/', ltrim($row->image, '/')) : '',
            'initials' => $initials
        ];
    }

    public function index() {
        $newsModel = $this->model('NewsModel');
        $eventModel = $this->model('EventModel');
        $galleryModel = $this->model('GalleryModel');
        $teamModel = $this->model('TeamModel');
        $playerModel = $this->model('PlayerModel');
        $coachModel = $this->model('CoachModel');
        
        // Fetch latest news
        $latestNews = $newsModel->getAll();
        
        // Fetch upcoming events
        $upcomingEvents = $eventModel->getUpcoming();
        
        // Fetch latest gallery images (limit to 6)
        $allGalleryImages = $galleryModel->getPublished();
        $latestGalleryImages = array_slice($allGalleryImages, 0, 6);

        $presentTeams = $teamModel->getByStatus('present');
        $activeTeam = !empty($presentTeams) ? $presentTeams[0] : null;

        $captainRow = null;
        $viceCaptainRow = null;
        $coachRow = null;

        if ($activeTeam) {
            $playerRows = $playerModel->query(
                "SELECT p.role, p.nic, u.first_name, u.last_name, u.image
                 FROM players p
                 JOIN team_players tp ON tp.player_id = p.player_id
                 LEFT JOIN users u ON u.nic = p.nic
                 WHERE tp.team_id = :team_id",
                ['team_id' => $activeTeam->team_id]
            );

            foreach ($playerRows as $playerRow) {
                if (!$captainRow && strcasecmp((string)$playerRow->role, 'Captain') === 0) {
                    $captainRow = $playerRow;
                    continue;
                }

                if (!$viceCaptainRow && strcasecmp((string)$playerRow->role, 'Vice-Captain') === 0) {
                    $viceCaptainRow = $playerRow;
                }
            }

            $coachRows = $coachModel->query(
                "SELECT c.nic, c.license, u.first_name, u.last_name, u.image
                 FROM coaches c
                 JOIN team_coaches tc ON tc.coach_id = c.coach_id
                 LEFT JOIN users u ON u.nic = c.nic
                 WHERE tc.team_id = :team_id
                 ORDER BY c.coach_id ASC
                 LIMIT 1",
                ['team_id' => $activeTeam->team_id]
            );

            if (!empty($coachRows)) {
                $coachRow = $coachRows[0];
            }
        }

        $landingTeamCards = [
            $this->buildPersonCard($coachRow, 'Coach'),
            $this->buildPersonCard($captainRow, 'Captain'),
            $this->buildPersonCard($viceCaptainRow, 'Vice Captain')
        ];
        
        $data = [
            'latestNews' => $latestNews,
            'upcomingEvents' => $upcomingEvents,
            'latestGalleryImages' => $latestGalleryImages,
            'landingTeamCards' => $landingTeamCards
        ];
        
        $this->view('landingPage', $data);
    }
}

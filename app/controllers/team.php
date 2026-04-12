<?php
class team extends Controller {

    public function index() {
        // Load PlayerModel
        $playerModel = $this->model('PlayerModel');
        
        // Get all players
        $players = $playerModel->getAll();
        
        // Organize players by position
        $organizedPlayers = [
            'goalkeeper' => [],
            'defender' => [],
            'midfielder' => [],
            'forward' => [],
            'striker' => []
        ];
        
        if ($players) {
            foreach ($players as $player) {
                $position = strtolower($player->position ?? 'forward');
                // Normalize position names
                if (strpos($position, 'goal') !== false) {
                    $organizedPlayers['goalkeeper'][] = $player;
                } elseif (strpos($position, 'defend') !== false) {
                    $organizedPlayers['defender'][] = $player;
                } elseif (strpos($position, 'mid') !== false) {
                    $organizedPlayers['midfielder'][] = $player;
                } elseif (strpos($position, 'strike') !== false || strpos($position, 'forward') !== false) {
                    $organizedPlayers['striker'][] = $player;
                } else {
                    $organizedPlayers['forward'][] = $player;
                }
            }
        }
        
        $data = [
            'players' => $organizedPlayers
        ];
        
        $this->view('team', $data);
    }
}


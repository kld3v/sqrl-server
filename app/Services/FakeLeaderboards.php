<?php

namespace App\Services;

class FakeLeaderboards
{
    protected $usernames = [
        // Example list of usernames
        'user1', 'user2', 'user3', 'user4', 'user5','user6','user7','user8','user9','user10','user11','user12','user13','user14','user15','user16','user17','user18','user19','user20','user21','user22'
    ];

    public function generateLeaderboard(int $count)
    {
        $selectedUsernames = $this->selectUniqueUsernames($this->usernames, $count);

        $leaderboardData = [];
        foreach ($selectedUsernames as $username) {
            $leaderboardData[] = [
                'username' => $username,
                'score' => 0,
                'mult' => mt_rand(0, 2),
            ];
        }

        return $leaderboardData;
    }

    protected function selectUniqueUsernames(array $usernames, int $count)
    {
        if ($count > count($usernames)) {
            throw new \Exception("Requested more usernames than available in the list.");
        }

        shuffle($usernames);
        return array_slice($usernames, 0, $count);
    }
}

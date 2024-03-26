<?php

namespace App\Services\FakeLeaderboards;

class FakeLeaderboards
{

    protected $multipliers = [2.68, 2.63, 2.35, 2.17, 1.93, 1.87, 1.82, 1.67, 1.56, 1.52, 1.46, 1.39, 1.86, 1.28, 1.25, 1.19, 1.93, 0.9, 1.89];
    protected $adjectives = [
        "Adventurous", "Artistic", "Bold", "Charming", "Crafty", "Daring", "Elegant", "Fiesty", "Graceful", "Heroic",
        "Inventive", "Jovial", "Keen", "Luminous", "Majestic", "Nimble", "Optimistic", "Peaceful", "Quirky", "Radiant",
        "Savage", "Thrifty", "Unstoppable", "Vibrant", "Wise", "Xenial", "Youthful", "Zealous", "Blissful", "Curious",
        "Dreamy", "Energetic", "Fearless", "Glorious", "Harmonious", "Idealistic", "Joyful", "Karmic", "Legendary",
        "Mystical", "Noble", "Outgoing", "Powerful", "Quintessential", "Resourceful", "Spiritual", "Tenacious", "Unique",
        "Valiant", "Whimsical"
    ];
    protected $insults = [
        "Bumbling", "Clueless", "Dopey", "Fickle", "Grouchy", "Hapless", "Irritable", "Jittery", "Klutzy", "Loopy",
        "Mopey", "Nerdy", "Obnoxious", "Peevish", "Quarrelsome", "Rowdy", "Snarky", "Twitchy", "Unruly", "Vain",
        "Wacky", "Xenophobic", "Yappy", "Zany", "Blockheaded", "Cowardly", "Dimwitted", "Egotistical", "Frivolous",
        "Greedy", "Hotheaded", "Impulsive", "Jealous", "Loudmouth", "Moody", "Nitpicky", "Overbearing",
        "Pompous", "Quixotic", "Reckless", "Stubborn", "Tactless", "Undisciplined", "Voluble", "Whiny", "Exasperating",
        "Yokelish", "Zealot", "Shortsighted"
    ];
    
    protected $symbols = ["_", "-"];


    public function generateLeaderboard(int $count)
    {
        $selectedUsernames = $this->generateUniqueUsernames($count);

        $leaderboardData = [];
        $multiplierIndex = 0;
        foreach ($selectedUsernames as $username) {
            $leaderboardData[] = [
                'username' => $username,
                'score' => mt_rand(0, 3),
                'mult' => $this->multipliers[$multiplierIndex],
            ];

            $multiplierIndex = ($multiplierIndex + 1) % count($this->multipliers);
        }

        return $leaderboardData;
    }

    protected function generateUniqueUsernames(int $count)
    {
        $usernames = [];
        while (count($usernames) < $count) {
            $username = $this->randomUsernameGenerator();
            if (!in_array($username, $usernames)) {
                $usernames[] = $username;
            }
        }
        return $usernames;
    }

    protected function loadRandomLine($fileName) {
        $filePath = __DIR__ . '/' . $fileName;

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines) {
            return $lines[array_rand($lines)];
        }
        return '';
    }

    protected function randomUsernameGenerator()
    {
        $numbers = range(1, 999);

        $firstName = $this->loadRandomLine('first-names.txt');
        $secondName = $this->loadRandomLine('surnames.txt');
        $adjective = $this->adjectives[array_rand($this->adjectives)];
        $insult = $this->insults[array_rand($this->insults)];
        $symbol = $this->symbols[array_rand($this->symbols)];
        $number = (string)$numbers[array_rand($numbers)];

        $formats = [
            $firstName . $secondName,
            $firstName . $secondName . $number,
            $secondName . $firstName,
            $secondName . $firstName . $number,
            $adjective . $firstName,
            $adjective . $secondName,
            $insult . $firstName,
            $insult . $secondName,
            $firstName . $symbol . $secondName,
            $secondName . $symbol . $firstName,
            $firstName . $adjective,
            $secondName . $adjective,
            $adjective . $firstName . $number,
            $adjective . $secondName . $number,
            $insult . $firstName . $number,
            $insult . $secondName . $number,
            $firstName . $symbol . $secondName . $number,
            $secondName . $symbol . $firstName . $number,
            $firstName . $adjective . $number,
            $secondName . $adjective . $number,
        ];

        return strtolower($formats[array_rand($formats)]);
    }
}

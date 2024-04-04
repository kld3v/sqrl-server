<?php

namespace App\Services;
//this file was added by nathan
//use this for scoring system as a weighted sum:
class TrustScoreResult
{
    private $reasons;

    public function __construct(
        private int $score = 1000, private float $weight = 1
    ) {
        $this->reasons = [];
    }

    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    public function setScore($score, $reason = null)
    {
        $this->score = $score;
        if ($reason)
            $this->addReason($reason);
    }

    public function addReason(string $reason)
    {
        $this->reasons[] = $reason;
    }

    public function addReasons($reasons = [])
    {
        $this->reasons = array_merge($this->reasons, $reasons);
    }

    public function fails()
    {
        return !$this->score;
    }

    public function getReasons()
    {
        return $this->reasons;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function getWeight()
    {
        return $this->weight;
    }


    public function getWeightedScore()
    {
        return $this->score * $this->weight;
    }
}
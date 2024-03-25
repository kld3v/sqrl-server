<?php

namespace App\Http\Controllers;

use App\Services\FakeLeaderboards;
use Illuminate\Http\Request;

class FakeLeaderboardController extends Controller
{
    public function index()
    {
        $leaderboardService = new FakeLeaderboards();
        $data = $leaderboardService->generateLeaderboard(19); 

        return response()->json($data);
    }
}

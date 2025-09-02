<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Inertia\Inertia;
use App\Models\Player;
use App\Models\Batting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $team = $user->team;

        $submittedOrder = Batting::with(['team', 'battingOrders', 'opponent'])->get();

        return Inertia::render('Dashboard', [
            'auth' => ['user' => $user],
            'team' => $team ? $team->load('players') : null,
            'opponents' => $team ? Team::where('id', '!=', $team->id)->get() : [],
            'submittedOrders' => $submittedOrder,
            'stats' => [
                'teams' => Team::count(),
                'players' => Player::count(),
                'orders' => Batting::count(),
                'pending' => Batting::where('status', 'pending')->count(),
            ]
        ]);
    }
}

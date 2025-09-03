<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Team/Index', [
            'teams' => Team::with('players')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Team/Form', [
            'managers' => User::where('role', 'manager')->whereDoesntHave('team')->get(),
            'mode' => 'create',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'logo' => 'nullable|image|max:2048',
            'id_manager' => 'required|exists:users,id',
            'team_name' => 'required|string|max:255',
            'head_coach' => 'required|string|max:255',
            'coach' => 'nullable|string|max:255',
            'players' => 'required|array|min:1',
            'players.*.full_name' => 'required|string|max:255',
            'players.*.position' => 'required|string|max:255',
            'players.*.back_number' => 'required|numeric',
            'players.*.birth_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, &$team) {
            $data = $request->only(['logo', 'id_manager', 'team_name', 'head_coach', 'coach']);

            if ($request->hasFile('logo')) {
                $data['logo'] = $request->file('logo')->store('logos', 'public');
            }

            $team = Team::create($data);

            foreach ($request->players as $p) {
                $team->players()->create([
                    'full_name' => $p['full_name'],
                    'position' => $p['position'],
                    'back_number' => $p['back_number'],
                    'birth_date' => $p['birth_date'],
                ]);
            }
        });

        return redirect()->route('team.index')->with('success', 'Team created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $team->load('players');

        return Inertia::render('Team/Form', [
            'team' => $team,
            'teamPlayers' => $team->players,
            'managers' => User::where('role', 'manager')->get(),
            'mode' => 'detail',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $team->load('players');

        return Inertia::render('Team/Form', [
            'team' => $team,
            'teamPlayers' => $team->players,
            'managers' => User::where('role', 'manager')->get(),
            'mode' => 'edit',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        // Ensure players array is present
        $requestData = $request->all();
        $players = $requestData['players'] ?? [];

        $validated = $request->validate([
            'team_name' => 'required|string|max:255',
            'head_coach' => 'required|string|max:255',
            'coach' => 'nullable|string|max:255',
            'id_manager' => 'required|exists:users,id',
            'players' => 'required|array|min:1',
            'players.*.full_name' => 'required|string|max:255',
            'players.*.position' => 'required|string|max:255',
            'players.*.back_number' => 'required|numeric',
            'players.*.birth_date' => 'nullable|date',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $filename = $team->id . '.' . $logoFile->getClientOriginalExtension();

            $path = $logoFile->storeAs('logos', $filename, 'public');

            $validated['logo'] = URL::to('/storage/' . $path);
        } else {
            unset($validated['logo']);
        }


        $team->update($validated);

        // Update or create players
        foreach ($players as $p) {
            $team->players()->updateOrCreate(
                ['id' => $p['id'] ?? null],
                [
                    'full_name' => $p['full_name'],
                    'position' => $p['position'],
                    'back_number' => $p['back_number'],
                    'birth_date' => $p['birth_date'],
                ]
            );
        }

        return redirect()->route('team.index')->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $team->delete();

        return redirect()->route('team.index')->with('success', 'Team deleted successfully.');
    }
}

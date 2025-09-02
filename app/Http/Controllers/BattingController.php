<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Inertia\Inertia;
use App\Models\Player;
use App\Models\Batting;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BattingController extends Controller
{
    /**
     * Display a listing of the batting orders.
     */
    public function index()
    {
        $battingOrders = Batting::with(['team', 'opponent', 'battingOrders.player'])
            ->latest()
            ->get();

        return Inertia::render('Batting/Index', [
            'battings' => $battingOrders
        ]);
    }

    /**
     * Show the form for creating a new batting order.
     */
    public function create()
    {
        return Inertia::render('Batting/Form', [
            'teams' => Team::all(),
            'mode' => 'create'
        ]);
    }

    /**
     * Store a newly created batting order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'opponent_id' => 'required|exists:teams,id',
            'game_date' => 'required|date',
            'players' => 'required|array',
            'players.*.id' => 'required|exists:players,id',
            'players.*.batting_position' => 'required|integer|min:1',
        ]);

        $batting = Batting::create([
            'team_id' => $validated['team_id'],
            'opponent_id' => $validated['opponent_id'],
            'game_date' => $validated['game_date'],
            'status' => 'pending',
        ]);

        foreach ($validated['players'] as $player) {
            $batting->battingOrders()->create([
                'id_player' => $player['id'],
                'batting_position' => $player['batting_position'],
            ]);
        }
        $batting->load(['team', 'opponent']);

        $whatsApp = new WhatsAppService();
        $panitia = User::where('role', 'panitia')->get();
        $message = 'Tim ' . $batting->team->team_name . ' telah menambahkan batting order melawan ' . $batting->opponent->team_name . '. Silakan untuk mengunjungi link berikut: ' . route('batting.index');

        foreach ($panitia as $user) {
            if (!empty($user->phone)) {
                $phone = $user->phone;
                if (strpos($phone, '08') === 0) {
                    $phone = '+62' . substr($phone, 1);
                }
                $whatsApp->sendMessage($phone, $message);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Batting order submitted successfully.');
    }

    /**
     * Display the specified batting order.
     */
    public function show(Batting $batting)
    {
        $batting->load(['team', 'opponent', 'orders.player']);

        return Inertia::render('Batting/Show', [
            'batting' => $batting
        ]);
    }

    /**
     * Show the form for editing the specified batting order.
     */
    public function edit(Batting $batting)
    {
        $batting->load(['orders.player']);

        return Inertia::render('Batting/Form', [
            'batting' => $batting,
            'teams' => Team::all(),
            'mode' => 'edit'
        ]);
    }

    /**
     * Update the specified batting order.
     */
    public function update(Request $request, $batting)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'opponent_id' => 'required|exists:teams,id',
            'game_date' => 'required|date',
            'players' => 'required|array|min:1',
            'players.*.id' => 'required|exists:players,id',
            'players.*.batting_position' => 'required|min:1',
        ]);

        $batting = Batting::findOrFail($batting)->load(['team', 'opponent']);

        DB::transaction(function () use ($batting, $validated) {
            // update batting main info
            $batting->update([
                'team_id' => $validated['team_id'],
                'opponent_id' => $validated['opponent_id'],
                'game_date' => $validated['game_date'],
            ]);

            // clear old orders
            $batting->battingOrders()->delete();

            // re-create orders
            foreach ($validated['players'] as $player) {
                $batting->battingOrders()->create([
                    'id_player'       => $player['id'],
                    'batting_position' => $player['batting_position'],
                ]);
            }
        });

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Batting order updated successfully.']);
        }
        $whatsApp = new WhatsAppService();
        $panitia = User::where('role', 'panitia')->get();
        $message = 'Tim ' . $batting->team->team_name . ' telah memperbarui batting order melawan ' . $batting->opponent->team_name . '. Silakan untuk mengunjungi link berikut: ' . route('batting.index');

        foreach ($panitia as $user) {
            if (!empty($user->phone)) {
                $phone = $user->phone;
                if (strpos($phone, '08') === 0) {
                    $phone = '+62' . substr($phone, 1);
                }
                $whatsApp->sendMessage($phone, $message);
            }
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Batting order updated successfully.');
    }
    /**
     * Remove the specified batting order.
     */
    public function destroy(Batting $batting)
    {
        $batting->delete();

        return redirect()->route('batting.index')->with('success', 'Batting order deleted successfully.');
    }

    /**
     * Approve batting order (for panitia).
     */
    public function approve($batting)
    {
        $batting = Batting::findOrFail($batting);
        $batting->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Batting order approved.');
    }

    /**
     * Reject batting order (for panitia).
     */
    public function reject($batting)
    {
        $batting = Batting::findOrFail($batting);

        $batting->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Batting order rejected.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JackpotWinner;
use App\Models\Jackpot;
use App\Models\GameTable;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JackpotWinnersExport;


class JackpotWinnerController extends Controller
{
    public function index(Request $request)
    {
        // Handle search query
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');

        // Handle date range filters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Handle search, sort, and date filtering
        $winners = JackpotWinner::with(['jackpot', 'gameTable', 'settledBy'])
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('jackpot', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                })
                    ->orWhereHas('gameTable', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('settledBy', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhere('sensor_number', 'like', "%$search%")
                    ->orWhere('win_amount', 'like', "%$search%");
            })
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('created_at', '<=', $endDate);
            })
            ->orderBy($sortBy, $sortDirection)
            ->paginate(10); // Adjust pagination as needed

        // Handle Excel export
        if ($request->has('export') && $request->export == 'excel') {
            return Excel::download(new JackpotWinnersExport($winners), 'jackpot_winners.xlsx');
        }

        return view('jackpot_winners.index', compact('winners'));
    }

    public function create()
    {
        $jackpots = Jackpot::all();
        $gameTables = GameTable::all();
        return view('jackpot_winners.create', compact('jackpots', 'gameTables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jackpot_id' => 'required|exists:jackpots,id',
            'game_table_id' => 'required|exists:game_tables,id',
            'table_name' => 'required|string|max:255',
            'sensor_number' => 'required|integer',
            'win_amount' => 'required|numeric',
            'is_settled' => 'boolean',
            'deduction_source' => 'required|string|max:255',
        ]);

        JackpotWinner::create($request->all());

        return redirect()->route('jackpot_winners.index')
            ->with('success', 'Jackpot Winner created successfully.');
    }

    public function edit(JackpotWinner $jackpotWinner)
    {
        $jackpots = Jackpot::all();
        $gameTables = GameTable::all();
        return view('jackpot_winners.edit', compact('jackpotWinner', 'jackpots', 'gameTables'));
    }

    public function update(Request $request, JackpotWinner $jackpotWinner)
    {
        $request->validate([
            'jackpot_id' => 'required|exists:jackpots,id',
            'game_table_id' => 'required|exists:game_tables,id',
            'table_name' => 'required|string|max:255',
            'sensor_number' => 'required|integer',
            'win_amount' => 'required|numeric',
            'is_settled' => 'boolean',
            'deduction_source' => 'required|string|max:255',
        ]);

        $jackpotWinner->update($request->all());

        return redirect()->route('jackpot_winners.index')
            ->with('success', 'Jackpot Winner updated successfully.');
    }

    public function destroy(JackpotWinner $jackpotWinner)
    {
        $jackpotWinner->delete();

        return redirect()->route('jackpot_winners.index')
            ->with('success', 'Jackpot Winner deleted successfully.');
    }

    // Function to update the 'is_settled' field
    public function settle($id, Request $request): JsonResponse
    {
        // Validate the input
        $validated = $request->validate([
            'is_settled' => 'required|boolean',
            'pin' => 'required|string',
        ]);

        // Check if any user exists with the given pin
        $user = User::where('pin', $validated['pin'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid pin.',
            ], 403);
        }

        // Find the JackpotWinner by ID
        $jackpotWinner = JackpotWinner::findOrFail($id);

        // Update the 'is_settled' field and set 'settled_by' as "Pit Manager"
        $jackpotWinner->is_settled = $validated['is_settled'];
        $jackpotWinner->settled_by = 1;  // Static value for settled_by
        $jackpotWinner->save();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Jackpot winner settlement status updated successfully.',
            'jackpot_winner' => $jackpotWinner
        ]);
    }

    // Function to get all unsettled jackpot winners
    public function getUnsettledWinners(): JsonResponse
    {
        // Retrieve all records where 'is_settled' is 0
        $unsettledWinners = JackpotWinner::where('is_settled', 0)->get();

        // Return the records as a JSON response
        return response()->json([
            'success' => true,
            'data' => $unsettledWinners
        ]);
    }
}

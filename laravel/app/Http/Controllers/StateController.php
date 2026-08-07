<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexStateRequest;
use App\Models\State;
use App\Services\InegiStateCatalog;
use Illuminate\Http\JsonResponse;

class StateController extends Controller
{
    public function summary(InegiStateCatalog $catalog): JsonResponse
    {
        return response()->json([
            'total' => State::query()->count(),
            'source' => $catalog->source(),
        ]);
    }

    public function index(IndexStateRequest $request): JsonResponse
    {
        $total = State::query()->count();
        $states = State::query()->matching($request->search());
        $filtered = $states->count();

        return response()->json([
            'draw' => $request->draw(),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $states
                ->orderBy($request->sortColumn(), $request->sortDirection())
                ->offset($request->start())
                ->limit($request->length())
                ->get(['state_code', 'name', 'short_name', 'total_population']),
        ]);
    }
}

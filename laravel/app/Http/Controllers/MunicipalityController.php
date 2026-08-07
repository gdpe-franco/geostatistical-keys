<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Services\InegiMunicipalityCatalog;
use Illuminate\Http\JsonResponse;

class MunicipalityController extends Controller
{
    public function index(State $state, InegiMunicipalityCatalog $catalog): JsonResponse
    {
        return response()->json($catalog->forState($state->state_code));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Services\InegiMunicipalityCatalog;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use UnexpectedValueException;

class MunicipalityController extends Controller
{
    public function index(State $state, InegiMunicipalityCatalog $catalog): JsonResponse
    {
        try {
            return response()->json($catalog->forState($state->state_code));
        } catch (RequestException|UnexpectedValueException) {
            return response()->json(['message' => 'Municipalities are temporarily unavailable.'], 502);
        }
    }
}

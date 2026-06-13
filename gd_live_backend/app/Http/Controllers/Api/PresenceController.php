<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HostAvailabilityService;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function __construct(private HostAvailabilityService $availabilityService)
    {
    }

    public function socketStatus(Request $request)
    {
        $data = $request->validate([
            'socket_status' => 'required|in:online,offline',
        ]);

        $availability = $this->availabilityService->updateSocketStatus($request->user()->id, $data['socket_status']);

        return response()->json([
            'ok' => true,
            'data' => $availability,
        ]);
    }
}

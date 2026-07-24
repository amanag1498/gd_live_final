<?php

// app/Http/Controllers/Api/PushTokenController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DevicePushToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PushTokenController extends Controller
{
    public function register(Request $req) {
        $data = $req->validate([
            'token' => 'required|string|max:255',
            'platform' => ['nullable', 'string', Rule::in(['android', 'ios'])],
        ]);
        $deviceId = $req->header('X-Device-Id') ?? $req->input('device_id');
        $platform = $data['platform'] ?? 'android';

        $row = DB::transaction(function () use ($req, $data, $deviceId, $platform) {
            if ($deviceId) {
                DevicePushToken::query()
                    ->where('device_id', $deviceId)
                    ->where('platform', $platform)
                    ->where('token', '!=', $data['token'])
                    ->delete();
            }

            return DevicePushToken::updateOrCreate(
                ['token' => $data['token']],
                [
                    'user_id' => $req->user()->id,
                    'device_id' => $deviceId,
                    'platform' => $platform,
                    'last_seen_at' => now(),
                ],
            );
        });
        return response()->json(['ok'=>true,'id'=>$row->id]);
    }

    public function unregister(Request $req) {
        $req->validate(['token'=>'required|string']);
        DevicePushToken::where('token',$req->token)->delete();
        return response()->json(['ok'=>true]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * Ingest endpoint a registered device calls to report its own position.
 *
 * The device authenticates with the per-phone API key (header
 * "X-Api-Key" or an "api_key" field). This is the data-collection side
 * of the tracker: a "find my device" style check-in.
 */
class LocationReportController extends Controller
{
    public function report(Request $request): JsonResponse
    {
        $key = $request->header('X-Api-Key') ?? $request->input('api_key');

        if (!$key) {
            return response()->json(['ok' => false, 'error' => 'Missing API key.'], 401);
        }

        $phone = Phone::where('api_key', $key)->first();
        if (!$phone) {
            return response()->json(['ok' => false, 'error' => 'Invalid API key.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy'  => ['nullable', 'numeric', 'min:0'],
            'battery'   => ['nullable', 'integer', 'between:0,100'],
            'label'     => ['nullable', 'string', 'max:120'],
        ]);

        if ($validator->fails()) {
            return response()->json(['ok' => false, 'errors' => $validator->errors()], 422);
        }

        $location = $phone->locations()->create($validator->validated() + ['source' => 'device']);
        $phone->touch();

        return response()->json([
            'ok'       => true,
            'message'  => 'Location recorded.',
            'phone'    => $phone->label,
            'location' => [
                'id'         => $location->id,
                'latitude'   => $location->latitude,
                'longitude'  => $location->longitude,
                'recorded_at'=> $location->created_at->toIso8601String(),
            ],
        ], 201);
    }
}

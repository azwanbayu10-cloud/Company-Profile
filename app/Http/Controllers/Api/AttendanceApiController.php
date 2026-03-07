<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceApiController extends Controller
{
    public function history(Request $request): JsonResponse
    {
        $data = Attendance::where('user_id', $request->user()->id)
            ->latest('date')
            ->paginate(10);

        return response()->json($data);
    }

    public function checkIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'photo' => ['required', 'string'],
        ]);

        $now = Carbon::now();

        if ($now->lt(Carbon::createFromTimeString('08:00:00')) || $now->gt(Carbon::createFromTimeString('17:00:00'))) {
            return response()->json(['message' => 'Check-in hanya pukul 08:00 - 17:00'], 422);
        }

        $existing = Attendance::where('user_id', $request->user()->id)
            ->whereDate('date', today())
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Sudah check-in hari ini'], 422);
        }

        $status = $now->gt(Carbon::createFromTimeString('08:00:00')) ? 'Telat' : 'Hadir';

        $attendance = Attendance::create([
            'user_id' => $request->user()->id,
            'date' => today(),
            'check_in' => $now->format('H:i:s'),
            'status' => $status,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'photo' => $validated['photo'],
        ]);

        return response()->json([
            'message' => 'Check-in berhasil',
            'data' => $attendance,
        ], 201);
    }

    public function checkOut(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $attendance = Attendance::where('user_id', $request->user()->id)
            ->whereDate('date', today())
            ->first();

        if (! $attendance) {
            return response()->json(['message' => 'Belum check-in hari ini'], 422);
        }

        if ($attendance->check_out) {
            return response()->json(['message' => 'Sudah check-out hari ini'], 422);
        }

        if (Carbon::now()->lt(Carbon::createFromTimeString('17:00:00'))) {
            return response()->json(['message' => 'Check-out hanya setelah 17:00'], 422);
        }

        $attendance->update([
            'check_out' => now()->format('H:i:s'),
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return response()->json([
            'message' => 'Check-out berhasil',
            'data' => $attendance,
        ]);
    }
}

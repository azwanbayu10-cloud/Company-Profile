<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function dashboard(Request $request): View
    {
        $todayRecord = Attendance::where('user_id', $request->user()->id)
            ->whereDate('date', today())
            ->first();

        $history = Attendance::where('user_id', $request->user()->id)
            ->latest('date')
            ->paginate(10);

        return view('employee.dashboard', compact('todayRecord', 'history'));
    }

    public function checkIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        $now = Carbon::now();
        $start = Carbon::createFromTimeString('08:00:00');
        $end = Carbon::createFromTimeString('17:00:00');

        if ($now->lt($start) || $now->gt($end)) {
            return back()->withErrors(['time' => 'Absensi masuk hanya diperbolehkan pukul 08:00 - 17:00.']);
        }

        $existing = Attendance::where('user_id', $request->user()->id)
            ->whereDate('date', today())
            ->first();

        if ($existing) {
            return back()->withErrors(['duplicate' => 'Anda sudah absen masuk hari ini.']);
        }

        $status = $now->gt(Carbon::createFromTimeString('08:00:00')) ? 'Telat' : 'Hadir';
        $photoPath = $request->file('photo')->store('attendance-photos', 'public');

        Attendance::create([
            'user_id' => $request->user()->id,
            'date' => today(),
            'check_in' => $now->format('H:i:s'),
            'status' => $status,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'photo' => $photoPath,
        ]);

        return back()->with('success', 'Absen masuk berhasil disimpan.');
    }

    public function checkOut(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $record = Attendance::where('user_id', $request->user()->id)
            ->whereDate('date', today())
            ->first();

        if (! $record) {
            return back()->withErrors(['missing' => 'Anda belum melakukan absen masuk hari ini.']);
        }

        if ($record->check_out) {
            return back()->withErrors(['duplicate' => 'Anda sudah absen pulang hari ini.']);
        }

        $now = Carbon::now();
        $end = Carbon::createFromTimeString('17:00:00');

        if ($now->lt($end)) {
            return back()->withErrors(['time' => 'Absen pulang hanya diperbolehkan setelah pukul 17:00.']);
        }

        $record->update([
            'check_out' => $now->format('H:i:s'),
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return back()->with('success', 'Absen pulang berhasil disimpan.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceExport;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminController extends Controller
{
    public function dashboard(Request $request): View
    {
        $filters = [
            'name' => $request->string('name')->toString(),
            'start_date' => $request->string('start_date')->toString(),
            'end_date' => $request->string('end_date')->toString(),
        ];

        $attendance = Attendance::query()
            ->with('user')
            ->when($filters['name'], fn ($query, $name) => $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$name}%")))
            ->when($filters['start_date'], fn ($query, $date) => $query->whereDate('date', '>=', $date))
            ->when($filters['end_date'], fn ($query, $date) => $query->whereDate('date', '<=', $date))
            ->orderByDesc('date')
            ->paginate(15)
            ->withQueryString();

        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('admin.dashboard', compact('attendance', 'employees', 'filters'));
    }

    public function export(Request $request): BinaryFileResponse
    {
        return Excel::download(new AttendanceExport($request->only(['name', 'start_date', 'end_date'])), 'rekap-absensi.xlsx');
    }
}

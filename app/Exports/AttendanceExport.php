<?php

namespace App\Exports;

use App\Models\Attendance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly array $filters = [])
    {
    }

    public function collection(): Collection
    {
        return Attendance::query()
            ->with('user')
            ->when($this->filters['name'] ?? null, fn ($query, $name) => $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$name}%")))
            ->when($this->filters['start_date'] ?? null, fn ($query, $date) => $query->whereDate('date', '>=', $date))
            ->when($this->filters['end_date'] ?? null, fn ($query, $date) => $query->whereDate('date', '<=', $date))
            ->orderByDesc('date')
            ->get()
            ->map(fn (Attendance $attendance) => [
                'Nama' => $attendance->user?->name,
                'Tanggal' => optional($attendance->date)->format('Y-m-d'),
                'Jam Masuk' => $attendance->check_in,
                'Jam Pulang' => $attendance->check_out,
                'Status' => $attendance->status,
                'Latitude' => $attendance->latitude,
                'Longitude' => $attendance->longitude,
            ]);
    }

    public function headings(): array
    {
        return ['Nama', 'Tanggal', 'Jam Masuk', 'Jam Pulang', 'Status', 'Latitude', 'Longitude'];
    }
}

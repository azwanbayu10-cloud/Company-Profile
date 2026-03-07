@extends('layouts.app')

@section('content')
<h4 class="mb-3">Dashboard Admin</h4>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2" method="GET" action="{{ route('admin.dashboard') }}">
            <div class="col-md-3">
                <input type="text" name="name" value="{{ $filters['name'] }}" class="form-control" placeholder="Filter nama">
            </div>
            <div class="col-md-3">
                <input type="date" name="start_date" value="{{ $filters['start_date'] }}" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="date" name="end_date" value="{{ $filters['end_date'] }}" class="form-control">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.attendance.export', request()->query()) }}" class="btn btn-success">Export Excel</a>
            </div>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h6>Data Karyawan</h6>
        <ul class="mb-0">
            @foreach($employees as $employee)
                <li>{{ $employee->name }} ({{ $employee->email }})</li>
            @endforeach
        </ul>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h6>Rekap Absensi</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Status</th>
                    <th>Lokasi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($attendance as $item)
                    <tr>
                        <td>{{ $item->user?->name }}</td>
                        <td>{{ $item->date->format('d-m-Y') }}</td>
                        <td>{{ $item->check_in }}</td>
                        <td>{{ $item->check_out ?? '-' }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->latitude }}, {{ $item->longitude }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Data tidak ditemukan</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $attendance->links() }}
    </div>
</div>
@endsection

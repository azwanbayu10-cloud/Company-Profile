@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Dashboard Karyawan</h4>
    <span class="badge bg-secondary">{{ auth()->user()->name }}</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h6>Absen Masuk</h6>
                <form id="checkInForm" action="{{ route('attendance.check-in') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="latitude">
                    <input type="hidden" name="longitude">
                    <div class="mb-2">
                        <label class="form-label">Foto Selfie</label>
                        <input type="file" name="photo" accept="image/*" capture="user" class="form-control" required>
                    </div>
                    <button type="button" onclick="fillGeo('checkInForm'); this.form.submit();" class="btn btn-success"
                            {{ $todayRecord ? 'disabled' : '' }}>Absen Masuk</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h6>Absen Pulang</h6>
                <form id="checkOutForm" action="{{ route('attendance.check-out') }}" method="POST">
                    @csrf
                    <input type="hidden" name="latitude">
                    <input type="hidden" name="longitude">
                    <button type="button" onclick="fillGeo('checkOutForm'); this.form.submit();" class="btn btn-warning"
                        {{ ! $todayRecord || $todayRecord->check_out ? 'disabled' : '' }}>Absen Pulang</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h6>Riwayat Absensi</h6>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Status</th>
                    <th>Lokasi</th>
                    <th>Foto</th>
                </tr>
                </thead>
                <tbody>
                @forelse($history as $item)
                    <tr>
                        <td>{{ $item->date->format('d-m-Y') }}</td>
                        <td>{{ $item->check_in ?? '-' }}</td>
                        <td>{{ $item->check_out ?? '-' }}</td>
                        <td><span class="badge bg-info text-dark">{{ $item->status }}</span></td>
                        <td>{{ $item->latitude }}, {{ $item->longitude }}</td>
                        <td>
                            @if($item->photo)
                                <a href="{{ asset('storage/' . $item->photo) }}" target="_blank">Lihat</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $history->links() }}
    </div>
</div>
@endsection

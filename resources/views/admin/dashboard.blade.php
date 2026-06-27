@extends('layouts.app')

@section('content')
<div class="container admin-page">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="mb-4">
                <h1 class="h3 mb-1 fw-bold">Bookings dashboard</h1>
                <p class="text-muted mb-0">Manage stylists and exports</p>
            </div>

            <div class="card mb-4">
                <div class="card-header">Stylists</div>
                <div class="card-body">
                    @livewire('admin.hairdresser-manager')
                </div>
            </div>

            
            </div>
            <div class="card mb-4">
                <div class="card-header">Export bookings (CSV)</div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.bookings.export') }}" class="row g-3 align-items-end">
                        <div class="col-md-4"><label class="form-label">From</label><input type="date" name="from" class="form-control" required></div>
                        <div class="col-md-4"><label class="form-label">To</label><input type="date" name="to" class="form-control" required></div>
                        <div class="col-md-4"><button class="btn btn-outline-primary w-100">Download CSV</button></div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">All bookings</div>
                <div class="card-body p-0">
                    @if($bookings->isEmpty())
                        <div class="alert alert-light border-0 text-center m-3 mb-0">No bookings yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">ID</th><th>Stylist</th><th>Client</th><th>Email</th><th>Date</th><th>Time</th><th class="pe-3">Booked</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td class="ps-3 text-muted">#{{ $booking->id }}</td>
                                            <td>{{ $booking->hairdresser?->name }}</td>
                                            <td><strong>{{ $booking->name }}</strong></td>
                                            <td>{{ $booking->email }}</td>
                                            <td>{{ $booking->date->format('M d, Y') }}</td>
                                            <td><strong>{{ $booking->hour }}</strong></td>
                                            <td class="pe-3"><small class="text-muted">{{ $booking->created_at->format('M d, H:i') }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">{{ $bookings->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Hairdresser Dashboard - Bookings</h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exportBookingsModal">
                            Export
                        </button>
                        <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-primary">
                            New Booking
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($bookings->isEmpty())
                        <div class="alert alert-info text-center">
                            <p class="mb-0">No bookings found.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Client Name</th>
                                        <th>Email</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Booked At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->id }}</td>
                                            <td>
                                                <strong>{{ $booking->name }}</strong>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $booking->date->format('M d, Y') }}
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ $booking->date->format('l') }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ date('g:i A', strtotime($booking->hour)) }}</strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $booking->created_at->format('M d, Y H:i') }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $bookings->links() }}
                        </div>

                        <div class="mt-3 alert alert-light">
                            <strong>Total Bookings:</strong> {{ $bookings->total() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exportBookingsModal" tabindex="-1" aria-labelledby="exportBookingsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="GET" action="{{ route('admin.bookings.export') }}">
            <div class="modal-header">
                <h5 class="modal-title" id="exportBookingsModalLabel">Export Bookings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="export_from" class="form-label">From</label>
                    <input type="date"
                           class="form-control"
                           id="export_from"
                           name="from"
                           value="{{ now()->subWeek()->toDateString() }}"
                           required>
                </div>
                <div class="mb-3">
                    <label for="export_to" class="form-label">To</label>
                    <input type="date"
                           class="form-control"
                           id="export_to"
                           name="to"
                           value="{{ now()->toDateString() }}"
                           required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Export CSV</button>
            </div>
        </form>
    </div>
</div>
@endsection

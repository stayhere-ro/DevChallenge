@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Hairdresser Dashboard - Bookings</h4>
                    <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-primary">
                        New Booking
                    </a>
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
                                        <th>Hairdresser</th>
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
                                            <td>{{ $booking->hairdresser?->name ?? 'Not assigned' }}</td>
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
@endsection

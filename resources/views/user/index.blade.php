@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Your Bookings</h4>
                        <a href="/new-booking" class="btn btn-sm btn-outline-primary">
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
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Hairdresser</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                                                        {{ substr($booking->hairdresser->name, 0, 1) }}
                                                    </div>
                                                    <strong>{{ $booking->hairdresser->name }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="d-block">
                                                    {{ $booking->scheduled_at->format('Y. m. d.') }}
                                                </span>
                                                <small class="text-primary fw-bold">
                                                    {{ $booking->scheduled_at->format('H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($booking->scheduled_at->isPast())
                                                    <span class="badge rounded-pill bg-light text-dark border">Completed</span>
                                                @else
                                                    <span class="badge rounded-pill bg-success">Upcoming</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


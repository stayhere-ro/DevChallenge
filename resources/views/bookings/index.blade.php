@extends('layouts.app')

@section('scripts')
  <script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.3.4/dist/js/datepicker.min.js"></script>
  <script src="{{ asset('js/bookings.js') }}"></script>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Book Your Appointment</h4>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <p class="text-muted mb-4">
                        Schedule your hair appointment with us. We're open Monday to Friday, 8:00 AM - 5:00 PM.
                    </p>

                    <form method="POST" action="{{ route('bookings.store') }}" data-availability-url="{{ route('bookings.availability') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', auth()->user()->name ?? '') }}"
                                   required
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', auth()->user()->email ?? '') }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('date') is-invalid @enderror"
                                       id="date"
                                       name="date"
                                       value="{{ old('date') }}"
                                       min="{{ date('Y-m-d') }}"
                                       placeholder="YYYY-MM-DD"
                                       autocomplete="off"
                                       readonly
                                       inputmode="none"
                                       required>
                                @error('date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted">Weekends are not available</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="hour" class="form-label">Time <span class="text-danger">*</span></label>
                                <select class="form-select @error('hour') is-invalid @enderror"
                                        id="hour"
                                        name="hour"
                                        required>
                                    <option value="">Select a time</option>
                                    @for($i = 8; $i < 17; $i++)
                                        @php
                                            $time = sprintf('%02d:00', $i);
                                        @endphp
                                        <option value="{{ $time }}" {{ old('hour') == $time ? 'selected' : '' }}>
                                            {{ date('g:00 A', strtotime($time)) }}
                                        </option>
                                    @endfor
                                </select>
                                @error('hour')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted">Business hours: 8:00 AM - 5:00 PM. Unavailable times will be disabled.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hairdresser_id" class="form-label">Hairdresser <span class="text-danger">*</span></label>
                                <select class="form-select @error('hairdresser_id') is-invalid @enderror"
                                    id="hairdresser_id"
                                    name="hairdresser_id"
                                    required>
                                    <option value="">Select a hairdresser</option>
                                    @foreach($hairdressers as $hairdresser)
                                        <option value="{{ $hairdresser->id }}" @selected(old('hairdresser_id') == $hairdresser->id)>
                                        {{ $hairdresser->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('hairdresser_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong>Note:</strong> Only one booking per hour is allowed. Please select an available time slot.
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Book Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(auth()->check() && auth()->user()->isClient())
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">My Booking History</h5>
                    </div>

                    <div class="card-body">
                        <form class="row g-2 mb-3 align-items-end" method="GET" action="{{ route('bookings.index') }}">

                            <div class="col-md-4">
                                <label for="from" class="form-label">From</label>
                                <input type="date" id="from" name="from" value="{{ request('from') }}" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="to" class="form-label">To</label>
                                <input type="date" id="to" name="to" value="{{ request('to') }}" class="form-control">
                            </div>

                            <div class="col-md-2">
                                <label for="filter_hour" class="form-label">Hour</label>
                                <select id="filter_hour" name="filter_hour" class="form-select">
                                    <option value="">Any</option>
                                    @for($i = 8; $i < 17; $i++)
                                        @php $time = sprintf('%02d:00', $i); @endphp
                                        <option value="{{ $time }}" @selected(request('filter_hour') === $time)>
                                            {{ date('g:00 A', strtotime($time)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-2 d-flex">
                                <button class="btn btn-outline-primary w-100" type="submit">Filter</button>
                            </div>
                        </form>

                        @if(!$myBookings || $myBookings->isEmpty())
                            <div class="alert alert-info mb-0">No bookings yet.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($myBookings as $booking)
                                            <tr>
                                                <td>{{ $booking->id }}</td>
                                                <td>{{ $booking->date?->format('M d, Y') ?? '—' }}</td>
                                                <td>{{ $booking->hour ? date('g:i A', strtotime($booking->hour)) : '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $myBookings->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- @auth
                <div class="mt-3 text-center">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        View Admin Dashboard
                    </a>
                </div>
            @endauth --}}
        </div>
    </div>
</div>
@endsection


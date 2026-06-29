@extends('layouts.app')

@section('content')
<style>
    .time-badge-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .time-badge {
        cursor: pointer;
        color: #0d6efd;
        background-color: #fff;
        border: 1px solid #0d6efd;
        transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, color 0.15s ease-in-out, transform 0.15s ease-in-out;
    }

    .time-badge-input:checked + .time-badge {
        color: #fff;
        background-color: #0d6efd;
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        transform: translateY(-1px);
    }

    .time-badge-input:focus + .time-badge {
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.35);
    }

    .time-badge-input:disabled + .time-badge {
        cursor: not-allowed;
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
        box-shadow: none;
        transform: none;
    }
</style>

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

                    <form id="booking-form" method="POST" action="{{ route('bookings.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
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
                                   value="{{ old('email') }}"
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
                                <input type="date"
                                       class="form-control @error('date') is-invalid @enderror"
                                       id="date"
                                       name="date"
                                       value="{{ old('date') }}"
                                       min="{{ date('Y-m-d') }}"
                                       required>
                                @error('date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="weekend-message" class="text-danger small mt-2 d-none">
                                    Weekends are not available.
                                </div>
                                <small id="weekend-help" class="text-muted">Weekends are not available</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="hairdresser_id" class="form-label">Hairdresser <span class="text-danger">*</span></label>
                                <select class="form-select @error('hairdresser_id') is-invalid @enderror"
                                        id="hairdresser_id"
                                        name="hairdresser_id"
                                        required>
                                    <option value="">Select a hairdresser</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('hairdresser_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('hairdresser_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted">Select hairdresser</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Time <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-2">
                                    @for($i = 8; $i < 17; $i++)
                                        @php
                                            $time = sprintf('%02d:00', $i);
                                        @endphp
                                        <label class="time-badge-option d-inline-flex align-items-center">
                                            <input type="radio"
                                                class="time-badge-input"
                                                name="hour"
                                                value="{{ $time }}"
                                                data-time="{{ $time }}"
                                                {{ old('hour') == $time ? 'checked' : '' }}
                                                required
                                                disabled>
                                            <span class="time-badge badge fs-6 px-2 py-2">
                                                {{ date('g:00 A', strtotime($time)) }}
                                            </span>
                                        </label>
                                    @endfor
                                </div>
                                @error('hour')
                                    <div class="text-danger small mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div id="hour-required-message" class="text-danger small mt-2 d-none">
                                    Please select a time.
                                </div>
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

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const bookedSlotKeys = new Set(@json($bookings->map(function ($booking) {
                                return $booking->hairdresser_id . '|' . $booking->scheduled_at->format('Y-m-d H:i');
                            })->values()));

                            const dateInput = document.getElementById('date');
                            const bookingForm = document.getElementById('booking-form');
                            const hairdresserSelect = document.getElementById('hairdresser_id');
                            const weekendMessage = document.getElementById('weekend-message');
                            const weekendHelp = document.getElementById('weekend-help');
                            const hourRequiredMessage = document.getElementById('hour-required-message');
                            const timeInputs = document.querySelectorAll('.time-badge-input');

                            function isBooked(hairdresserId, date, time) {
                                return bookedSlotKeys.has(hairdresserId + '|' + date + ' ' + time);
                            }

                            function isWeekend(date) {
                                if (!date) {
                                    return false;
                                }

                                const day = new Date(date + 'T00:00:00').getDay();

                                return day === 0 || day === 6;
                            }

                            function updateTimeBadges() {
                                const date = dateInput.value;
                                const hairdresserId = hairdresserSelect.value;
                                const hasRequiredSelection = Boolean(date && hairdresserId);

                                timeInputs.forEach(function (input) {
                                    const booked = hasRequiredSelection && isBooked(hairdresserId, date, input.dataset.time);
                                    const option = input.closest('.time-badge-option');

                                    input.disabled = !hasRequiredSelection || booked;
                                    option.hidden = booked;

                                    if (input.disabled && input.checked) {
                                        input.checked = false;
                                    }
                                });

                                hourRequiredMessage.classList.add('d-none');
                            }

                            dateInput.addEventListener('change', function () {
                                if (isWeekend(dateInput.value)) {
                                    dateInput.value = '';
                                    weekendMessage.classList.remove('d-none');
                                    weekendHelp.classList.add('d-none');
                                } else {
                                    weekendMessage.classList.add('d-none');
                                    weekendHelp.classList.remove('d-none');
                                }

                                updateTimeBadges();
                            });
                            hairdresserSelect.addEventListener('change', updateTimeBadges);
                            timeInputs.forEach(function (input) {
                                input.addEventListener('change', function () {
                                    hourRequiredMessage.classList.add('d-none');
                                });
                            });
                            bookingForm.addEventListener('submit', function (event) {
                                const hasSelectedTime = Array.from(timeInputs).some(function (input) {
                                    return !input.disabled && input.checked;
                                });

                                if (!hasSelectedTime) {
                                    event.preventDefault();
                                    hourRequiredMessage.classList.remove('d-none');
                                }
                            });
                            updateTimeBadges();
                        });
                    </script>
                </div>
            </div>

            @auth
                <div class="mt-3 text-center">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        View Admin Dashboard
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection

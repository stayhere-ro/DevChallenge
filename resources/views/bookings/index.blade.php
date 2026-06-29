@extends('layouts.app')

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

                    <form method="POST" action="{{ route('bookings.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="hairdresser_id" class="form-label">Hairdresser <span class="text-danger">*</span></label>
                            <select class="form-select @error('hairdresser_id') is-invalid @enderror"
                                    id="hairdresser_id"
                                    name="hairdresser_id"
                                    required>
                                <option value="">Select a hairdresser</option>
                                @foreach($hairdressers as $hairdresser)
                                    <option value="{{ $hairdresser->id }}" {{ old('hairdresser_id') == $hairdresser->id ? 'selected' : '' }}>
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
                                <small class="text-muted">Business hours: 8:00 AM - 5:00 PM</small>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong>Note:</strong> Each hairdresser can only accept one booking per hour. Please select an available time slot.
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Book Appointment
                            </button>
                        </div>
                    </form>
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

@extends('layouts.app')

@section('content')
<div class="container admin-page">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 fw-bold">Bookings dashboard</h1>
                    <p class="text-muted mb-0">Manage stylists, appointments, and exports</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adminBookingModal">
                    + New booking
                </button>
            </div>

            <div class="card mb-4">
                <div class="card-header">Stylists</div>
                <div class="card-body">
                    @livewire('admin.hairdresser-manager')
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Export bookings (CSV)</div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.bookings.export') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">From</label>
                            <input type="date" name="from" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">To</label>
                            <input type="date" name="to" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-primary w-100">Download CSV</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <span>All bookings</span>
                        <span class="badge bg-secondary ms-2">{{ $bookings->total() }}</span>
                    </div>
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex gap-2" style="min-width: 280px;">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="direction" value="{{ $direction }}">
                        <input type="search"
                               name="search"
                               value="{{ $search }}"
                               class="form-control form-control-sm"
                               placeholder="Search client, email, stylist…"
                               aria-label="Search bookings">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Search</button>
                        @if($search !== '')
                            <a href="{{ route('admin.dashboard', ['sort' => $sort, 'direction' => $direction]) }}"
                               class="btn btn-sm btn-link text-decoration-none">Clear</a>
                        @endif
                    </form>
                </div>

                <div class="card-body p-0">
                    @if($bookings->isEmpty())
                        <div class="alert alert-light border-0 text-center m-3 mb-0">
                            {{ $search !== '' ? 'No bookings match your search.' : 'No bookings yet.' }}
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        @php
                                            $sortLink = function (string $column, string $label) use ($sort, $direction, $search) {
                                                $nextDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
                                                $arrow = $sort === $column ? ($direction === 'asc' ? ' ↑' : ' ↓') : '';
                                                $query = array_filter([
                                                    'sort' => $column,
                                                    'direction' => $nextDirection,
                                                    'search' => $search !== '' ? $search : null,
                                                ]);
                                                $url = route('admin.dashboard', $query);

                                                return '<a href="'.$url.'" class="text-decoration-none text-reset fw-semibold">'.$label.$arrow.'</a>';
                                            };
                                        @endphp
                                        <th class="ps-3">{!! $sortLink('id', 'ID') !!}</th>
                                        <th>{!! $sortLink('stylist', 'Stylist') !!}</th>
                                        <th>{!! $sortLink('client', 'Client') !!}</th>
                                        <th>{!! $sortLink('email', 'Email') !!}</th>
                                        <th>{!! $sortLink('scheduled_at', 'Date') !!}</th>
                                        <th>Time</th>
                                        <th class="pe-3">{!! $sortLink('created_at', 'Booked') !!}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td class="ps-3 text-muted">#{{ $booking->id }}</td>
                                            <td>{{ $booking->hairdresser?->name }}</td>
                                            <td><strong>{{ $booking->name }}</strong></td>
                                            <td><a href="mailto:{{ $booking->email }}" class="text-decoration-none">{{ $booking->email }}</a></td>
                                            <td><span class="badge rounded-pill text-bg-light border">{{ $booking->date->format('M d, Y') }}</span></td>
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

<div class="modal fade" id="adminBookingModal" tabindex="-1" aria-labelledby="adminBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminBookingModalLabel">New booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @livewire('booking-wizard', ['embedded' => true], key('admin-booking-wizard'))
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        const modalEl = document.getElementById('adminBookingModal');
        if (!modalEl) return;

        modalEl.addEventListener('show.bs.modal', () => {
            Livewire.dispatch('reset-wizard');
        });

        Livewire.on('booking-created', () => {
            const instance = bootstrap.Modal.getInstance(modalEl) ?? bootstrap.Modal.getOrCreateInstance(modalEl);
            instance.hide();
            window.location.reload();
        });
    });
</script>
@endpush

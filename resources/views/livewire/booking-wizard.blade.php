<div class="booking-wizard">
    <div class="booking-wizard__header">
        @unless($bookingComplete)
            <div class="wizard-steps">
                @foreach([1 => 'Your details', 2 => 'Pick a time', 3 => 'Confirm'] as $number => $label)
                    <div class="wizard-step {{ $step === $number ? 'wizard-step--active' : ($step > $number ? 'wizard-step--done' : '') }}">
                        <div class="wizard-step__track">
                            <div class="wizard-step__fill"></div>
                        </div>
                        <span class="wizard-step__label">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        @endunless
    </div>

    <div class="booking-wizard__body">
        @if($bookingComplete)
            <div class="booking-success">
                <div class="booking-success__icon">✓</div>
                <h5>You're all set!</h5>
                <p class="text-muted mb-3">
                    We look forward to seeing you on
                    <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('l, M j') }}</strong>
                    at <strong>{{ $selectedHour }}</strong>.
                </p>
</div>
        @else
            @if($step === 1)
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="client-name">Full name</label>
                        <input id="client-name" type="text" class="form-control" wire:model="name" placeholder="Jane Doe" autocomplete="name">
                        @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="client-email">Email</label>
                        <input id="client-email" type="email" class="form-control" wire:model="email" placeholder="jane@example.com" autocomplete="email">
                        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Choose your stylist</label>
                        @if($hairdressers->isEmpty())
                            <div class="alert alert-warning mb-0">No stylists available. Please run <code>php artisan db:seed</code>.</div>
                        @else
                            <div class="stylist-grid">
                                @foreach($hairdressers as $hairdresser)
                                    @php
                                        $initials = collect(explode(' ', $hairdresser->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->join('');
                                    @endphp
                                    <button type="button"
                                            class="stylist-card {{ (int) $hairdresserId === $hairdresser->id ? 'stylist-card--selected' : '' }}"
                                            wire:click="selectHairdresser({{ $hairdresser->id }})">
                                        <span class="stylist-card__avatar">{{ $initials }}</span>
                                        <span class="stylist-card__name">{{ $hairdresser->name }}</span>
                                        <span class="stylist-card__meta">{{ $hairdresser->location ?? 'Salon' }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        @error('hairdresserId') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>
                </div>
            @endif

            @if($step === 2)
                <div wire:poll.5s.visible="refreshSlots">
                    <div class="week-nav">
                        <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="shiftWeek(-1)" wire:loading.attr="disabled" wire:target="shiftWeek">← Prev</button>
                        <span class="week-nav__label">
                            {{ \Carbon\Carbon::parse($weekStart)->format('M j') }}
                            –
                            {{ \Carbon\Carbon::parse($weekEnd)->format('M j, Y') }}
                        </span>
                        <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="shiftWeek(1)" wire:loading.attr="disabled" wire:target="shiftWeek">Next →</button>
                    </div>

                    <div class="schedule-panel"
                         wire:loading.class="schedule-panel--busy"
                         wire:loading.delay.400ms
                         wire:target="shiftWeek">

                    @if(empty($availableSlots))
                        <div class="alert alert-light border text-center mb-0">No open slots this week. Try another week.</div>
                    @else
                        <div class="day-grid">
                            @foreach($availableSlots as $date => $slots)
                                @php $day = \Carbon\Carbon::parse($date); @endphp
                                <button type="button"
                                        class="day-chip {{ $selectedDate === $date ? 'day-chip--selected' : '' }}"
                                        wire:click="$set('selectedDate', '{{ $date }}')">
                                    <span class="day-chip__date">{{ $day->format('D, M j') }}</span>
                                    <span class="day-chip__count">{{ count($slots) }} slots</span>
                                </button>
                            @endforeach
                        </div>

                        @if($selectedDate)
                            <p class="small text-muted mb-2">Available times for {{ \Carbon\Carbon::parse($selectedDate)->format('l, M j') }}</p>
                            <div class="slot-grid">
                                @foreach($availableSlots[$selectedDate] ?? [] as $slot)
                                    <button type="button"
                                            class="slot-chip {{ $selectedHour === $slot['hour'] ? 'slot-chip--selected' : '' }}"
                                            wire:click="$set('selectedHour', '{{ $slot['hour'] }}')">
                                        {{ $slot['hour'] }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @endif
                    @error('selectedHour') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                    </div>
                </div>
            @endif

            @if($step === 3)
                <dl class="booking-summary row mb-0">
                    <dt class="col-sm-4">Name</dt>
                    <dd class="col-sm-8">{{ $name }}</dd>
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $email }}</dd>
                    <dt class="col-sm-4">Stylist</dt>
                    <dd class="col-sm-8">{{ $hairdressers->firstWhere('id', $hairdresserId)?->name }}</dd>
                    <dt class="col-sm-4">When</dt>
                    <dd class="col-sm-8 mb-0">
                        {{ \Carbon\Carbon::parse($selectedDate)->format('l, M j, Y') }}
                        at {{ $selectedHour }}
                    </dd>
                </dl>
            @endif
        @endif
    </div>

    @unless($bookingComplete)
        <div class="booking-wizard__footer d-flex justify-content-between align-items-center">
            @if($step > 1)
                <button type="button" class="btn btn-outline-secondary" wire:click="previousStep">Back</button>
            @else
                <span></span>
            @endif

            @if($step < 3)
                <button type="button" class="btn btn-primary px-4" wire:click="nextStep" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="nextStep">Continue</span>
                    <span wire:loading wire:target="nextStep">…</span>
                </button>
            @elseif($step === 3)
                <button type="button" class="btn btn-success px-4" wire:click="confirmBooking" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="confirmBooking">Confirm booking</span>
                    <span wire:loading wire:target="confirmBooking">Booking…</span>
                </button>
            @endif
        </div>
    @endunless
</div>

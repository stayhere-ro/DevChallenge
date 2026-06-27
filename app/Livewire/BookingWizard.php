<?php

namespace App\Livewire;

use App\DTO\CreateBookingData;
use App\Exceptions\BookingConflictException;
use App\Models\Hairdresser;
use App\Services\Booking\BookingAvailabilityService;
use App\Services\Booking\BookingService;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class BookingWizard extends Component
{
    public bool $embedded = false;

    public int $step = 1;

    public string $name = '';

    public string $email = '';

    public ?int $hairdresserId = null;

    public string $selectedDate = '';

    public string $selectedHour = '';

    /** @var array<string, list<array{hour: string}>> */
    public array $availableSlots = [];

    public string $weekStart = '';

    public string $weekEnd = '';

    public bool $bookingComplete = false;

    public function mount(bool $embedded = false): void
    {
        $this->embedded = $embedded;
        $this->hairdresserId = Hairdresser::active()->orderBy('id')->value('id');
        $this->setWeekContaining(now());
    }

    #[On('reset-wizard')]
    public function resetWizard(): void
    {
        $this->reset([
            'step', 'name', 'email', 'selectedDate', 'selectedHour',
            'availableSlots', 'bookingComplete',
        ]);
        $this->hairdresserId = Hairdresser::active()->orderBy('id')->value('id');
        $this->setWeekContaining(now());
    }

    #[On('stylist-created')]
    public function refreshDefaultStylist(): void
    {
        if (! $this->hairdresserId || ! Hairdresser::active()->whereKey($this->hairdresserId)->exists()) {
            $this->hairdresserId = Hairdresser::active()->orderBy('id')->value('id');
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email'],
                'hairdresserId' => ['required', 'integer', 'exists:hairdressers,id'],
            ]);
            $this->loadSlots();
        }

        if ($this->step === 2) {
            $this->validate([
                'selectedDate' => ['required', 'date'],
                'selectedHour' => ['required', 'date_format:H:i'],
            ]);
        }

        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function shiftWeek(int $direction): void
    {
        $anchor = Carbon::parse($this->weekStart)->addWeeks($direction);
        $this->setWeekContaining($anchor);
        $this->loadSlots();
    }

    public function selectHairdresser(int $hairdresserId): void
    {
        $this->hairdresserId = $hairdresserId;
        $this->selectedDate = '';
        $this->selectedHour = '';

        if ($this->step >= 2) {
            $this->loadSlots();
        }
    }

    public function refreshSlots(): void
    {
        if ($this->step !== 2 || ! $this->hairdresserId) {
            return;
        }

        $service = app(BookingAvailabilityService::class);
        $fresh = $service->slotsForWeek(
            (int) $this->hairdresserId,
            Carbon::parse($this->weekStart),
            Carbon::parse($this->weekEnd),
        );

        // Skip re-render when nothing changed (avoids poll flicker).
        if (json_encode($fresh) === json_encode($this->availableSlots)) {
            return;
        }

        $this->availableSlots = $fresh;

        if ($this->selectedDate && empty($this->availableSlots[$this->selectedDate] ?? [])) {
            $this->selectedDate = '';
            $this->selectedHour = '';
        }
    }

    public function confirmBooking(BookingService $bookingService): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'hairdresserId' => ['required', 'integer'],
            'selectedDate' => ['required', 'date'],
            'selectedHour' => ['required', 'date_format:H:i'],
        ]);

        try {
            $bookingService->create(new CreateBookingData(
                hairdresserId: (int) $this->hairdresserId,
                clientName: $this->name,
                clientEmail: $this->email,
                scheduledAt: Carbon::parse($this->selectedDate.' '.$this->selectedHour.':00'),
            ));
        } catch (ValidationException $e) {
            $this->addError('selectedHour', $e->errors()['scheduled_at'][0] ?? 'Invalid slot.');

            return;
        } catch (BookingConflictException) {
            $this->addError('selectedHour', 'This time slot has just been booked. Please choose another.');
            $this->loadSlots();

            return;
        }

        $this->bookingComplete = true;
        $this->step = 4;

        if (! $this->embedded) {
            $this->dispatch('booking-created');
        }
    }

    public function closeEmbedded(): void
    {
        if (! $this->embedded) {
            return;
        }

        $this->dispatch('booking-created');
    }

    public function render()
    {
        return view('livewire.booking-wizard', [
            'hairdressers' => Hairdresser::active()->orderBy('name')->get(),
        ]);
    }

    private function setWeekContaining(Carbon $date): void
    {
        $start = $date->copy()->startOfWeek(Carbon::MONDAY);
        $this->weekStart = $start->toDateString();
        $this->weekEnd = $start->copy()->addDays(6)->toDateString();
    }

    private function loadSlots(): void
    {
        if (! $this->hairdresserId) {
            return;
        }

        $service = app(BookingAvailabilityService::class);
        $this->availableSlots = $service->slotsForWeek(
            (int) $this->hairdresserId,
            Carbon::parse($this->weekStart),
            Carbon::parse($this->weekEnd),
        );

        if ($this->selectedDate && empty($this->availableSlots[$this->selectedDate] ?? [])) {
            $this->selectedDate = '';
            $this->selectedHour = '';
        }
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\Hairdresser;
use Illuminate\Validation\Rule;
use Livewire\Component;

class HairdresserManager extends Component
{
    public string $name = '';

    public string $email = '';

    public string $location = '';

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('hairdressers', 'email')],
            'location' => ['required', 'string', 'max:255'],
        ]);

        Hairdresser::create([
            ...$validated,
            'is_active' => true,
        ]);

        $this->reset(['name', 'email', 'location']);
        $this->dispatch('stylist-created');
        session()->flash('stylist_saved', 'Stylist added successfully.');
    }

    public function toggleActive(int $hairdresserId): void
    {
        $hairdresser = Hairdresser::query()->findOrFail($hairdresserId);
        $hairdresser->update(['is_active' => ! $hairdresser->is_active]);
        $this->dispatch('stylist-created');
    }

    public function render()
    {
        return view('livewire.admin.hairdresser-manager', [
            'hairdressers' => Hairdresser::query()->orderBy('name')->get(),
        ]);
    }
}

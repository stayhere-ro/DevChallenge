<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\HairdresserManager;
use App\Models\Hairdresser;
use Livewire\Livewire;
use Tests\TestCase;

class HairdresserManagerTest extends TestCase
{
    public function test_admin_can_add_stylist(): void
    {
        Livewire::test(HairdresserManager::class)
            ->set('name', 'New Stylist')
            ->set('email', 'new@example.com')
            ->set('location', 'Studio East')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('hairdressers', [
            'name' => 'New Stylist',
            'email' => 'new@example.com',
            'location' => 'Studio East',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_toggle_stylist_active_state(): void
    {
        $stylist = Hairdresser::factory()->create(['is_active' => true]);

        Livewire::test(HairdresserManager::class)
            ->call('toggleActive', $stylist->id);

        $this->assertFalse($stylist->fresh()->is_active);
    }
}

<div>
    @if (session('stylist_saved'))
        <div class="alert alert-success py-2 small mb-3">{{ session('stylist_saved') }}</div>
    @endif

    <form wire:submit="save" class="row g-3 align-items-end mb-4">
        <div class="col-md-4">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" wire:model="name" placeholder="Alex Morgan">
            @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" wire:model="email" placeholder="alex@salon.com">
            @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">Location</label>
            <input type="text" class="form-control" wire:model="location" placeholder="Main Salon">
            @error('location') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Add stylist</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hairdressers as $hairdresser)
                    <tr>
                        <td class="fw-semibold">{{ $hairdresser->name }}</td>
                        <td>{{ $hairdresser->email }}</td>
                        <td>{{ $hairdresser->location ?? '—' }}</td>
                        <td>
                            @if($hairdresser->is_active)
                                <span class="badge text-bg-success">Active</span>
                            @else
                                <span class="badge text-bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    wire:click="toggleActive({{ $hairdresser->id }})">
                                {{ $hairdresser->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted text-center py-3">No stylists yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@props(['items' => []])

<div class="badge-row">
    @forelse($items as $item)
        <span class="badge">{{ $item }}</span>
    @empty
        <span class="muted">No skills added yet.</span>
    @endforelse
</div>

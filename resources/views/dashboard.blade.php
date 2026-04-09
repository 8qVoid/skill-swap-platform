<x-layouts.app title="Dashboard | Skill Swap">
    <section class="section-heading">
        <div>
            <p class="eyebrow">Dashboard</p>
            <h1>{{ $user->name }}, here's your swap hub.</h1>
        </div>
        <a href="{{ route('browse') }}" class="primary-button">Find people</a>
    </section>

    <section class="stats-row">
        <div class="panel stat"><strong>{{ $incomingRequests->count() }}</strong><span>Incoming requests</span></div>
        <div class="panel stat"><strong>{{ $activeSwaps->count() }}</strong><span>Active swaps</span></div>
        <div class="panel stat"><strong>{{ $savedUsers->count() }}</strong><span>Saved users</span></div>
        <div class="panel stat"><strong>{{ collect($user->learn_skills)->count() }}</strong><span>Learning goals</span></div>
    </section>

    <section class="dashboard-grid">
        <article class="panel">
            <div class="section-heading compact">
                <h2>Suggested matches</h2>
                <a href="{{ route('browse') }}">Browse all</a>
            </div>
            <div class="stack-list">
                @forelse($suggestedMatches as $match)
                    <div class="list-card">
                        <div class="list-person">
                            <img src="{{ $match['user']->profile_photo_url }}" alt="{{ $match['user']->name }}" class="avatar-thumb">
                            <div>
                                <strong>{{ $match['user']->name }}</strong>
                                <p class="muted">{{ $match['user']->location }} · {{ $match['user']->availability }}</p>
                                <x-skill-badges :items="$match['user']->teach_skills" />
                            </div>
                        </div>
                        <div class="right-cluster">
                            <span class="match-pill">{{ $match['score'] }}% match</span>
                            <a href="{{ route('users.show', $match['user']) }}" class="ghost-button">View profile</a>
                        </div>
                    </div>
                @empty
                    <p class="muted">Complete onboarding and more matches will appear here.</p>
                @endforelse
            </div>
        </article>

        <article class="panel">
            <h2>Incoming swap requests</h2>
            <div class="stack-list">
                @forelse($incomingRequests as $request)
                    <div class="list-card">
                        <div class="list-person">
                            <img src="{{ $request->requester->profile_photo_url }}" alt="{{ $request->requester->name }}" class="avatar-thumb">
                            <div>
                                <strong>{{ $request->requester->name }}</strong>
                                <p class="muted">Wants {{ $request->skill_to_learn }} and offers {{ $request->skill_to_offer }}</p>
                                <p>{{ $request->message }}</p>
                                <p class="meta">{{ $request->proposed_schedule }} · {{ ucfirst($request->preferred_format) }} · {{ ucfirst($request->status) }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('requests.respond', $request) }}" class="mini-form">
                            @csrf
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="primary-button small">Accept</button>
                        </form>
                    </div>
                @empty
                    <p class="muted">No incoming requests yet.</p>
                @endforelse
            </div>
        </article>

        <article class="panel">
            <h2>Active swaps</h2>
            <div class="stack-list">
                @forelse($activeSwaps as $swap)
                    @php($partner = $swap->requester_id === $user->id ? $swap->receiver : $swap->requester)
                    <div class="list-card">
                        <div class="list-person">
                            <img src="{{ $partner->profile_photo_url }}" alt="{{ $partner->name }}" class="avatar-thumb">
                            <div>
                                <strong>{{ $partner->name }}</strong>
                                <p class="muted">{{ $swap->skill_to_offer }} to {{ $swap->skill_to_learn }}</p>
                                <p class="meta">{{ ucfirst($swap->status) }} · {{ $swap->progress_percent }}% progress</p>
                            </div>
                        </div>
                        <a href="{{ route('swaps.show', $swap) }}" class="ghost-button">Open workspace</a>
                    </div>
                @empty
                    <p class="muted">Accept a request to create your first active swap.</p>
                @endforelse
            </div>
        </article>

        <article class="panel">
            <h2>Recent messages</h2>
            <div class="stack-list">
                @forelse($recentMessages as $message)
                    <div class="list-card">
                        <div>
                            <strong>{{ $message->user->name }}</strong>
                            <p>{{ $message->body }}</p>
                        </div>
                        <a href="{{ route('swaps.show', $message->swap) }}" class="ghost-button">Reply</a>
                    </div>
                @empty
                    <p class="muted">Messages will show up here after a swap starts.</p>
                @endforelse
            </div>
        </article>

        <article class="panel">
            <h2>Saved users</h2>
            <div class="stack-list">
                @forelse($savedUsers as $saved)
                    <div class="list-card">
                        <div class="list-person">
                            <img src="{{ $saved->profile_photo_url }}" alt="{{ $saved->name }}" class="avatar-thumb">
                            <div>
                                <strong>{{ $saved->name }}</strong>
                                <p class="muted">{{ implode(', ', array_slice($saved->teach_skills ?? [], 0, 2)) }}</p>
                            </div>
                        </div>
                        <a href="{{ route('users.show', $saved) }}" class="ghost-button">View</a>
                    </div>
                @empty
                    <p class="muted">Save profiles to revisit them later.</p>
                @endforelse
            </div>
        </article>

        <article class="panel">
            <h2>Recommended skills</h2>
            <div class="badge-row">
                @forelse($recommendedSkills as $skill => $count)
                    <span class="badge">{{ $skill }} · {{ $count }}</span>
                @empty
                    <p class="muted">Recommendations appear as more users join.</p>
                @endforelse
            </div>
        </article>
    </section>
</x-layouts.app>

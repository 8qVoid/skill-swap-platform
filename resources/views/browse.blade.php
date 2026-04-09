<x-layouts.app title="Browse Skills | Skill Swap">
    <section class="section-heading">
        <div>
            <p class="eyebrow">Browse skills</p>
            <h1>Search people by skill, level, timezone, availability, and format.</h1>
        </div>
    </section>

    <section class="panel">
        <form method="GET" action="{{ route('browse') }}" class="filter-grid">
            <input type="text" name="skill" placeholder="Python, UI Design, Public Speaking" value="{{ $filters['skill'] ?? '' }}">
            <select name="level">
                <option value="">Any level</option>
                @foreach(['Beginner', 'Intermediate', 'Advanced'] as $level)
                    <option value="{{ $level }}" @selected(($filters['level'] ?? '') === $level)>{{ $level }}</option>
                @endforeach
            </select>
            <select name="availability">
                <option value="">Any availability</option>
                @foreach(['Flexible', 'Weeknights', 'Weekends', 'Mornings'] as $availability)
                    <option value="{{ $availability }}" @selected(($filters['availability'] ?? '') === $availability)>{{ $availability }}</option>
                @endforeach
            </select>
            <input type="text" name="timezone" placeholder="Timezone" value="{{ $filters['timezone'] ?? '' }}">
            <select name="format">
                <option value="">Any format</option>
                @foreach(['video call', 'chat', 'in person', 'recorded lessons'] as $format)
                    <option value="{{ $format }}" @selected(($filters['format'] ?? '') === $format)>{{ ucfirst($format) }}</option>
                @endforeach
            </select>
            <button type="submit" class="primary-button">Search</button>
        </form>
    </section>

    <section class="card-grid">
        @forelse($results as $result)
            @php($member = $result['user'])
            <article class="panel member-card">
                <img src="{{ $member->profile_photo_url }}" alt="{{ $member->name }}">
                <div>
                    <div class="card-topline">
                        <h2>{{ $member->name }}</h2>
                        @if(! is_null($result['score']))
                            <span class="match-pill">{{ $result['score'] }}% match</span>
                        @endif
                    </div>
                    <p class="muted">{{ $member->location }} · {{ $member->timezone }} · {{ $member->skill_level }}</p>
                    <p>{{ $member->bio }}</p>
                    <p class="meta">Teaches</p>
                    <x-skill-badges :items="$member->teach_skills" />
                    <p class="meta">Wants</p>
                    <x-skill-badges :items="$member->learn_skills" />
                    <div class="button-row">
                        <a href="{{ route('users.show', $member) }}" class="ghost-button">View profile</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="panel">
                <p class="muted">No users matched those filters yet.</p>
            </div>
        @endforelse
    </section>
</x-layouts.app>

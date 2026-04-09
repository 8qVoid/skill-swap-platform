<x-layouts.app title="Skill Swap Platform">
    <section class="hero hero-surface">
        <div class="hero-copy-block">
            <p class="eyebrow">1-on-1 skill exchange</p>
            <h1>Teach what you know. Learn what you want.</h1>
            <p class="hero-copy">Skill Swap helps curious people trade practical knowledge through focused one-on-one sessions, from React and Figma to public speaking and video editing.</p>
            <div class="button-row">
                <a href="{{ route('signup') }}" class="primary-button">Create account</a>
                <a href="{{ route('browse') }}" class="ghost-button">Browse skills</a>
            </div>
        </div>
        <div class="panel spotlight">
            <p class="eyebrow">Live matching logic</p>
            <div class="metric-grid">
                <div><strong>Reciprocity</strong><span>Teach and learn overlap</span></div>
                <div><strong>Timezone</strong><span>Better scheduling fit</span></div>
                <div><strong>Format</strong><span>Video, chat, in person, recorded</span></div>
                <div><strong>Level</strong><span>Beginner to advanced</span></div>
            </div>
        </div>
    </section>

    <section class="section-grid">
        <article class="panel">
            <p class="eyebrow">How it works</p>
            <div class="steps">
                <div><strong>1.</strong><span>Create your profile with teach and learn skills.</span></div>
                <div><strong>2.</strong><span>Browse members and compare match percentages.</span></div>
                <div><strong>3.</strong><span>Send a swap request, chat, schedule, and leave a review.</span></div>
            </div>
        </article>

        <article class="panel">
            <p class="eyebrow">Browse skills</p>
            <div class="badge-row">
                @foreach($topSkills as $skill => $count)
                    <span class="badge">{{ $skill }} · {{ $count }}</span>
                @endforeach
            </div>
        </article>
    </section>

    <section class="panel">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Success stories</p>
                <h2>Real portfolio-ready examples</h2>
            </div>
            <a href="{{ route('browse') }}" class="ghost-button">Explore members</a>
        </div>
        <div class="card-grid">
            @foreach($featuredUsers as $user)
                <article class="profile-card">
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                    <div>
                        <h3>{{ $user->name }}</h3>
                        <p class="muted">{{ $user->bio }}</p>
                        <x-skill-badges :items="$user->teach_skills" />
                        <p class="meta">{{ $user->average_rating ?: 'New' }} rating · {{ $user->timezone }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</x-layouts.app>

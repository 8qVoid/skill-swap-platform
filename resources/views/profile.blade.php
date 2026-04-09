<x-layouts.app title="{{ $user->name }} | Skill Swap">
    <section class="profile-hero panel">
        <img class="avatar-xl" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
        <div>
            <div class="card-topline">
                <h1>{{ $user->name }}</h1>
                @if(! is_null($score))
                    <span class="match-pill">{{ $score }}% match</span>
                @endif
            </div>
            <p class="muted">{{ $user->location }} · {{ $user->timezone }} · {{ $user->availability }}</p>
            <p>{{ $user->bio }}</p>
            <p class="meta">{{ $user->average_rating ?: 'New' }} rating · {{ $user->completed_swap_count }} completed swaps @if($user->is_verified) · Verified @endif</p>
            <div class="button-row">
                @auth
                    @if(auth()->id() !== $user->id)
                        <form method="POST" action="{{ route('users.save', $user) }}">
                            @csrf
                            <button type="submit" class="ghost-button">Save profile</button>
                        </form>
                        @if($existingSwap)
                            <a href="{{ route('swaps.show', $existingSwap) }}" class="ghost-button">Message</a>
                        @endif
                    @endif
                @endauth
            </div>
        </div>
    </section>

    <section class="section-grid">
        <article class="panel">
            <h2>Skills offered</h2>
            <x-skill-badges :items="$user->teach_skills" />
            <h2>Skills wanted</h2>
            <x-skill-badges :items="$user->learn_skills" />
            <p class="meta">Preferred format</p>
            <x-skill-badges :items="$user->formats" />
        </article>

        <article class="panel">
            <h2>Portfolio links</h2>
            <div class="stack-list">
                @forelse($user->portfolio_links ?? [] as $link)
                    <a href="{{ $link }}" target="_blank" rel="noreferrer">{{ $link }}</a>
                @empty
                    <p class="muted">No portfolio links yet.</p>
                @endforelse
            </div>
        </article>
    </section>

    @auth
        @if(auth()->id() !== $user->id)
            <section class="section-grid">
                <article class="panel">
                    <h2>Request a swap</h2>
                    <form method="POST" action="{{ route('users.request', $user) }}" class="stack-form">
                        @csrf
                        <label>
                            <span>Skill you want to learn</span>
                            <input type="text" name="skill_to_learn" placeholder="Figma" required>
                        </label>
                        <label>
                            <span>Skill you can offer</span>
                            <input type="text" name="skill_to_offer" placeholder="React basics" required>
                        </label>
                        <label>
                            <span>Intro message</span>
                            <textarea name="message" rows="4" required>Hi, I can help you with React basics if you can teach me Figma.</textarea>
                        </label>
                        <label>
                            <span>Proposed schedule</span>
                            <input type="text" name="proposed_schedule" placeholder="Saturday 3 PM" required>
                        </label>
                        <label>
                            <span>Preferred format</span>
                            <select name="preferred_format" required>
                                @foreach(['video call', 'chat', 'in person', 'recorded lessons'] as $format)
                                    <option value="{{ $format }}">{{ ucfirst($format) }}</option>
                                @endforeach
                            </select>
                        </label>
                        <button type="submit" class="primary-button">Send swap request</button>
                    </form>
                </article>

                <article class="panel">
                    <h2>Trust and safety</h2>
                    <form method="POST" action="{{ route('users.report', $user) }}" class="stack-form">
                        @csrf
                        <label>
                            <span>Report reason</span>
                            <textarea name="reason" rows="3" placeholder="Spam, harassment, fake profile..." required></textarea>
                        </label>
                        <button type="submit" class="ghost-button">Report user</button>
                    </form>
                    <form method="POST" action="{{ route('users.block', $user) }}">
                        @csrf
                        <button type="submit" class="ghost-button danger">Block user</button>
                    </form>
                </article>
            </section>
        @endif
    @endauth

    <section class="panel">
        <h2>Reviews</h2>
        <div class="stack-list">
            @forelse($user->reviewsReceived as $review)
                <div class="list-card">
                    <div>
                        <strong>{{ $review->reviewer->name }}</strong>
                        <p class="meta">{{ $review->rating }}/5</p>
                        <p>{{ $review->feedback }}</p>
                    </div>
                </div>
            @empty
                <p class="muted">No reviews yet.</p>
            @endforelse
        </div>
    </section>
</x-layouts.app>

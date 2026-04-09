<x-layouts.app title="Active Swap | Skill Swap">
    <section class="section-heading">
        <div>
            <p class="eyebrow">Active swap workspace</p>
            <h1>{{ $partner->name }} · {{ $swap->skill_to_offer }} to {{ $swap->skill_to_learn }}</h1>
            <p class="muted">{{ ucfirst($swap->status) }} · {{ $swap->format }} · {{ $swap->progress_percent }}% complete</p>
        </div>
    </section>

    <section class="section-grid">
        <article class="panel">
            <h2>Partner info</h2>
            <div class="profile-card compact">
                <img src="{{ $partner->profile_photo_url }}" alt="{{ $partner->name }}">
                <div>
                    <strong>{{ $partner->name }}</strong>
                    <p class="muted">{{ $partner->location }} · {{ $partner->timezone }}</p>
                    <p>{{ $partner->bio }}</p>
                </div>
            </div>
            <h2>Progress tracker</h2>
            <form method="POST" action="{{ route('swaps.progress.update', $swap) }}" class="stack-form">
                @csrf
                <label>
                    <span>Notes / tasks</span>
                    <textarea name="progress_notes" rows="4" required>{{ $swap->progress_notes }}</textarea>
                </label>
                <label>
                    <span>Progress percent</span>
                    <input type="number" name="progress_percent" min="0" max="100" value="{{ $swap->progress_percent }}" required>
                </label>
                <label>
                    <span>Status</span>
                    <select name="status" required>
                        <option value="active" @selected($swap->status === 'active')>Active</option>
                        <option value="completed" @selected($swap->status === 'completed')>Completed</option>
                    </select>
                </label>
                <button type="submit" class="primary-button">Update progress</button>
            </form>
        </article>

        <article class="panel">
            <h2>Session scheduling</h2>
            <form method="POST" action="{{ route('swaps.sessions.store', $swap) }}" class="stack-form">
                @csrf
                <label>
                    <span>Date and time</span>
                    <input type="datetime-local" name="scheduled_for" required>
                </label>
                <label>
                    <span>Meeting link</span>
                    <input type="url" name="meeting_link" placeholder="https://meet.google.com/...">
                </label>
                <label>
                    <span>Topic</span>
                    <input type="text" name="topic" placeholder="React components review" required>
                </label>
                <label>
                    <span>Status</span>
                    <select name="status" required>
                        <option value="upcoming">Upcoming</option>
                        <option value="completed">Completed</option>
                        <option value="canceled">Canceled</option>
                    </select>
                </label>
                <button type="submit" class="ghost-button">Add session</button>
            </form>

            <div class="stack-list top-gap">
                @forelse($swap->sessions as $session)
                    <div class="list-card">
                        <div>
                            <strong>{{ $session->topic }}</strong>
                            <p class="meta">{{ $session->scheduled_for?->format('M d, Y h:i A') }} · {{ ucfirst($session->status) }}</p>
                            @if($session->meeting_link)
                                <a href="{{ $session->meeting_link }}" target="_blank" rel="noreferrer">{{ $session->meeting_link }}</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="muted">No sessions scheduled yet.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="section-grid">
        <article class="panel">
            <h2>Chat and shared resources</h2>
            <form method="POST" action="{{ route('swaps.messages.store', $swap) }}" class="stack-form">
                @csrf
                <label>
                    <span>Send a message or share a link</span>
                    <textarea name="body" rows="3" placeholder="Share notes, links, tasks, or follow-up questions." required></textarea>
                </label>
                <button type="submit" class="primary-button">Send message</button>
            </form>

            <div class="chat-list">
                @forelse($swap->messages as $message)
                    <div class="chat-bubble {{ $message->user_id === $user->id ? 'mine' : '' }}">
                        <strong>{{ $message->user->name }}</strong>
                        <p>{{ $message->body }}</p>
                    </div>
                @empty
                    <p class="muted">No messages yet.</p>
                @endforelse
            </div>
        </article>

        <article class="panel">
            <h2>Completion and review</h2>
            <form method="POST" action="{{ route('swaps.reviews.store', $swap) }}" class="stack-form">
                @csrf
                <label>
                    <span>Rating</span>
                    <select name="rating" required>
                        @foreach([5,4,3,2,1] as $rating)
                            <option value="{{ $rating }}">{{ $rating }}/5</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Feedback</span>
                    <textarea name="feedback" rows="4" placeholder="What made this swap helpful?" required></textarea>
                </label>
                <button type="submit" class="ghost-button">Leave review</button>
            </form>

            <div class="stack-list top-gap">
                @forelse($swap->reviews as $review)
                    <div class="list-card">
                        <div>
                            <strong>{{ $review->reviewer->name }}</strong>
                            <p class="meta">{{ $review->rating }}/5</p>
                            <p>{{ $review->feedback }}</p>
                        </div>
                    </div>
                @empty
                    <p class="muted">No reviews added yet.</p>
                @endforelse
            </div>
        </article>
    </section>
</x-layouts.app>

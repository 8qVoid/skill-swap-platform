<x-layouts.app title="Settings | Skill Swap">
    <section class="section-heading">
        <div>
            <p class="eyebrow">Reviews and settings</p>
            <h1>Keep your profile current and portfolio-ready.</h1>
        </div>
    </section>

    <section class="section-grid">
        <article class="panel">
            <h2>Edit profile</h2>
            <form method="POST" action="{{ route('settings.update') }}" class="form-grid" enctype="multipart/form-data">
                @csrf
                <div class="full profile-upload-card">
                    <div>
                        <p class="eyebrow">Profile image</p>
                        <h3>Swap in a new photo anytime.</h3>
                    </div>
                    <div class="avatar-uploader">
                        <img
                            src="{{ $user->profile_photo_url }}"
                            alt="{{ $user->name }}"
                            class="avatar-preview avatar-preview-lg"
                            data-profile-preview
                            data-profile-preview-base="https://ui-avatars.com/api/?background=163a5f&color=ffffff&bold=true&size=256&name="
                        >
                        <label class="upload-field">
                            <span>Replace image</span>
                            <input type="file" name="profile_photo_upload" accept="image/*" data-profile-file-input>
                        </label>
                    </div>
                </div>
                <label class="full">
                    <span>Bio</span>
                    <textarea name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                </label>
                <label>
                    <span>Location</span>
                    <input type="text" name="location" value="{{ old('location', $user->location) }}">
                </label>
                <label>
                    <span>Timezone</span>
                    <input type="text" name="timezone" value="{{ old('timezone', $user->timezone) }}" required>
                </label>
                <label>
                    <span>Availability</span>
                    <select name="availability" required>
                        @foreach(['Flexible', 'Weeknights', 'Weekends', 'Mornings'] as $availability)
                            <option value="{{ $availability }}" @selected(old('availability', $user->availability) === $availability)>{{ $availability }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="full">
                    <span>Teach skills</span>
                    <input type="text" name="teach_skills" value="{{ old('teach_skills', implode(', ', $user->teach_skills ?? [])) }}" required>
                </label>
                <label class="full">
                    <span>Learn skills</span>
                    <input type="text" name="learn_skills" value="{{ old('learn_skills', implode(', ', $user->learn_skills ?? [])) }}" required>
                </label>
                <fieldset class="full">
                    <legend>Formats</legend>
                    <div class="check-grid">
                        @foreach(['video call', 'chat', 'in person', 'recorded lessons'] as $format)
                            <label class="inline-check">
                                <input type="checkbox" name="formats[]" value="{{ $format }}" @checked(in_array($format, old('formats', $user->formats ?? []), true))>
                                <span>{{ ucfirst($format) }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <label class="full">
                    <span>Portfolio links</span>
                    <input type="text" name="portfolio_links" value="{{ old('portfolio_links', implode(', ', $user->portfolio_links ?? [])) }}">
                </label>
                <button type="submit" class="primary-button">Save settings</button>
            </form>
        </article>

        <article class="panel">
            <h2>Your reviews</h2>
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
        </article>
    </section>
</x-layouts.app>

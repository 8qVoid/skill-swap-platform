<x-layouts.app title="Onboarding | Skill Swap">
    <section class="panel">
        <p class="eyebrow">Onboarding</p>
        <h1>Build a profile that people can actually match with.</h1>
        <form method="POST" action="{{ route('onboarding.save') }}" class="form-grid" enctype="multipart/form-data">
            @csrf
            <div class="full profile-upload-card">
                <div>
                    <p class="eyebrow">Profile image</p>
                    <h2>Show up with a real face or brand.</h2>
                    <p class="muted">A strong profile photo makes the browse page feel much more alive.</p>
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
                        <span>Upload image</span>
                        <input type="file" name="profile_photo_upload" accept="image/*" data-profile-file-input>
                    </label>
                </div>
            </div>
            <label>
                <span>Name</span>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required data-profile-name-input>
            </label>
            <label class="full">
                <span>Bio</span>
                <textarea name="bio" rows="4" required>{{ old('bio', $user->bio) }}</textarea>
            </label>
            <label>
                <span>Location</span>
                <input type="text" name="location" value="{{ old('location', $user->location) }}">
            </label>
            <label>
                <span>Timezone</span>
                <input type="text" name="timezone" value="{{ old('timezone', $user->timezone ?? 'Asia/Manila') }}" required>
            </label>
            <label>
                <span>Availability</span>
                <select name="availability" required>
                    @foreach(['Flexible', 'Weeknights', 'Weekends', 'Mornings'] as $availability)
                        <option value="{{ $availability }}" @selected(old('availability', $user->availability) === $availability)>{{ $availability }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Skill level</span>
                <select name="skill_level" required>
                    @foreach(['Beginner', 'Intermediate', 'Advanced'] as $level)
                        <option value="{{ $level }}" @selected(old('skill_level', $user->skill_level) === $level)>{{ $level }}</option>
                    @endforeach
                </select>
            </label>
            <label class="full">
                <span>Skills you can teach</span>
                <input type="text" name="teach_skills" value="{{ old('teach_skills', implode(', ', $user->teach_skills ?? [])) }}" placeholder="React, Figma, Public Speaking" required>
            </label>
            <label class="full">
                <span>Skills you want to learn</span>
                <input type="text" name="learn_skills" value="{{ old('learn_skills', implode(', ', $user->learn_skills ?? [])) }}" placeholder="Python, UI Design, Video Editing" required>
            </label>
            <fieldset class="full">
                <legend>Preferred learning format</legend>
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
                <input type="text" name="portfolio_links" value="{{ old('portfolio_links', implode(', ', $user->portfolio_links ?? [])) }}" placeholder="https://yourportfolio.com, https://github.com/you">
            </label>
            <button type="submit" class="primary-button">Finish onboarding</button>
        </form>
    </section>
</x-layouts.app>

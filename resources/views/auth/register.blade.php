<x-layouts.app title="Sign Up | Skill Swap">
    <section class="auth-shell auth-grid">
        <div class="panel auth-panel">
            <p class="eyebrow">Get started</p>
            <h1>Build your profile and start swapping skills.</h1>
            <form method="POST" action="{{ route('signup.perform') }}" class="stack-form" enctype="multipart/form-data">
                @csrf
                <div class="avatar-uploader centered">
                    <img
                        src="https://ui-avatars.com/api/?name={{ urlencode(old('name', 'New Member')) }}&background=163a5f&color=ffffff&bold=true&size=256"
                        alt="Profile preview"
                        class="avatar-preview"
                        data-profile-preview
                        data-profile-preview-base="https://ui-avatars.com/api/?background=163a5f&color=ffffff&bold=true&size=256&name="
                    >
                    <label class="upload-field">
                        <span>Profile image</span>
                        <input type="file" name="profile_photo_upload" accept="image/*" data-profile-file-input>
                    </label>
                </div>
                <label>
                    <span>Name</span>
                    <input type="text" name="name" value="{{ old('name') }}" required data-profile-name-input>
                </label>
                <label>
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </label>
                <label>
                    <span>Password</span>
                    <input type="password" name="password" required>
                </label>
                <label>
                    <span>Confirm password</span>
                    <input type="password" name="password_confirmation" required>
                </label>
                <label>
                    <span>Role</span>
                    <select name="role" required>
                        <option value="teach-and-learn">I want to teach and learn</option>
                        <option value="normal-user">Continue as normal user</option>
                    </select>
                </label>
                <button type="submit" class="primary-button">Create account</button>
            </form>
        </div>

        <div class="panel gradient-panel">
            <p class="eyebrow">What you get</p>
            <ul class="feature-list">
                <li>Skill-based profile with teach and learn goals</li>
                <li>Smart match percentage based on reciprocity and schedule fit</li>
                <li>Swap requests, chat, sessions, reviews, and saved users</li>
            </ul>
            <div class="auth-note">
                <strong>New</strong>
                <span>Upload your profile image now and update it anytime in settings.</span>
            </div>
        </div>
    </section>
</x-layouts.app>

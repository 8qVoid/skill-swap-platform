<x-layouts.app title="Log In | Skill Swap">
    <section class="auth-shell">
        <div class="panel auth-panel">
            <p class="eyebrow">Welcome back</p>
            <h1>Pick up your next swap where you left it.</h1>
            <p class="muted">Use the seeded demo account with <strong>test@example.com</strong> / <strong>password</strong> or create your own.</p>

            <form method="POST" action="{{ route('login.perform') }}" class="stack-form">
                @csrf
                <label>
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </label>
                <label>
                    <span>Password</span>
                    <input type="password" name="password" required>
                </label>
                <label class="inline-check">
                    <input type="checkbox" name="remember" value="1">
                    <span>Keep me signed in</span>
                </label>
                <button type="submit" class="primary-button">Log in</button>
            </form>

            <button type="button" class="ghost-button full-width" disabled>Google login coming soon</button>
            <p class="muted">No account yet? <a href="{{ route('signup') }}">Create one</a>.</p>
        </div>
    </section>
</x-layouts.app>

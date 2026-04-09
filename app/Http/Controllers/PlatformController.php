<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Swap;
use App\Models\SwapMessage;
use App\Models\SwapRequest;
use App\Models\SwapSession;
use App\Models\User;
use App\Models\UserReport;
use App\Support\MatchService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PlatformController extends Controller
{
    public function __construct(private readonly MatchService $matchService)
    {
    }

    public function home(): View
    {
        $featuredUsers = User::query()
            ->where('onboarding_completed', true)
            ->with('reviewsReceived')
            ->latest()
            ->take(3)
            ->get();

        $topSkills = User::query()
            ->where('onboarding_completed', true)
            ->get()
            ->flatMap(fn (User $user) => $user->teach_skills ?? [])
            ->map(fn (string $skill) => trim($skill))
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(8);

        return view('home', compact('featuredUsers', 'topSkills'));
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->onboarding_completed) {
            return redirect()->route('onboarding');
        }

        $suggestedMatches = User::query()
            ->where('id', '!=', $user->id)
            ->where('onboarding_completed', true)
            ->whereDoesntHave('blockedByUsers', fn (Builder $query) => $query->where('users.id', $user->id))
            ->get()
            ->map(fn (User $candidate) => [
                'user' => $candidate,
                'score' => $this->matchService->score($user, $candidate),
            ])
            ->sortByDesc('score')
            ->take(4)
            ->values();

        $incomingRequests = SwapRequest::query()
            ->with('requester')
            ->where('receiver_id', $user->id)
            ->latest()
            ->get();

        $activeSwaps = Swap::query()
            ->with(['requester', 'receiver', 'sessions'])
            ->where(fn (Builder $query) => $query
                ->where('requester_id', $user->id)
                ->orWhere('receiver_id', $user->id))
            ->latest()
            ->get();

        $savedUsers = User::query()
            ->whereIn('id', $user->saved_users ?? [])
            ->get();

        $recommendedSkills = User::query()
            ->where('id', '!=', $user->id)
            ->where('onboarding_completed', true)
            ->get()
            ->flatMap(fn (User $candidate) => $candidate->teach_skills ?? [])
            ->reject(fn (string $skill) => in_array($skill, $user->teach_skills ?? [], true))
            ->countBy()
            ->sortDesc()
            ->take(6);

        $recentMessages = SwapMessage::query()
            ->with(['user', 'swap'])
            ->whereHas('swap', fn (Builder $query) => $query
                ->where('requester_id', $user->id)
                ->orWhere('receiver_id', $user->id))
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'suggestedMatches',
            'incomingRequests',
            'activeSwaps',
            'savedUsers',
            'recommendedSkills',
            'recentMessages',
        ));
    }

    public function onboarding(Request $request): View
    {
        return view('onboarding', ['user' => $request->user()]);
    }

    public function saveOnboarding(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'profile_photo_upload' => ['nullable', 'image', 'max:5120'],
            'bio' => ['required', 'string', 'max:1200'],
            'location' => ['nullable', 'string', 'max:255'],
            'timezone' => ['required', 'string', 'max:100'],
            'availability' => ['required', 'string', 'max:255'],
            'skill_level' => ['required', 'string', 'max:100'],
            'teach_skills' => ['required', 'string', 'max:1000'],
            'learn_skills' => ['required', 'string', 'max:1000'],
            'formats' => ['required', 'array', 'min:1'],
            'formats.*' => ['string', 'max:100'],
            'portfolio_links' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'profile_photo' => $this->storeProfilePhoto($request, $user),
            'bio' => $validated['bio'],
            'location' => $validated['location'] ?? null,
            'timezone' => $validated['timezone'],
            'availability' => $validated['availability'],
            'skill_level' => $validated['skill_level'],
            'teach_skills' => $this->parseList($validated['teach_skills']),
            'learn_skills' => $this->parseList($validated['learn_skills']),
            'formats' => $validated['formats'],
            'portfolio_links' => $this->parseList($validated['portfolio_links'] ?? ''),
            'onboarding_completed' => true,
        ]);

        return redirect()->route('dashboard')->with('status', 'Your profile is now matchable.');
    }

    public function browse(Request $request): View
    {
        /** @var User|null $viewer */
        $viewer = $request->user();

        $users = User::query()
            ->where('onboarding_completed', true)
            ->when($viewer, fn (Builder $query) => $query->where('id', '!=', $viewer->id))
            ->when($request->string('skill')->toString(), function (Builder $query, string $skill) {
                $query->where(function (Builder $inner) use ($skill) {
                    $inner->whereJsonContains('teach_skills', $skill)
                        ->orWhereJsonContains('learn_skills', $skill);
                });
            })
            ->when($request->string('level')->toString(), fn (Builder $query, string $level) => $query->where('skill_level', $level))
            ->when($request->string('availability')->toString(), fn (Builder $query, string $availability) => $query->where('availability', $availability))
            ->when($request->string('timezone')->toString(), fn (Builder $query, string $timezone) => $query->where('timezone', 'like', '%' . $timezone . '%'))
            ->get()
            ->filter(function (User $candidate) use ($request) {
                $format = $request->string('format')->toString();

                return $format === '' || in_array($format, $candidate->formats ?? [], true);
            })
            ->map(function (User $candidate) use ($viewer) {
                return [
                    'user' => $candidate,
                    'score' => $viewer ? $this->matchService->score($viewer, $candidate) : null,
                ];
            })
            ->sortByDesc(fn (array $item) => $item['score'] ?? 0)
            ->values();

        return view('browse', [
            'results' => $users,
            'filters' => $request->only(['skill', 'level', 'availability', 'timezone', 'format']),
        ]);
    }

    public function profile(Request $request, User $user): View
    {
        $user->load(['reviewsReceived.reviewer']);

        /** @var User|null $viewer */
        $viewer = $request->user();

        $score = $viewer ? $this->matchService->score($viewer, $user) : null;

        $existingSwap = $viewer
            ? Swap::query()
                ->where(fn (Builder $query) => $query
                    ->where('requester_id', $viewer->id)
                    ->where('receiver_id', $user->id))
                ->orWhere(fn (Builder $query) => $query
                    ->where('requester_id', $user->id)
                    ->where('receiver_id', $viewer->id))
                ->latest()
                ->first()
            : null;

        return view('profile', compact('user', 'viewer', 'score', 'existingSwap'));
    }

    public function toggleSave(Request $request, User $user): RedirectResponse
    {
        /** @var User $viewer */
        $viewer = $request->user();
        $saved = collect($viewer->saved_users ?? []);

        $viewer->update([
            'saved_users' => $saved->contains($user->id)
                ? $saved->reject(fn (int $id) => $id === $user->id)->values()->all()
                : $saved->push($user->id)->unique()->values()->all(),
        ]);

        return back()->with('status', 'Saved users updated.');
    }

    public function reportUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        UserReport::create([
            'reporter_id' => $request->user()->id,
            'reported_user_id' => $user->id,
            'reason' => $validated['reason'],
        ]);

        return back()->with('status', 'Thanks. The report has been logged.');
    }

    public function blockUser(Request $request, User $user): RedirectResponse
    {
        /** @var User $viewer */
        $viewer = $request->user();
        $viewer->blockedUsers()->syncWithoutDetaching([$user->id]);

        return redirect()->route('browse')->with('status', 'That user has been blocked from your experience.');
    }

    public function createRequest(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'skill_to_learn' => ['required', 'string', 'max:255'],
            'skill_to_offer' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
            'proposed_schedule' => ['required', 'string', 'max:255'],
            'preferred_format' => ['required', 'string', 'max:100'],
        ]);

        SwapRequest::create($validated + [
            'requester_id' => $request->user()->id,
            'receiver_id' => $user->id,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')->with('status', 'Swap request sent.');
    }

    public function respondToRequest(Request $request, SwapRequest $swapRequest): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($swapRequest->receiver_id === $user->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:accepted,declined,countered'],
            'counter_message' => ['nullable', 'string', 'max:1000'],
            'counter_schedule' => ['nullable', 'string', 'max:255'],
            'counter_format' => ['nullable', 'string', 'max:100'],
        ]);

        $swapRequest->update([
            'status' => $validated['status'],
            'counter_message' => $validated['counter_message'] ?? null,
            'counter_schedule' => $validated['counter_schedule'] ?? null,
            'counter_format' => $validated['counter_format'] ?? null,
        ]);

        if ($validated['status'] === 'accepted') {
            $swap = Swap::firstOrCreate(
                ['swap_request_id' => $swapRequest->id],
                [
                    'requester_id' => $swapRequest->requester_id,
                    'receiver_id' => $swapRequest->receiver_id,
                    'skill_to_learn' => $swapRequest->skill_to_learn,
                    'skill_to_offer' => $swapRequest->skill_to_offer,
                    'format' => $swapRequest->preferred_format,
                    'status' => 'active',
                    'progress_notes' => 'Kickoff confirmed. Decide your first learning milestone together.',
                    'progress_percent' => 10,
                ]
            );

            return redirect()->route('swaps.show', $swap)->with('status', 'Swap accepted and workspace opened.');
        }

        return back()->with('status', 'Request updated.');
    }

    public function showSwap(Request $request, Swap $swap): View
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless(in_array($user->id, [$swap->requester_id, $swap->receiver_id], true), 403);

        $swap->load([
            'requester',
            'receiver',
            'messages.user',
            'sessions',
            'reviews.reviewer',
        ]);

        $partner = $swap->requester_id === $user->id ? $swap->receiver : $swap->requester;

        return view('swap', compact('swap', 'partner', 'user'));
    }

    public function addMessage(Request $request, Swap $swap): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless(in_array($user->id, [$swap->requester_id, $swap->receiver_id], true), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $swap->messages()->create([
            'user_id' => $user->id,
            'body' => $validated['body'],
        ]);

        return back()->with('status', 'Message sent.');
    }

    public function addSession(Request $request, Swap $swap): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless(in_array($user->id, [$swap->requester_id, $swap->receiver_id], true), 403);

        $validated = $request->validate([
            'scheduled_for' => ['required', 'date'],
            'meeting_link' => ['nullable', 'url', 'max:500'],
            'topic' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:upcoming,completed,canceled'],
        ]);

        $swap->sessions()->create($validated);

        return back()->with('status', 'Session scheduled.');
    }

    public function updateProgress(Request $request, Swap $swap): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless(in_array($user->id, [$swap->requester_id, $swap->receiver_id], true), 403);

        $validated = $request->validate([
            'progress_notes' => ['required', 'string', 'max:2000'],
            'progress_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'in:active,completed'],
        ]);

        $swap->update($validated + [
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
        ]);

        return back()->with('status', 'Swap progress updated.');
    }

    public function addReview(Request $request, Swap $swap): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless(in_array($user->id, [$swap->requester_id, $swap->receiver_id], true), 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'feedback' => ['required', 'string', 'max:1000'],
        ]);

        $revieweeId = $swap->requester_id === $user->id ? $swap->receiver_id : $swap->requester_id;

        Review::updateOrCreate(
            [
                'swap_id' => $swap->id,
                'reviewer_id' => $user->id,
            ],
            $validated + ['reviewee_id' => $revieweeId]
        );

        return back()->with('status', 'Review saved.');
    }

    public function settings(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $user->load('reviewsReceived.reviewer');

        return view('settings', compact('user'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'bio' => ['nullable', 'string', 'max:1200'],
            'location' => ['nullable', 'string', 'max:255'],
            'timezone' => ['required', 'string', 'max:100'],
            'availability' => ['required', 'string', 'max:255'],
            'profile_photo_upload' => ['nullable', 'image', 'max:5120'],
            'teach_skills' => ['required', 'string', 'max:1000'],
            'learn_skills' => ['required', 'string', 'max:1000'],
            'portfolio_links' => ['nullable', 'string', 'max:1000'],
            'formats' => ['required', 'array', 'min:1'],
            'formats.*' => ['string', 'max:100'],
        ]);

        $user->update([
            'bio' => $validated['bio'] ?? null,
            'location' => $validated['location'] ?? null,
            'timezone' => $validated['timezone'],
            'availability' => $validated['availability'],
            'profile_photo' => $this->storeProfilePhoto($request, $user),
            'teach_skills' => $this->parseList($validated['teach_skills']),
            'learn_skills' => $this->parseList($validated['learn_skills']),
            'portfolio_links' => $this->parseList($validated['portfolio_links'] ?? ''),
            'formats' => $validated['formats'],
        ]);

        return back()->with('status', 'Settings updated.');
    }

    private function parseList(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function storeProfilePhoto(Request $request, User $user): ?string
    {
        if (! $request->hasFile('profile_photo_upload')) {
            return $user->profile_photo;
        }

        if (is_string($user->profile_photo) && $user->profile_photo !== '' && ! str_starts_with($user->profile_photo, 'http')) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        return $request->file('profile_photo_upload')->store('profile-photos', 'public');
    }
}

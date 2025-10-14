<?php

namespace App\Http\Controllers;

use App\Data\UserProfileData;
use App\Events\UserFollowEvent;
use App\Jobs\DeleteAccountJob;
use App\Services\SeoService;
use App\Services\SessionService;
use App\Services\SettingsService;
use App\Services\UserService;
use App\Settings\SecuritySettings;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Notifications\Auth\VerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class UserController extends Controller
{
    use WithRateLimiting;

    protected ?SecuritySettings $securitySettings;

    protected UserService $userService;

    protected SeoService $seoService;

    public function __construct()
    {
        $this->securitySettings = SettingsService::getSecuritySettings();
        $this->userService = app(UserService::class);
        $this->seoService = app(SeoService::class);
    }

    public function view(Request $request)
    {
        $username = $request->route('username');
        $userId = $this->userService->getId($username);
        $user = $this->userService->getData($userId);
        $profile = $this->userService->getProfileData($userId);
        $followers = $this->userService->getFollowersCount($userId);
        $following = $this->userService->getFollowingCount($userId);
        $isFollowing = $this->userService->checkFollowing(Auth::id(), $userId);

        return Inertia::render('User/Profile', [
            'user' => $user,
            'profile' => $profile,
            'followers' => $followers,
            'following' => $following,
            'isFollowing' => $isFollowing,
        ])->withViewData(['seo' => $this->seoService->getUserSEO($user, $profile)]);
    }

    public function preview(Request $request)
    {
        $username = $request->route('username');
        $userId = $this->userService->getId($username);
        $user = $this->userService->getData($userId);
        $profile = $this->userService->getProfileData($userId);
        $followers = $this->userService->getFollowersCount($userId);
        $following = $this->userService->getFollowingCount($userId);
        $isFollowing = $this->userService->checkFollowing(Auth::id(), $userId);

        return response()->json([
            'user' => $user,
            'profile' => $profile,
            'followers' => $followers,
            'following' => $following,
            'isFollowing' => $isFollowing,
        ]);
    }

    public function verificationView()
    {
        return Inertia::render('User/Verify');
    }

    public function verificationVerify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->route('user.verification.view');
    }

    public function verificationResend(Request $request)
    {
        try {
            $this->rateLimit($this->securitySettings->max_email_verification_attempts, $this->securitySettings->max_email_verification_attempts_seconds);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'email' => [__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => $exception->minutesUntilAvailable,
                ])],
            ]);
        }
        $user = $request->user();

        $notification = app(VerifyEmail::class);
        $notification->url = URL::temporarySignedRoute(
            'user.verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ],
        );

        $user->notify($notification);

        return response()->noContent(200);
    }

    public function settingsView(Request $request)
    {
        $user = $request->user();
        /** @var \App\Models\UserProfile $profileModel */
        $profileModel = $user->profile;
        $account = [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'avatar' => $user->getAvatarImage(),
        ];
        $profile = UserProfileData::fromModel($profileModel);
        $sessions = SessionService::getUserSessions();

        return Inertia::render('User/Settings', [
            'account' => $account,
            'profile' => $profile,
            'sessions' => $sessions,
        ]);
    }

    public function updateAccount(Request $request)
    {
        $request->validate([
            'avatar' => 'nullable|file|image|max:2048',
            'name' => 'required|string',
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
        }

        $user->name = $request->name;
        $user->save();

        return response()->noContent(200);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|string|max:255',
            'bio' => 'required|string',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'socials' => 'nullable|array',
            'socials.*' => 'nullable|string|max:255',
            'cover' => 'nullable|file|image|max:2048',
        ]);

        $user = $request->user();
        /** @var \App\Models\UserProfile $profile */
        $profile = $user->profile;

        if ($request->hasFile('cover')) {
            $profile->addMediaFromRequest('cover')->toMediaCollection('cover');
        }

        $profile->role = $validated['role'];
        $profile->bio = $validated['bio'] ?? '';
        $profile->location = $validated['location'] ?? '';
        $profile->website = $validated['website'] ?? '';
        $profile->socials = $validated['socials'] ?? [];
        $profile->save();

        return response()->noContent(200);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', Rules\Password::defaults()],
            'new_password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => [__('auth.password')],
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent(200);
    }

    public function logOutOtherSessions(Request $request)
    {
        if (! SessionService::logoutOtherSessions($request->password)) {
            throw ValidationException::withMessages([
                'password' => [__('auth.password')],
            ]);
        }

        return response()->noContent(200);
    }

    public function deleteAccount(Request $request)
    {
        $password = $request->password;
        if (! Hash::check($password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('auth.password')],
            ]);
        }

        DeleteAccountJob::dispatch(Auth::user());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent(200);
    }

    public function follow(Request $request)
    {
        $username = $request->route('username');
        $currentUser = Auth::user();

        if ($username == $currentUser->username) {
            throw ValidationException::withMessages([
                'general' => ['You cannot follow yourself.'],
            ]);
        }

        $executed = RateLimiter::attempt(
            key: "{$currentUser->username}:{$username}:follow",
            maxAttempts: 1,
            decaySeconds: 60 * 60 * 24,
            callback: function () use ($currentUser, $username) {
                $userId = $this->userService->getId($username);

                if (! $this->userService->deleteFollow($currentUser->id, $userId)) {
                    $this->userService->insertFollow($currentUser->id, $userId);
                    event(new UserFollowEvent($currentUser, $userId));
                }
            }
        );

        if (! $executed) {
            return response()->noContent(429);
        }

        return response()->noContent(200);
    }
}

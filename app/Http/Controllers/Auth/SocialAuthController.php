<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirectToGoogle(): SymfonyRedirectResponse|RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        return $this->handleProviderCallback('google');
    }

    private function handleProviderCallback(string $provider): RedirectResponse
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Unable to authenticate with '.Str::headline($provider).'. Please try again.']);
        }

        if (! $socialUser->getEmail()) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => Str::headline($provider).' did not provide an email address. Please use email login.']);
        }

        $user = $this->findOrCreateUser($provider, $socialUser);

        Auth::login($user, true);
        request()->session()->regenerate();

        if ($user->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if (request()->session()->has('selected_template_slug')) {
            return redirect()->route('invitations.create', ['template' => request()->session()->get('selected_template_slug')]);
        }

        return redirect()->route('home');
    }

    private function findOrCreateUser(string $provider, SocialiteUser $socialUser): User
    {
        $email = $socialUser->getEmail();

        $user = User::query()->where('email', $email)->first();

        if ($user) {
            $user->forceFill([
                'provider' => $user->provider ?? $provider,
                'provider_id' => $user->provider_id ?? $socialUser->getId(),
                'avatar' => $socialUser->getAvatar() ?? $user->avatar,
            ])->save();

            return $user;
        }

        return User::create([
            'name' => $socialUser->getName() ?: Str::before($email, '@'),
            'email' => $email,
            'password' => Hash::make(Str::random(48)),
            'is_admin' => false,
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
        ]);
    }
}

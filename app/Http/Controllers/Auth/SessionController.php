<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MagicLinkToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, false)) {
            return back()->withErrors(['email' => 'Those details do not match an account.']);
        }

        $request->session()->regenerate();

        return redirect()->intended($request->user()->isClient() ? '/portal' : '/admin');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function sendMagicLink(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        $user = User::query()->where('email', $data['email'])->first();

        if ($user && config('mail.default') !== 'log') {
            $plain = Str::random(48);
            MagicLinkToken::query()->create([
                'email' => $user->email,
                'token_hash' => Hash::make($plain),
                'expires_at' => now()->addMinutes(15),
            ]);
            $url = url('/magic/'.$plain.'?email='.urlencode($user->email));
            Mail::raw('Use this link within 15 minutes: '.$url, function ($message) use ($user) {
                $message->to($user->email)->subject('Your sign-in link');
            });
        }

        return back()->with('success', 'If an account exists for that email, a sign-in link has been sent.');
    }

    public function consumeMagicLink(Request $request, string $token): RedirectResponse
    {
        $email = (string) $request->query('email');
        $record = MagicLinkToken::query()
            ->where('email', $email)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record || ! Hash::check($token, $record->token_hash)) {
            return redirect()->route('login')->withErrors(['email' => 'That sign-in link is no longer valid.']);
        }

        $record->forceFill(['used_at' => now()])->save();
        $user = User::query()->where('email', $email)->firstOrFail();
        Auth::login($user);
        $request->session()->regenerate();

        return redirect($user->isClient() ? '/portal' : '/admin');
    }
}

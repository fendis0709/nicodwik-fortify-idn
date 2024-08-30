<?php

namespace Laravel\Fortify\Http\Controllers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\ViewException;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse;
use Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Laravel\Fortify\Events\RecoveryCodeReplaced;
use Laravel\Fortify\Events\TwoFactorAuthenticationFailed;
use Laravel\Fortify\Events\ValidTwoFactorAuthenticationCodeProvided;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;

class TwoFactorAuthenticatedSessionController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Show the two factor authentication challenge view.
     *
     * @param  \Laravel\Fortify\Http\Requests\TwoFactorLoginRequest  $request
     * @return \Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse
     */
    public function create(TwoFactorLoginRequest $request): TwoFactorChallengeViewResponse
    {
        if (! $request->hasChallengedUser()) {
            throw new HttpResponseException(redirect()->route('login'));
        }

        return app(TwoFactorChallengeViewResponse::class);
    }

    /**
     * Attempt to authenticate a new session using the two factor authentication code.
     *
     * @param  \Laravel\Fortify\Http\Requests\TwoFactorLoginRequest  $request
     * @return mixed
     */
    public function store(TwoFactorLoginRequest $request)
    {
        $recoveryCode = $request->input('recovery_code');
        $user = $request->challengedUser();

        if ($request->validRecoveryCode()) {
            
            $user->replaceRecoveryCode($recoveryCode);
            $user->sendEmailQRCode();

            event(new RecoveryCodeReplaced($user, $recoveryCode));
        } elseif (! $request->hasValidCode()) {
            event(new TwoFactorAuthenticationFailed($user));

            return app(FailedTwoFactorLoginResponse::class)->toResponse($request);
        }

        event(new ValidTwoFactorAuthenticationCodeProvided($user));

        $this->guard->login($user, $request->remember());

        $request->session()->regenerate();

        return app(TwoFactorLoginResponse::class);
    }

    public function register(TwoFactorLoginRequest $request)
    {
        if (! $request->hasChallengedUser()) {
            throw new HttpResponseException(redirect()->route('login'));
        }

        if (! config('fortify.view-paths.two-factor.register')) {
            return new ViewException('[two-factor.register] view path has not been set, please set in fortify.view-paths.two-factor.register');
        }

        return view(config('fortify.view-paths.two-factor.register'));
    }

    public function challenge(TwoFactorLoginRequest $request)
    {
        if (! $request->hasChallengedUser()) {
            throw new HttpResponseException(redirect()->route('login'));
        }

        $challengeType = $request->query('type', 'code');
        $user = $request->challengedUser();

        if (empty($user->two_factor_challenge_type)) {
            return redirect()
                ->route('two-factor.register')
                ->withErrors(config('fortify.messages.error.two-factor.register'));
        }

        if ($user->two_factor_challenge_type != $challengeType) {
            $user->forceFill([
                'two_factor_challenge_type' => $challengeType,
            ])->save();
        }

        if ($user->two_factor_challenge_type == $user::RECOVERYCODECHALLENGE) {
            if (! config('fortify.view-paths.two-factor.recovery-code')) {
                return new ViewException('[two-factor.recovery-code] view path has not been set, please set in fortify.view-paths.two-factor.recovery-code');
            }

            return view(config('fortify.view-paths.two-factor.recovery-code'));
        }

        if (! config('fortify.view-paths.two-factor.challenge')) {
            return new ViewException('[two-factor.challenge] view path has not been set, please set in fortify.view-paths.two-factor.challenge');
        }

        return view(config('fortify.view-paths.two-factor.challenge'));
    }

    public function verify(TwoFactorLoginRequest $request)
    {
        $user = $request->challengedUser();
        if ($user->twoFactorInactive()) {
            $user->forceFill([
                'two_factor_confirmed_at' => now(),
                'two_factor_challenge_type' => 'code',
            ])->save();
        }

        return redirect()
            ->route('two-factor.register')
            ->withSuccess(config('fortify.messages.success.two-factor.register'));
    }

    public function proceed(TwoFactorLoginRequest $request)
    {
        $user = $request->challengedUser();
        if ($user->twoFactorInactive()) {
            return redirect()
                ->route('two-factor.register')
                ->withErrors(config('fortify.messages.error.two-factor.register'));
        }

        return redirect()
            ->route('two-factor.login');
    }

    public function resendEmail(TwoFactorLoginRequest $request)
    {
        $user = $request->challengedUser();

        $isEmailSent = RateLimiter::attempt(
            'resend-email-two-factor:'.$user->getKeyName(),
            6,
            function () use ($user) {
                $user->sendEmailQRCode(true);
            },
        );

        if (! $isEmailSent) {
            $seconds = RateLimiter::availableIn('resend-email-two-factor:'.$user->getKeyName());

            return redirect()
                ->back()
                ->with('resendEmailTimer', $seconds)
                ->withErrors(config('fortify.messages.error.two-factor.resend-email'));
        }

        return redirect()
            ->back()
            ->with('resendEmailTimer', 10)
            ->withSuccess(config('fortify.messages.success.two-factor.resend-email'));
    }
}

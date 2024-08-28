<?php

namespace Laravel\Fortify;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Events\RecoveryCodeReplaced;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;

trait TwoFactorAuthenticatable
{
    const CODECHALLENGE = 'code';
    const RECOVERYCODECHALLENGE = 'recovery_code';

    /**
     * Determine user has active Two Factor Authentication.
     */
    public function twoFactorActive(): bool
    {
        return $this->two_factor_secret &&
            $this->two_factor_confirmed_at;
    }

    /**
     * Determine user has inactive Two Factor Authentication.
     */
    public function twoFactorInactive(): bool
    {
        return $this->two_factor_secret &&
            ! $this->two_factor_confirmed_at;
    }

    /**
     * Generate QR Code of the user's two factor authentication and upload to S3.
     *
     * @return void
     */
    public function generateQrCodeAndUpload(string $targetFolder, string $fileSystem = 's3')
    {
        $gd = new GDLibRenderer(200);
        $writer = new Writer($gd);

        $binaryFile = $writer->writeString($this->twoFactorQrCodeUrl());

        if (Storage::disk($fileSystem)->exists($targetFolder)) {
            return;
        }

        try {
            Storage::disk($fileSystem)
                ->put($targetFolder, $binaryFile);
        } catch (\Exception $e) {
            Log::error('ERROR: failed to upload Two Factor Auth QR Code, reason: '.$e->getMessage());
        }
    }

    /**
     * Send Two Factor Authentication QR Code to user email.
     *
     * @return void
     */
    public function sendEmailQRCode(bool $isResend = false)
    {
        $mail = config('fortify.mail.two-factor.qr-code');
        if (! $mail) {
            return new Exception('Mail class must be identified, please check config!');
        }
        
        try {
            app()->isLocal()
                ? Mail::to($this->email)->send(new $mail($this, $isResend))
                : Mail::to($this->email)->queue(new $mail($this, $isResend));
        } catch (\Exception $e) {
            Log::error('ERROR: failed to send email Two Factor Auth QR Code, reason: '.$e->getMessage());
        }
    }

    /**
     * Determine if two-factor authentication has been enabled.
     *
     * @return bool
     */
    public function hasEnabledTwoFactorAuthentication()
    {
        if (Fortify::confirmsTwoFactorAuthentication()) {
            return ! is_null($this->two_factor_secret) &&
                   ! is_null($this->two_factor_confirmed_at);
        }

        return ! is_null($this->two_factor_secret);
    }

    /**
     * Get the user's two factor authentication recovery codes.
     *
     * @return array
     */
    public function recoveryCodes(): array
    {
        return json_decode(decrypt($this->two_factor_recovery_codes));
    }

    /**
     * Replace the given recovery code with a new one in the user's stored codes.
     *
     * @param  string  $code
     * @return void
     */
    public function replaceRecoveryCode($usedCode): void
    {
        $recoveryCodes = collect($this->recoveryCodes())->transform(function ($item) use ($usedCode) {
            if ($item->code == $usedCode) {
                $item->used = true;
            }
            return $item;
        });

        // check if used key >= 3, then remove used key to keep data size in DB
        if ($recoveryCodes->where('used', true)->count() >= 3) {
            $recoveryCodes = $recoveryCodes->where('used', false)->values();
        }

        $this->forceFill([
            'two_factor_challenge_type' => 'code',
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes->push([
                'code' => RecoveryCode::generate(),
                'used' => false,
            ])->all())),
        ])->save();

        RecoveryCodeReplaced::dispatch($this, $usedCode);
    }

     /**
     * Check Recovery Code from input is valid.
     *
     * @param  Laravel\Fortify\Http\Requests\TwoFactorLoginRequest  $request
     * @param  string  $usedCode
     */
    public function checkRecoveryCodesIsValid(TwoFactorLoginRequest $request, $usedCode): bool
    {
        $data = collect($this->recoveryCodes())->where('code', $usedCode)->first();

        if ($data && $data->used) {
            $request->merge([
                'is_used' => true,
            ]);
        }

        return $data && ! $data->used;
    }

    /**
     * Get the QR code SVG of the user's two factor authentication QR code URL.
     *
     * @return string
     */
    public function twoFactorQrCodeSvg()
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd
            )
        ))->writeString($this->twoFactorQrCodeUrl());

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    /**
     * Get the two factor authentication QR code URL.
     *
     * @return string
     */
    public function twoFactorQrCodeUrl()
    {
        return app(TwoFactorAuthenticationProvider::class)->qrCodeUrl(
            config('app.name'),
            $this->{Fortify::username()},
            decrypt($this->two_factor_secret)
        );
    }
}

<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Permission;
use App\Models\Setting;

use App\Policies\UserPolicy;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\SettingPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Setting::class => SettingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        VerifyEmail::createUrlUsing(function (object $notifiable) {
            $backendUrl = env('BACKEND_URL', 'http://admin.test/verify').'/email/verify/';

            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => Crypt::encrypt($notifiable->getKey()),
                ]
            );

            return $backendUrl . 'verify_url=' . urlencode($verifyUrl);

        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address. ')
                ->line('Once you have verified your email address, try login with password `123456789`.')
                ->action('Verify Email Address', $url);
        });

        ResetPassword::createUrlUsing(function (User $user, string $token) {
            // if role is not contractor and organization
            $roles = $user->roles->pluck('name')->toArray();
            if (in_array('Contractor', $roles) || in_array('Organization', $roles)) {
                return env('FRONTEND_URL', env('APP_URL')).'/reset-password?token='.$token;
            }
            return env('BACKEND_URL', env('APP_URL')).'/reset-password?token='.$token;

        });
    }
}

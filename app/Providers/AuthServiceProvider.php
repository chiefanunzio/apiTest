<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // custom email verification
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $md5Email = md5($notifiable->email);
            $psswToken = env('PASSWORD_TOKEN');
            $url = env('APP_URL').'/api/email/verify/'.$notifiable->id .'/'.$md5Email.$psswToken;
            return (new MailMessage())
                ->subject('Verifica la tua email')
                ->line('Clicca sul bottone per verificare la tua email.')
                ->action('Verifica', $url);
        });
    }
}

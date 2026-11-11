<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
 
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Users with the "Super Admin" role bypass every permission check, including
        // permissions created after this role was seeded. Explicit permissions are still
        // synced onto the role (see RoleSeeder) so `$role->permissions` stays accurate for display.
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });



        Queue::after(function (JobProcessed $event) {
            // This runs after any job finishes successfully
            Log::info('Job finished successfully', [
                'connection' => $event->connectionName,
                'queue'      => $event->job->getQueue(),
                'job'        => $event->job->resolveName(),
            ]);
        });

        // You can also track failures
        Queue::failing(function (JobFailed $event) {
            Log::error('Job failed', [
                'job' => $event->job->resolveName(),
                'exception' => $event->exception->getMessage(),
            ]);
        });
        

        $this->usePublicStorageUrlOfCurrentHost();
        $this->expireIdleAccessTokens();
    }

    /**
     * Reject an API token that has not been used for `sanctum.idle_timeout` minutes.
     * Sanctum stamps last_used_at on every authenticated request (after this
     * check), so working keeps the login alive and inactivity ends it.
     */
    private function expireIdleAccessTokens(): void
    {
        Sanctum::authenticateAccessTokensUsing(function (PersonalAccessToken $accessToken, bool $isValid): bool {
            $lastActivity = $accessToken->last_used_at ?? $accessToken->created_at;

            return $isValid && $lastActivity->gt(now()->subMinutes(config('sanctum.idle_timeout')));
        });
    }

    /**
     * Build public file URLs (company logo, profile photos, attachments) from the
     * host that is serving the API rather than APP_URL, so they still resolve when
     * APP_URL points at a different domain (e.g. www vs. bare, or production in a local .env).
     */
    private function usePublicStorageUrlOfCurrentHost(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }
        config(['filesystems.disks.public.url' => url('storage')]);

    }
}

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Laravel\Sanctum\PersonalAccessToken;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Logins die after `sanctum.idle_timeout` idle minutes; clear tokens idle for over a day.
Schedule::call(function () {
    PersonalAccessToken::query()
        ->where(fn ($query) => $query
            ->where('last_used_at', '<', now()->subDay())
            ->orWhere(fn ($query) => $query->whereNull('last_used_at')->where('created_at', '<', now()->subDay())))
        ->delete();
})->name('prune-idle-access-tokens')->daily();

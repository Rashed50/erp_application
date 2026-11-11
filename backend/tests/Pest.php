<?php

use App\Models\AccountTransactionDetail;
use App\Models\ChartOfAccount;
use App\Models\User;
use Database\Seeders\ChartOfAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * Create a user with the "Super Admin" role, which bypasses every permission
 * check via the Gate::before hook in AppServiceProvider. Shared across
 * feature tests that need an authenticated, fully-privileged actor.
 */
function adminUser(): User
{
    $user = User::factory()->create();
    $user->assignRole(Role::findOrCreate('Super Admin', 'web'));

    return $user;
}

/**
 * Seed the predefined chart of accounts (idempotent) and return the Cash in
 * Hand account id, for tests that pay money from or into a cash account.
 */
function cashInHandAccountId(): int
{
    (new ChartOfAccountSeeder)->run();

    return ChartOfAccount::query()->where('account_number', '1010')->value('id');
}

/**
 * Debits minus credits posted to an account, from its journal lines.
 */
function ledgerBalance(string $accountNumber): float
{
    $lines = AccountTransactionDetail::query()
        ->whereHas('account', fn ($query) => $query->where('account_number', $accountNumber));

    return round((float) (clone $lines)->sum('debit') - (float) $lines->sum('credit'), 2);
}

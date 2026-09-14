<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ChangePassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:change-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->ask('Enter email');
        $password = $this->secret('Enter new password');
        $password_confirmation = $this->secret('Enter new password again');

        if ($password != $password_confirmation) {
            $this->error('Password confirmation does not match');
            return Command::FAILURE;
        }


        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error('User not found');
            return Command::FAILURE;
        }

        $user->password = bcrypt($password);
        $user->save();

        $this->info('Password changed successfully');
        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UserRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:role {identifier} {--demote}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change a user role to admin (default) or demote them to a normal user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $identifier = $this->argument('identifier');
        $isDemote = $this->option('demote');

        $user = User::where('username', $identifier)
                    ->orWhere('mobile_number', $identifier)
                    ->first();

        if (!$user && is_numeric($identifier)) {
            $user = User::find($identifier);
        }

        if (!$user) {
            $this->error("User with identifier '{$identifier}' not found.");
            return 1;
        }

        $newRole = $isDemote ? 0 : 1;
        $roleName = $isDemote ? 'user' : 'admin';

        if ($user->role == $newRole) {
            $this->warn("User '{$user->username}' is already a {$roleName}. No changes made.");
            return 0;
        }

        $user->role = $newRole;
        $user->save();

        $this->info("Successfully updated user '{$user->username}' to {$roleName}.");
        return 0;
    }
}

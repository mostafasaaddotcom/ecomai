<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdminToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:create-admin
                            {--name=Admin Token : The name of the token}
                            {--user= : The ID of the user to create token for (optional)}
                            {--expires= : Token expiration in days (default: never)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin API token with full access to all resources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tokenName = $this->option('name');
        $userId = $this->option('user');
        $expiresInDays = $this->option('expires');

        // Get or create the admin user
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
        } else {
            // Create or find a system admin user
            $user = User::firstOrCreate(
                ['email' => 'admin@system.local'],
                [
                    'name' => 'System Administrator',
                    'phone' => '0000000000',
                    'password' => Hash::make(Str::random(32)),
                    'credit' => 0,
                    'email_verified_at' => now(),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $this->info('Created system admin user: admin@system.local');
            }
        }

        // Calculate expiration
        $expiresAt = $expiresInDays ? now()->addDays((int) $expiresInDays) : null;

        // Create token with admin:* ability
        $token = $user->createToken(
            $tokenName,
            ['admin:*'],
            $expiresAt
        );

        $this->newLine();
        $this->info('Admin token created successfully!');
        $this->newLine();

        $this->table(
            ['Property', 'Value'],
            [
                ['Token Name', $tokenName],
                ['User ID', $user->id],
                ['User Email', $user->email],
                ['Abilities', 'admin:* (full access)'],
                ['Expires At', $expiresAt ? $expiresAt->toDateTimeString() : 'Never'],
            ]
        );

        $this->newLine();
        $this->warn('IMPORTANT: Save this token securely. It will not be shown again.');
        $this->line('Token: ' . $token->plainTextToken);
        $this->newLine();

        return 0;
    }
}

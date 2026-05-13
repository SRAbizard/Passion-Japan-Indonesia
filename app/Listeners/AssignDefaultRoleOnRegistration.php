<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

class AssignDefaultRoleOnRegistration
{
    public function handle(Registered $event): void
    {
        try {
            Log::info('AssignDefaultRoleOnRegistration FIRED', [
                'user_class'  => is_object($event->user) ? get_class($event->user) : gettype($event->user),
                'user_id'     => $event->user->getKey(),
                'user_email'  => $event->user->email ?? null,
            ]);

            if (! $event->user instanceof User) {
                Log::warning('AssignDefaultRoleOnRegistration: not an App\\Models\\User, skipping');
                return;
            }

            // Bust Spatie's permission cache before the read so we see the freshest state.
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $user = $event->user->fresh(['roles']) ?? $event->user;

            if ($user->roles->isEmpty()) {
                $user->assignRole('student');
                Log::info('AssignDefaultRoleOnRegistration: assigned student role', ['user_id' => $user->id]);
            } else {
                Log::info('AssignDefaultRoleOnRegistration: user already has roles, skipping', [
                    'user_id' => $user->id,
                    'roles'   => $user->roles->pluck('name')->all(),
                ]);
            }
        } catch (Throwable $e) {
            Log::error('AssignDefaultRoleOnRegistration FAILED', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile().':'.$e->getLine(),
            ]);
            throw $e;
        }
    }
}

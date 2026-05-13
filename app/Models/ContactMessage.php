<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone', 'subject', 'message', 'locale', 'ip_address', 'user_agent'])]
class ContactMessage extends Model
{
    protected function casts(): array
    {
        return [
            'read_at'    => 'datetime',
            'replied_at' => 'datetime',
        ];
    }

    public function markAsRead(): void
    {
        if (! $this->read_at) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }
}

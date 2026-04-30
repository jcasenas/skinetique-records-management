<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'employees';

    protected $primaryKey = 'id';         

    protected $fillable = [
        'username',
        'password',
        // other fields like name, role, etc.
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Important for custom username login
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    // Helper method used in your middleware
    public function isOwner(): bool
    {
        return $this->role === 'owner';   // or however you store the role
        // Alternative: return in_array($this->role, ['owner', 'super_admin']);
    }
}
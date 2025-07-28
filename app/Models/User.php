<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
     public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isReadOnly()
    {
        return $this->role === 'readonly';
    }

    public function canViewPasswords()
    {
        return $this->isSuperAdmin();
    }

    public function canModify()
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    // Server assignments
    public function assignedServers()
    {
        return $this->belongsToMany(Server::class, 'user_server_assignments');
    }

    // Portal assignments  
    public function assignedPortals()
    {
        return $this->belongsToMany(Portal::class, 'user_portal_assignments');
    }
}

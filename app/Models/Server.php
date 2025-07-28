<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
        'password',
        'type',
        'exposed'
    ];

    public function portals()
    {
        return $this->hasMany(Portal::class);
    }

    public function getActivePortalsCount()
    {
        return $this->portals()->where('status', 'up')->count();
    }

   
    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Crypt::decryptString($value),
            set: fn ($value) => Crypt::encryptString($value),
        );
    }
    public function assignedUsers()
{
    return $this->belongsToMany(User::class, 'user_server_assignments');
}

public function scopeForUser($query, User $user)
{
    if ($user->isSuperAdmin()) {
        return $query;
    }
    return $query->whereHas('assignedUsers', function($q) use ($user) {
        $q->where('user_id', $user->id);
    });
}
}

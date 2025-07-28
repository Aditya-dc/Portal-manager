<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Portal extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_id',
        'name',
        'url',
        'developed_by',
        'vapt',
        'backup',
        'status',
        'last_checked'
    ];

    protected $casts = [
        'vapt' => 'boolean',
        'backup' => 'boolean',
        'last_checked' => 'datetime'
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function getStatusBadgeClass()
    {
        return $this->status === 'up' ? 'badge-success' : 'badge-danger';
    }
    public function assignedUsers()
{
    return $this->belongsToMany(User::class, 'user_portal_assignments');
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

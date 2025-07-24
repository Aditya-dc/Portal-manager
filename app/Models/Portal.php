<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}

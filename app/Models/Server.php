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
        'name',
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
}

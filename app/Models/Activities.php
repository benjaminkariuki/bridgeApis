<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activities extends Model
{
    use HasFactory;
    protected $table = 'activities';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'name',
        'route',
        'iconOpened',
        'iconClosed',
    ];

    
    

    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'role_activities', 'role_id', 'activity_id')
            ->withPivot('permissions');
    }

   
}
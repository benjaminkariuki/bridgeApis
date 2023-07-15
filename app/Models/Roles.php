<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Roles extends Model
{
    use HasFactory;
    protected $table = 'roles';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        
    ];

    

    public function customUser()
    {
        return $this->hasMany(CustomUser::class);
    }

   

    public function activities()
    {
        return $this->belongsToMany(Activities::class, 'role_activities', 'role_id', 'activity_id')
            ->withPivot('permissions'); // Include the 'permissions' column from the pivot table
    }

    protected static function boot()
    {
        parent::boot();

        // Define a deleting event listener
        static::deleting(function ($role) {
            // Delete all related entries in the role_activities table
            $role->activities()->detach();
        });
    }

    
    
}
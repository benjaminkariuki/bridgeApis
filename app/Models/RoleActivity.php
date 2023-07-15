<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleActivity extends Model
{
    use HasFactory;

    protected $table = 'role_activities';
    public $timestamps = false;
  

    protected $fillable = [
        'role_id',
        'activity_id',
        'permissions',
    ];

    /**
     * The role associated with the role-activity relationship.
     */
    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    /**
     * The activity associated with the role-activity relationship.
     */
    public function activity()
    {
        return $this->belongsTo(Activities::class, 'activity_id');
    }

    
}
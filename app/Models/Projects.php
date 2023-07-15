<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    use HasFactory;

    protected $table = 'projects';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'title',
        'overview',
        'status',
        'clientname',
        'start_date',
        'end-date',
        
    ];

    
    public function phases()
{
    return $this->hasMany(Phases::class);
}
    
}
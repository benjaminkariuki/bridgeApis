<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phases extends Model
{
    use HasFactory;

    protected $table = 'phases';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'projectid',
        'name',
    ];

    public function project()
    {
        return $this->belongsTo(Projects::class, 'projectid');
    }
    


    
}
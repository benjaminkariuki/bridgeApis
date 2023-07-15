<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessAnalyst extends Model
{
    use HasFactory;

    protected $table = 'busanalyst';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'userId',
        'projectId',
    ];
}
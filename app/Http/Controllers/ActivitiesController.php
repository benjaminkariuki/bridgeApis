<?php

namespace App\Http\Controllers;
use App\Models\Activities;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ActivitiesController extends Controller
{
    
    //
    public function getActivities()
    {
        $activities = Activities::all();
        
        return response()->json([
            'activities' => $activities,
        ]);
    }
    
}
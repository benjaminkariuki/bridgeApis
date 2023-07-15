<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Projects;
use App\Imports\PhasesImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controller;

class ProjectsController extends Controller
{
    public function createProjects(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'overview' => 'required',
            'status' => 'required',
            'clientname' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'excel_file' => 'required|file|mimes:xlsx,csv,xls', // added validation for the file
        ]);

        // If validation fails, return an error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Begin Transaction
        DB::beginTransaction();
        
        try {
            $project = new Projects();
            $project->title = $request->title;
            $project->overview = $request->overview;
            $project->status = $request->status;
            $project->clientname = $request->clientname;
            $project->start_date = $request->start_date;
            $project->end_date = $request->end_date;
            $project->save();
            
            // Import Excel spreadsheet
            Excel::import(new PhasesImport($project->id), $request->file('excel_file'));
            
            // Commit Transaction
            DB::commit();

            return response()->json([
                'message' => 'Project and related phases created successfully',
                'project' => $project
            ], 201);

        } catch (\Exception $e) {
            // Rollback Transaction on Error
            DB::rollback();
            return response()->json(['error' => 'Error creating project: ' . $e->getMessage()], 500);
        }
    }
}
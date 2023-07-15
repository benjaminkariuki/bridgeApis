<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Roles;
use App\Models\CustomUser;


class RolesController extends Controller
{
    //
    public function createRoles(Request $request)
    {
        $validatedData = $request->validate([
            'roleName' => 'required|string|max:255|unique:roles,name',
            'activities' => 'required|array',
            'activities.*.id' => 'required|exists:activities,id',
        'activities.*.permissions' => 'nullable|array',
        'activities.*.permissions.*' => 'in:read,write',
        ]);
    
        try {
            $role = Roles::create([
                'name' => $validatedData['roleName'],
            ]);
    
            if ($role) {
                foreach ($validatedData['activities'] as $activity) {
                    $permissions = implode(',', $activity['permissions']);
                    $role->activities()->attach($activity['id'], ['permissions' => $permissions]);
                }
    
                return response()->json([
                    'message' => 'Role created successfully',
                    'role' => $role,
                ], 201);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'Role name already exists',
                ], 400);
            } else {
                return response()->json([
                    'message' => 'Failed to create role',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
    }

    public function updateRoles(Request $request, $id)
{

    $defaultRoleId = 22;
    
    if($id == $defaultRoleId) {
        return response()->json([
            'message' => 'Default role cannot be updated',
        ], 400); // 400 Bad Request, since this is a client error
    }
    
    $validatedData = $request->validate([
        'roleName' => 'required|string|max:255|unique:roles,name,'.$id,
        'activities' => 'required|array',
        'activities.*.id' => 'required|exists:activities,id',
        'activities.*.permissions' => 'nullable|array',
        'activities.*.permissions.*' => 'in:read,write',
    ]);

    try {
        $role = Roles::findOrFail($id);
        $role->name = $validatedData['roleName'];
        $role->save();

        $activityData = [];
        foreach ($validatedData['activities'] as $activity) {
            $permissions = implode(',', $activity['permissions']);
            $activityData[$activity['id']] = ['permissions' => $permissions];
        }
        $role->activities()->sync($activityData);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role,
        ], 200);
    } catch (\Illuminate\Database\QueryException $e) {
        if ($e->getCode() === '23000') {
            return response()->json([
                'message' => 'Role name already exists',
            ], 400);
        } else {
            return response()->json([
                'message' => 'Failed to update role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}



    public function getRoles()
    {
        $roles = Roles::with(['activities' => function ($query) {
            $query->select('id', 'name', 'route');
        }, 'activities.roles.pivot' => function ($query) {
            $query->select('role_id', 'activity_id', 'permissions');
        }])->get();
    
        return response()->json([
            'roles' => $roles,
        ]);
    }


    //get role based on id
    public function getRolesWithId($id)
{
    $roles = Roles::where('id', $id)
        ->with(['activities' => function ($query) {
            $query->select('id', 'name', 'route');
        }, 'activities.roles.pivot' => function ($query) {
            $query->select('role_id', 'activity_id', 'permissions');
        }])
        ->get();

    return response()->json([
        'roles' => $roles,
    ]);
}

    
    


public function deleteRole($id)
{

    $defaultRoleId = 22;
    
    if($id == $defaultRoleId) {
        return response()->json([
            'message' => 'Default role cannot be deleted',
        ], 400); // 400 Bad Request, since this is a client error
    }


    DB::beginTransaction();
    try {
        $role = Roles::findOrFail($id);
        //retrieve all users with this role
        $users = CustomUser::where('role_id', $id)->get();
        //default Role id
      

        $users->each(function ($user) {
            $user->role_id = 17;
            $user->save();
        });
 
        $role->delete();
        DB::commit();

        return response()->json([
            'message' => 'Role deleted successfully',
            
        ],200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        DB::rollback();
        return response()->json([
            'message' => 'Role not found',
        ], 404);
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'message' => 'Failed to delete role',
            'error' => $e->getMessage(),
        ], 500);
    }
}


}
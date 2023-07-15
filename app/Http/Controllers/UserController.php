<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App\Models\CustomUser;
use Illuminate\Database\QueryException;
use Exception;

class UserController extends Controller
{
    //
    public function getAllUsers()
{
    try {
        $users = CustomUser::with('role:id,name')->get([
            'firstName',
            'lastName',
            'email',
            'contacts',
            'role_id',
            'id',
            'profile_pic',
        ]);

        return response()->json([
            'users' => $users,
        ], 200);
    } catch (QueryException $e) {
        return response()->json([
            'message' => 'Failed to fetch users',
            'error' => $e->getMessage(),
        ], 500);
    } catch (Exception $e) {
        return response()->json([
            'message' => 'An error occurred',
            'error' => $e->getMessage(),
        ], 500);
    }
}


public function search(Request $request)
{
    $searchQuery = $request->input('query');

    // Perform the search query on your User model or database
    $users = CustomUser::where('firstName', 'LIKE', '%'.$searchQuery.'%')
        ->orWhere('lastName', 'LIKE', '%'.$searchQuery.'%')
        ->orWhere('email', 'LIKE', '%'.$searchQuery.'%')
           ->orWhere('role_id', 'LIKE', '%'.$searchQuery.'%')
        ->get();

    $formattedUsers = [];
    foreach ($users as $user) {
        $formattedUsers[] = [
            'id' => $user->id,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'email' => $user->email,
            'contacts' => $user->contacts,
            'role' => [
                'id' => $user->role->id,
                'name' => $user->role->name,
            ],
            'profile_pic' => $user->profile_pic,
        ];
    }

    return response()->json(['users' => $formattedUsers]);
}


    
    
}
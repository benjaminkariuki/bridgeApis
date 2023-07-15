<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\CustomUser;
use App\Models\Roles;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
class ManageUser extends Controller
{
    //
    public function updateUserDetails(Request $request, $id)
    {
        $user = CustomUser::findOrFail($id);
        
    
      // Validate the request inputs
$validator = Validator::make($request->all(), [
    'firstName' => 'nullable|string|max:255',
    'lastName' => 'nullable|string|max:255',
    'email' => 'nullable|email|unique:customusers,email,' . $user->id,
    'contacts' => 'nullable|string|max:255',
    'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
]);

        if ($validator->fails()) {
            // Return a response with validation errors
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        // Update the user details based on the provided inputs
        if ($request->has('firstName')) {
            $user->firstName = $request->input('firstName');
        }
        if ($request->has('lastName')) {
            $user->lastName = $request->input('lastName');
        }
        if ($request->has('email')) {
            $user->email = $request->input('email');
        }
        if ($request->has('contacts')) {
            $user->contacts = $request->input('contacts');
        }
        if ($request->hasFile('profile_pic')) {
            $profilePic = $request->file('profile_pic');
            $profilePicPath = $profilePic->store('profile_pics','public');
            $user->profile_pic = $profilePicPath;
        }

        // Save the updated user details
        $user->save();

          // Get the full URL for the profile picture. $user->profile_pic
    $profilePicUrl = asset('storage/' . $user->profile_pic);
        // Return a response indicating the success of the update

        // Fetch the user's role and its associated activities
        $role = Roles::find($user->role_id);

        if ($role) {
            // Fetch the role name
  $roleName = $role->name;
          $activities = $activities = $role->activities()->select('id', 'name', 'route','iconOpened', 'iconClosed')->get();

      } else {
          $activities = [];
      }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'email'  =>  $user->email,
                'contacts'=> $user->contacts,
                'role_id'  => $user->role_id,
                'token' => $user->token,
                'profile_pic'  => $profilePicUrl,
                'roleName'=> $roleName,
                'activities' => $activities,
                
            ]
        ]);
    }


    public function getDetails($id)
{
    $user = CustomUser::find($id);

    if (!$user) {
        return response()->json([
            'message' => 'User not found.',
        ], 404);
    }

    return response()->json([
        'message' => 'User details retrieved successfully.',
        'user' => $user,
    ]);
}





public function deleteUserImage(Request $request, $id)
{
    $user = CustomUser::findOrFail($id);

    // Check if the profile picture exists
    if ($user->profile_pic === null) {
        return response()->json([
            'message' => 'Profile picture does not exist',
        ]);
    }

    // Delete the profile picture from storage
  //  Storage::delete($user->profile_pic);
    Storage::disk('public')->delete($user->profile_pic);

    // Set the profile_pic column in the database back to null
    $user->profile_pic = null;

    // Save the updated user details
    $user->save();

    // Return a response indicating the success of the image deletion
    return response()->json([
        'message' => 'Profile picture deleted successfully',
    ]);
}





    

}
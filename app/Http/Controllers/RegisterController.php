<?php

namespace App\Http\Controllers;

use App\Models\CustomUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Mail\DeleteMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Roles;




class RegisterController extends Controller
{
    //
    public function register(Request $request)
{
   
     // Validate user input
     $validatedData = $request->validate([
        'firstName' => 'required|string',
        'lastName' => 'required|string',
        'email' => 'required|email|unique:customusers',
        'contacts' => 'required',
        'role_id' => 'required',
    ]);

    // Check if email already exists
    $existingUser = CustomUser::where('email', $validatedData['email'])->first();
    if ($existingUser) {
        return response()->json(['error' => 'Email already exists.'], 400);
    }
    

    // Generate a random password
    $password = Str::random(10);
    $sendPassword = $password;
 

    //generate unique token
    $token = $this->generateUniqueToken();

    // Create the user
    $user = new CustomUser;    
    $user->firstName = $validatedData['firstName'];
    $user->lastName = $validatedData['lastName'];
    $user->email = $validatedData['email'];
  //  $user->username = $validatedData['username'];
  $user->contacts = $validatedData['contacts'];
    $user->role_id = $validatedData['role_id'];
    $user->password = bcrypt($password);

    $user->token = $this->hashToken($token);
    
    $result = $user->save();
    if($result){

         // Send registration email
         
         Mail::to($user->email)->queue(new WelcomeMail($user->email,$password));



        return response()->json(['message' => 'User registered successfully.']);
    }
    // Send an email to the user with their password
    // (You can use Laravel's built-in Mail class or a third-party package)
    // If user creation failed
    return response()->json(['error' => 'User registration failed.'], 500);
   
}



private function hashToken($token)
    {
        return hash('sha256', $token);
    }


private function generateUniqueToken()
{
    $token = Str::random(60);

    // Check if the token already exists in the database
    $existingToken = CustomUser::where('token', $token)->exists();

    // If the token exists, generate a new one recursively
    if ($existingToken) {
        return $this->generateUniqueToken();
    }

    return $token;
}

public function login(Request $request)
{
    // Validate user input
    $validatedData = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $credentials = $request->only('email', 'password');

  

    if (Auth::attempt($credentials)) {
        // Authentication successful
        $user = Auth::user();

          // Hash the token for response
          $hashedToken = $this->hashToken($user->token);
     //     $profilePicUrl = asset('storage/' . $user->profile_pic);

           // Fetch the user's role and its associated activities
        $role = Roles::find($user->role_id);
        if ($role) {
              // Fetch the role name
    $roleName = $role->name;
            $activities = $activities = $role->activities()->select('id', 'name', 'route','iconOpened', 'iconClosed')->get();

        } else {
            $activities = [];
        }

        // Return user details as JSON response
        return response()->json([
            'message' => 'Login successful.',
            'user' => [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'email'  =>  $user->email,
               'contacts'  => $user->contacts,
                'role_id'  => $user->role_id,
                'token' => $hashedToken,
                'profile_pic'  => $user->profile_pic,
                'activities' => $activities,
                'roleName'=> $roleName,
            ]
        ]);
    }

    // Authentication failed
   // Check if the provided email exists in the database
   $userExists = CustomUser::where('email', $validatedData['email'])->exists();

   if ($userExists) {
       // Email is correct, so the password must be incorrect
       return response()->json(['message' => 'Incorrect password.'], 401);
   }

   // Email is incorrect
   return response()->json(['message' => 'Incorrect email.'], 401);
}

public function deleteUser($id)
{
    try {
       
        $user = CustomUser::findOrFail($id);
        $email = $user->email;
        $result =  $user->delete();

        if($result){

            // Send registration email
            Mail::to($email)->queue(new DeleteMail($email));
            return response()->json([
                'message' => 'User deleted successfully',
                
            ],200);
   
   
          
       }else{
        return response()->json(['error' => 'User delete failed.']);
       }
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'message' => 'User not found',
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to delete user',
            'error' => $e->getMessage(),
        ], 500);
    }
}


public function updateUser(Request $request, $id)
{
    try {
        $user = CustomUser::findOrFail($id);
        
        // Validate the role_id input
        $validatedData = $request->validate([
            'role_id' => 'required',
        ]);

        // Update the user's role
        $user->role_id = $validatedData['role_id'];
        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
        ], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'message' => 'User not found',
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to update user',
            'error' => $e->getMessage(),
        ], 500);
    }
}



}
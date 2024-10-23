<?php

namespace App\Http\Controllers\api\Authentication;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
class authController extends Controller{
    

    public function userLogin(Request $request){
        $validator =  Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required',
        ]);
        if ($validator->fails())
        return response()->json([
            'error' => 'Email is required',

        ], 404, ['Content-Type' => 'application/json']);
       // return $request->password;
        $userHasEmail = User::where('Email', $request->email)->first();
        if($userHasEmail){
            $hashedPassword = $userHasEmail->password;
            $receivedPassword = $request->password;
            
            // Verify the received password against the hashed password
            if (!Hash::check($receivedPassword, $hashedPassword)) {
                // Passwords match, create and return a token
                return response()->json(['error' => 'Invalid password'], 404,['Content-Type' => 'application/json']);

            } else {
                // Passwords do not match, return a 404 response with an invalid password error message
                $token = $userHasEmail->createToken($userHasEmail->Role ,$userHasEmail->Role == 'admin'?['Role:admin']:['Role:viewer'])->plainTextToken; // Assuming createToken is a function that generates a token
                return response()->json(['token' => $token], 200,['Content-Type' => 'application/json']);
            }
        } else {
            // Handle the case where the user does not exist
            return response()->json(['error' => 'User not found'], 404,['Content-Type' => 'application/json']);
        }
    }
    public function userRegistration(Request $request){
        $validator =  Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required',
        ]);
        if ($validator->fails())
        return response()->json([
            'error' => 'Email is required',

        ], 404, ['Content-Type' => 'application/json']);
        $checkifUserExists = User::where('Email', $request->email)->first();
        if($checkifUserExists){
            return response()->json(['error' => 'User already exists'], 404,['Content-Type' => 'application/json']);
        }else{
            $user = new User();
            $user->email = $request->email;
            $user->password = Hash::make(trim($request->password));
            $user->Role = 'viewer';
            $user->name = "Default";
            $user->save();
            //Get the user that was created
            $currentUser = User::where('Email', $request->email)->first();
            $token = $currentUser->createToken($currentUser->Role ,$currentUser->Role == 'admin'?['Role:admin']:['Role:viewer'])->plainTextToken; // Assuming createToken is a function that generates a token
            return response()->json(['token' => $token,'userRole'=>$currentUser->Role,'userID'=>$currentUser->id], 201,['Content-Type' => 'application/json']);
        }
    }
    public function doesuserExists(Request $request){
        $validator =  Validator::make($request->all(),[
            'email'=>'required|email'
        ]);
        if ($validator->fails())
            return response()->json([
                'error' => 'Email is required',

            ], 404, ['Content-Type' => 'application/json']);
            $user = User::where('Email', $request->email)->first();
            if($user){
                return response()->json( status:200, headers:['Content-Type' => 'application/json']);
            }else{
                return response()->json( status:204, headers:['Content-Type' => 'application/json']);
            }
    }
    public function verifyUser(Request $request){
        $userRole = $request->user()->Role;
        $userID =  $request->user()->id;
        return response()->json( ['user'=> $userRole,'userID'=>$userID],status:200, headers:['Content-Type' => 'application/json']);
    }
}

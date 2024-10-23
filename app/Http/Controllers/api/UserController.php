<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserDetailsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    public function index() {}
    public function getUserDetails(Request $request)
    {
        $user = $request->user();
        return response()->json(
            UserDetailsResource::make($user),
            200,
            ['Content-Type' => 'application/json']
        );
    }
    public function checkUserPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);
        if ($validator->fails())
            return response()->json([
                'error' => 'Password is required',
            ], 422, ['Content-Type' => 'application/json']);

        $user = $request->user();
        $receivedPassword = $request->password;
        $hashedPassword = $user->password;
        if (!$user || !$receivedPassword) {
            return response()->json(['message' => 'User or password missing'], 400, ['Content-Type' => 'application/json']);
        }

        // Check if the received password matches the stored hashed password
        if (Hash::check($receivedPassword, $hashedPassword)) {
            return response()->json([
                'message' => 'C'
            ], 200, ['Content-Type' => 'application/json']);
        }
        return response()->json([
            'message' => 'I',
        ], 200, ['Content-Type' => 'application/json']);
    }
    public function updateUser(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email'=> 'required|email',
            'password' => 'nullable'
        ]);
        if ($validator->fails())
            return response()->json([
                'error' => 'Error is required',
            ], 422, ['Content-Type' => 'application/json']);

            $userAuthData = $request->user();
            $user = User::find($userAuthData->id);
            $user->email = $request->email;
            $user->name = $request->name;
            if(isset($request->password)){   $user->password = Hash::make($request->password);}
            $user->Save();
            return response()->json([
                'User Updated'
            ], 200, ['Content-Type' => 'application/json']);
    }
    //Using DB:Table as an example, please switch to a model going forward
    public function banUser(Request $request, )
    {
    $validated = $request->validate([
        'userId' =>'required|exists:users,id',
        'ban_type' => 'required|in:permanent,temporary',
        'ban_reason' => 'nullable|string|max:255',
        'ban_days' => 'required_if:ban_type,temporary|nullable|integer|min:1', // Only for temporary bans
    ]);

    $user = User::find($request->userId);
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }
    $banData = [];
    if($request->ban_type == 'temporary'){
        $banData = [
            'user_id' => $user->id,
            'banned_by' => $request->user()->id,
            'ban_type' => $validated['ban_type'],
            'ban_reason' => $validated['ban_reason'],
            'ban_start' => now(),
            'ban_end' => now()->addDays($request->ban_days) ?? null,
        ];
    }else{
        $banData = [
        'user_id' => $user->id,
        'banned_by' => $request->user()->id,
        'ban_type' => $validated['ban_type'],
        'ban_reason' => $validated['ban_reason'],
        'ban_start' => now(),
        'ban_end' => null,
    ];
    }

    // Store the ban in the DB.
    DB::table('user_suspensions')->insert($banData);

    return response()->json(['message' => 'User banned successfully'],200, ['Content-Type' => 'application/json']);
}

public function unbanUser($userId)
{
    $user = User::find($userId);
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Update the user's suspension to mark them as unbanned.
    DB::table('user_suspensions')
        ->where('user_id', $userId)
        ->update(['ban_type' => 'unbanned', 'ban_end' => now()]);

    return response()->json(['message' => 'User unbanned successfully']);
}
}

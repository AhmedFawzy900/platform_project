<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class UserController extends Controller
{
     // Fetch all users (admin only)
     public function index()
     {
         $users = User::all();
         return response()->json($users);
     }
 
     // Fetch a specific user by ID
     public function show($id)
     {
         $user = User::findOrFail($id);
         return response()->json($user);
     }
 
     // Update user profile
     public function update(Request $request, $id)
     {
         $user = User::findOrFail($id);
 
         $validatedData = $request->validate([
             'name' => 'sometimes|string|max:255',
             'email' => 'sometimes|email|unique:users,email,' . $id,
             'phone' => 'sometimes|string|max:15',
         ]);
 
         if ($request->has('password')) {
             $validatedData['password'] = Hash::make($request->password);
         }
 
         $user->update($validatedData);
         return response()->json($user);
     }
 
     // soft Delete a user (admin only)
     public function destroy($id)
     {
         $user = User::findOrFail($id);
         $user->delete();
 
         return response()->json(['message' => 'User deleted successfully']);
     }

    //  fetch trash users (admin only)
    public function trash()
    {
        $users = User::onlyTrashed()->get();
        return response()->json($users);
    }
    // Restore a soft deleted user (admin only)
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return response()->json(['message' => 'User restored successfully']);
    }
    // force delete a user (admin only)
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();

        return response()->json(['message' => 'User permanently deleted']);
    }

    //  Register a new user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'session_token' => null,
            ]);
            return response()->json(['message' => 'User registered successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to register user'], 500);
        }
    }
    // login a user
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        if ($user->session_token) {
            return response()->json(['message' => 'User already logged in on another device'], 403);
        }

        // Generate a new session token
        $sessionToken = Str::random(60);
        $user->session_token = $sessionToken;
        $user->save();

        // Create a new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'session_token' => $sessionToken,
        ]);
    }

    // logout a user
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $request->user()->session_token = null;
        $request->user()->save();

        return response()->json(['message' => 'Logged out successfully']);
    }
}

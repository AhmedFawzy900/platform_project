<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
     // Fetch all users (admin only)
     public function index()
     {
         try {
             $users = User::all();
             return response()->json($users);
         } catch (\Exception $e) {
             return response()->json(['error' => 'Error fetching users'], 500);
         }
     }
 
     // Fetch a specific user by ID
     public function show($id)
     {
         try {
             $user = User::findOrFail($id);
             return response()->json($user);
         } catch (\Exception $e) {
             return response()->json(['error' => 'Error fetching user'], 404);
         }
     }
 
     // Update user profile
     public function update(Request $request, $id)
     {
         try {
             // Find the user by ID or fail if not found
             $user = User::findOrFail($id);
     
             // Start building validation rules
             $rules = [
                 'name' => 'sometimes|string|max:255',
                 'email' => 'sometimes|email',
                 'phone' => 'sometimes|string|max:15',
             ];
     
             // Apply unique validation rules only if the fields are different from the existing values
             if ($request->has('email') && $request->email !== $user->email) {
                 $rules['email'] = 'email|unique:users,email';
             }
     
             if ($request->has('phone') && $request->phone !== $user->phone) {
                 $rules['phone'] = 'string|max:15|unique:users,phone';
             }
     
             // Validate the request data with the constructed rules
             $validatedData = $request->validate($rules);
     
             // Hash the password if provided
             if ($request->has('password')) {
                 $validatedData['password'] = Hash::make($request->password);
             }
     
             // Update the user with validated data
             $user->update($validatedData);
     
             // Return the updated user data
             return response()->json($user);
     
         } catch (ValidationException $e) {
             // Handle validation errors
             return response()->json([
                 'error' => 'Validation failed',
                 'messages' => $e->errors()
             ], 422);
     
         } catch (\Exception $e) {
             // Handle other types of exceptions
             return response()->json(['error' => 'An unexpected error occurred', 'message' => $e->getMessage()], 500);
         }
     }
 
     // soft Delete a user (admin only)
     public function destroy($id)
     {
         try {
             $user = User::findOrFail($id);
             $user->delete();
 
             return response()->json(['message' => 'User deleted successfully']);
         } catch (\Exception $e) {
             return response()->json(['error' => 'Error deleting user'], 500);
         }
     }

    //  fetch trash users (admin only)
    public function trash()
    {
        try {
            $users = User::onlyTrashed()->get();
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching trashed users'], 500);
        }
    }
    // Restore a soft deleted user (admin only)
    public function restore($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();

            return response()->json(['message' => 'User restored successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error restoring user'], 500);
        }
    }
    // force delete a user (admin only)
    public function forceDelete($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->forceDelete();

            return response()->json(['message' => 'User permanently deleted']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error permanently deleting user'], 500);
        }
    }

    //  Register a new user
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:255|unique:users',
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
        try {
            $request->user()->currentAccessToken()->delete();
            $request->user()->session_token = null;
            $request->user()->save();

            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error logging out: ' . $e->getMessage()], 500);
        }
    }
}

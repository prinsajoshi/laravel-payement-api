<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    //
    public function login(Request $request)
{
    // Step 1: Validate the incoming request
    $this->validateRequest($request);

    // Step 2: Retrieve the user by username
    $user = $this->getUserByUsername($request->username);

    // Step 3: Check if user exists
    $this->checkUserExists($user);

    // Step 4: Generate and save the token
    $token = $this->generateAndSaveToken($user);

    // Step 5: Return success response with user details
    return $this->createSuccessResponse($user, $token);
}

private function validateRequest(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'password' => 'required',
    ]);
}

private function getUserByUsername($username)
{
    return Customer::where('username', $username)->first();
}

private function checkUserExists($user)
{
    if (!$user) {
        return response()->json([
            'success' => 'Error',
            'message' => 'User not found'
        ], 401)->send();
    }
}

private function generateAndSaveToken($user)
{
    $token = Hash::make(Str::random(16));
    $user->token = $token;
    $user->save();

    return $token;
}

private function createSuccessResponse($user, $token)
{
    return response()->json([
        'success' => 'Login successful',
        'user' => [
            'token' => $token,
            'username' => $user->username,
            'usertype' => $user->usertype,
            'email' => $user->email,
        ]
    ]);
}

}

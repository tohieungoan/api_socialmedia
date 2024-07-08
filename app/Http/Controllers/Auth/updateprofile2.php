<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class updateprofile2
{
    protected $database;
    protected $collection = 'users'; // Collection name in Firebase

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function apiupdateprofile2(Request $request): JsonResponse
    {
        // Validate inputs (you can add validation if needed)

        // Retrieve inputs
        $name = $request->input('name');
        $avatar = $request->input('avatar');
        $id = $request->input('id');

        try {
            // Query Firebase for user data
            $query = $this->database->getReference($this->collection . "/" . $id)->getValue();
            if ($query !== null) {
                // Assuming $query is an associative array, directly access its elements
                $user = $query;
                $user['name'] = $name;
                $user['avatar'] = $avatar;

                $this->database->getReference($this->collection . '/' . $id)
                    ->update($user);

                return response()->json(['message' => 'Profile updated successfully'], 200);
            } else {
                return response()->json(['message' => 'User not found'], 404);
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error updating profile: ' . $e->getMessage());

            return response()->json(['message' => 'Update profile failed'], 500);
        }
    }
}

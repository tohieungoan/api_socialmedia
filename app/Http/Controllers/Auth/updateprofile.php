<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class updateprofile
{
    protected $database;
    protected $collection;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
    }

    public function apiupdateprofile(Request $request): JsonResponse
    {
        $name = $request->input('name');
        $avatar = $request->input('avatar');
        $birthday = $request->input('birthday');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $query = $this->database->getReference($this->collection)->orderByChild('email')->equalTo($email)->getValue();

        if ($query) {
            $user = current($query);
            $user['name'] = $name;
            $user['avatar'] = $avatar;
            $user['birthday'] = $birthday;
            $user['phone'] = $phone;
            // Save the updated user data back to the database
            $this->database->getReference($this->collection . '/' . key($query))
                ->update($user);
                
            return response()->json(['message' => 'Profile updated successfully'], 200);
        }
        else {
            return response()->json(['message' => 'Update profile failed'], 404);


         }
      
    }
}
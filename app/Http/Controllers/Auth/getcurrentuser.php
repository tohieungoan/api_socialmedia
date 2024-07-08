<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class getcurrentuser
{
    protected $database;
    protected $collection;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
    }

    public function apigetcurrentuser(Request $request): JsonResponse
    {
        $email = $request->input('email');

        $query = $this->database->getReference($this->collection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();

        if ($query) {
            $user = current($query);
            $name = $user['name'];
            $imgUrl = $user['avatar'];
            $userIds = array_keys($query);
            $userId = $userIds[0];

            $data = ['name' => $name, 'imageUrl' => $imgUrl ,'id' => $userId];
            return response()->json(['message' => 'get success', 'data' => $data], 200);
        } 
      
        else {
            $data = ['name' => "null", 'imageUrl' => "null" ,'id' => "khong co"];
            return response()->json(['message' => 'User not found','data' =>$data], 404);
        }
    }
}
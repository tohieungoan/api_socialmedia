<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
class logout
{
    protected $database;
    protected $collection;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
    }
    public function apilogout(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $query = $this->database->getReference($this->collection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();


    
        if ($query) {
            $user = current($query);
            $user['Loginsection'] = false;
        
            $this->database->getReference($this->collection . '/' . key($query))
                ->update($user);
                return response()->json(['message' => 'Logout success'], 200);
        }
        else {
            return response()->json(['message' => 'Logout fail'], 404);
        }
    }
    
}

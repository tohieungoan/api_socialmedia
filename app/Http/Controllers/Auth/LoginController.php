<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
class LoginController
{
    protected $database;
    protected $collection;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
    }
    public function apiLogin(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');
    
        $query = $this->database->getReference($this->collection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();
    
        if ($query) {
            $user = current($query);
            $storedPassword = $user['password'];
            $phone = $user['phone'];
            $birthday = $user['birthday'];
            
            if (password_verify($password, $storedPassword)) {
                if($phone=="." || $birthday=="."){
                    return response()->json(['message' => 'login success in first time'], 201);
                }
                else
                {
                    $user['Loginsection'] = true;
                    $this->database->getReference($this->collection . '/' . key($query))
                        ->update($user);
                    return response()->json(['message' => 'Login success'], 200);

                }
            } else {
                return response()->json(['message' => 'Login failed , password wrong'], 400);
            }
        }
    
        return response()->json(['message' => 'Email not in Database'], 401);
    }
}
<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
class getlogingsection 
{
    protected $database;
    protected $collection;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->collection = 'users';
    }
    public function apigetloging(Request $request): JsonResponse
    {
        $email = $request->input('email');
    
        $query = $this->database->getReference($this->collection)
            ->orderByChild('email')
            ->equalTo($email)
            ->getValue();
    
        if ($query) {
            $user = current($query);
            $loginsection = $user['Loginsection'];
      if($loginsection==true){
        return response()->json(['message' => 'you can continue use app '], 200);
      }
      else {
        return response()->json(['message' => 'you need loging again'], 404);
      }

            
      
        }
        else {
            return response()->json(['message' => 'not have email in db'], 400);
        }
    
    }
}